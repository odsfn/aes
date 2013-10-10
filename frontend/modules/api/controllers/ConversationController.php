<?php
/**
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ConversationController extends RestController {
    
    public $acceptFilters = array(
        'plain' => 'participants,since'
    );
    
    public $nestedModels = array(        
        'participants.user' => array(
            'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64'
        )
    );
        
    public $virtualAttrs = array(
            'messages',
            'participants'
    );
    
    protected function getOutputFormatters() {
        
        $tsFormatter = function($value) {
            
            if(strstr($value,'0000-00-00'))
                return 0;
            
            return (int)strtotime($value) * 1000;
        };
        
        return array(
            'created_ts' => $tsFormatter,
            'messages.created_ts' => $tsFormatter,
            'participants.last_view_ts' => $tsFormatter
        );
        
    }

    public function doRestCreate($data) {
        $data['initiator_id'] = Yii::app()->user->id;
        
        $participants = $data['participants'];
        unset($data['participants']);
        
        $models = $this->saveModel($this->getModel(), $data);
        
        $conversation = $models[0];
        
        foreach ($participants as $participantAttrs) {
            $participant = new ConversationParticipant;
            $participant->user_id = $participantAttrs['user_id'];
            $participant->conversation_id = $conversation->id;
            $participant->save();
        }
        
        $conversation->participants = $conversation->participants(array(
            'with' => array(
                'user' => array(
                    'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64'
                )
             ))
        );
        $conversation->messages = $conversation->messages(array(
            'order'=>'created_ts DESC',
            'limit'=>1
        ));
        
        $models[0] = $conversation;
        
        $this->outputHelper(
            'Record(s) Created',
            $models,
            1
        );
    }
    
    public function doRestList() {
        
        $userId = Yii::app()->user->id;
        
        if(!empty($this->plainFilter['participants'])) {
            $participants = $this->plainFilter['participants'];
            if(!in_array($userId, $participants))
                throw new CHttpException(403, 'You are able to see conversations where you are participating only.');
        } else
            $participants = array($userId);
            
        $criteria = $this->getModel()
                ->criteriaWithParticipants($participants)
                ->with($this->nestedRelations);

        $conversation = new Conversation;
        $this->_attachBehaviors($conversation);
        $countCriteria = $conversation
                ->criteriaWithParticipants($participants)
                ->with($this->nestedRelations);
        
        if(empty($this->plainFilter['participants'])) {
            
            $since = null;
            
            if(!empty($this->plainFilter['since']))
                $since = date('Y-m-d H:i:s', floor(((int)$this->plainFilter['since']) / 1000));
            
            $criteria->criteriaHasMessages($since);
            $countCriteria->criteriaHasMessages($since);
        }
        
        $conversations = $criteria->orderBy('created_ts', 'DESC')
                ->limit($this->restLimit)
                ->offset($this->restOffset)
                ->findAll(array('distinct'=>true));
        
        foreach ($conversations as $conversation) {
            $params = array(
                'order'=>'created_ts DESC',
                'limit'=>1
            );
            
            if($since) {
                $params['condition'] = 'created_ts > "' . $since . '"';
                unset($params['limit']);
            }
            
            $conversation->messages = $conversation->messages($params);
        }
        
        $this->outputHelper( 
            'Records Retrieved Successfully',
            $conversations,
            $countCriteria->count()
        );
    }
    
}
