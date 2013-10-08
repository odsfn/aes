<?php

/**
 * This is the model class for table "conversation_participant".
 *
 * The followings are the available columns in table 'conversation_participant':
 * @property integer $id
 * @property integer $conversation_id
 * @property integer $user_id
 * @property string $last_view_ts
 *
 * The followings are the available model relations:
 * @property Profile $profile
 * @property Conversation $conversation
 */
class ConversationParticipant extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ConversationParticipant the static model class
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
        return 'conversation_participant';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('conversation_id, user_id', 'required'),
            array('conversation_id, user_id', 'numerical', 'integerOnly'=>true),
            array('last_view_ts', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, conversation_id, user_id, last_view_ts', 'safe', 'on'=>'search'),
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
            'user' => array(self::BELONGS_TO, 'Profile', 'user_id'),
            'conversation' => array(self::BELONGS_TO, 'Conversation', 'conversation_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'conversation_id' => 'Conversation',
            'user_id' => 'User',
            'last_view_ts' => 'Last View Ts',
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
        $criteria->compare('conversation_id',$this->conversation_id);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('last_view_ts',$this->last_view_ts,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}