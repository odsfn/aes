<?php

/**
 * This is the model class for table "conversation".
 *
 * The followings are the available columns in table 'conversation':
 * @property integer $id
 * @property string $title
 * @property string $created_ts
 * @property integer $initiator_id
 *
 * The followings are the available model relations:
 * @property Profile $initiator
 * @property ConversationParticipant[] $participants
 * @property Message[] $messages
 */
class Conversation extends CActiveRecord
{
    public function behaviors() {
        return array(
            'UpdateDateBehavior' => array(
                'class' => 'UpdateDateBehavior',
                'fields' => array(
                    'create'=> array('created_ts')
                )
            )
        );
    }
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Conversation the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'conversation';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('initiator_id', 'required'),
            array('initiator_id', 'numerical', 'integerOnly'=>true),
            array('title', 'length', 'max'=>256),
            array('created_ts', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, title, created_ts, initiator_id', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'initiator' => array(self::BELONGS_TO, 'Profile', 'initiator_id'),
            'participants' => array(self::HAS_MANY, 'ConversationParticipant', 'conversation_id'),
            'messages' => array(self::HAS_MANY, 'Message', 'conversation_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => 'Title',
            'created_ts' => 'Created Ts',
            'initiator_id' => 'Initiator',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('title',$this->title,true);
        $criteria->compare('created_ts',$this->created_ts,true);
        $criteria->compare('initiator_id',$this->initiator_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    public function criteriaWithParticipants($participants) {
        
        if(!is_array($participants)) {
            $participants = array($participants);
        }
        
        foreach ($participants as $index => $participantId) {
            $this->getDbCriteria()->mergeWith(array(
                'join' => 'INNER JOIN conversation_participant cp' . $index . ' ON cp' . $index . '.user_id = '  . (int)$participantId . ' AND t.id = cp' . $index . '.conversation_id '
            ));
        }
        
        return $this;
    } 
    
//    public function scopes() {
//        return array(
//            'criteriaHasMessages' => array(
//                'join' => 'INNER JOIN message m ON t.id = m.conversation_id'
//            )
//        );
//    }
}