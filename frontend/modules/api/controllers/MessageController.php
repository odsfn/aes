<?php
/**
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class MessageController extends RestController {
    
    public $nestedModels = false;
    
    protected $convertRestFilters = true;
    
    public $acceptableFilters = array(
        'plain' => 'conversation_id',
        'model' => 'conversation_id'
    );
    
    protected function getOutputFormatters() {
        
        $tsFormatter = function($value) {
            
            if(strstr($value,'0000-00-00'))
                return 0;
            
            return (int)strtotime($value) * 1000;
        };
        
        return array(
            'created_ts' => $tsFormatter
        );
        
    }
    
    public function doRestList() {
        
        $userId = Yii::app()->user->id;
        
        $conversationId = $this->plainFilter['conversation_id'];
        
        $participant = ConversationParticipant::model()->findByAttributes(array(
            'conversation_id' => $conversationId,
            'user_id'   => $userId
        ));
        
        if(!$participant)
            throw new CHttpException(403, 'Specified user is not participant of specified conversation.');
        
        $criteria = $this->getModel()
                ->with($this->nestedRelations)
                ->filter($this->restFilter)
                ->orderBy('created_ts', 'DESC');
        
        $this->outputHelper( 
            'Records Retrieved Successfully',
            $criteria
                ->limit($this->restLimit)
                ->offset($this->restOffset)
                ->findAll(),
            $this->getModel()
                    ->with($this->nestedRelations)
                    ->filter($this->restFilter)
            ->count()
        );
    }
    
}
