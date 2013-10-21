<?php

/**
 * This is the model class for table "post".
 *
 * The followings are the available columns in table 'post':
 * @property integer $id
 * @property integer $user_id
 * @property integer $reply_to
 * @property string $content
 * @property string $created_ts
 * @property string $last_update_ts
 *
 * The followings are the available model relations:
 * @property Post $replyTo
 * @property Post[] $posts
 * @property UserProfile $user
 * @property PostRate[] $rates
 * @property PostPlacement[] $placements
 */
class Post extends CActiveRecord
{
    public function behaviors() {
        return array(
            'UpdateDateBehavior' => array(
                'class' => 'UpdateDateBehavior',
                'fields' => array(
                    'create'=> array('created_ts'),
                    'update'=> array('last_update_ts')
                )
            )
        );
    }
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Post the static model class
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
        return 'post';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, content', 'required'),
            array('user_id, reply_to', 'numerical', 'integerOnly'=>true),
            array('created_ts, last_update_ts', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, reply_to, content, created_ts, last_update_ts', 'safe', 'on'=>'search'),
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
            'replyTo' => array(self::BELONGS_TO, 'Post', 'reply_to'),
            'comments' => array(self::HAS_MANY, 'Post', 'reply_to'),
            'user' => array(self::BELONGS_TO, 'Profile', 'user_id'),
            'rates' => array(self::HAS_MANY, 'PostRate', 'post_id'),
            'placements' => array(self::HAS_MANY, 'PostPlacement', 'post_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'reply_to' => 'Reply To',
            'content' => 'Content',
            'created_ts' => 'Created Ts',
            'last_update_ts' => 'Last Update Ts',
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
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('reply_to',$this->reply_to);
        $criteria->compare('content',$this->content,true);
        $criteria->compare('created_ts',$this->created_ts,true);
        $criteria->compare('last_update_ts',$this->last_update_ts,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    // @TODO: replace it. Add corresponding formatter to the Rest controller
    public function getAttributes($names = true) {
        $result = parent::getAttributes($names);
        $result['displayTime'] = $this->displayTime;
        $result['createdTs'] = $this->createdTs;
        return $result;
    }
    
    // @TODO: replace it. Add corresponding formatter to the Rest controller
    public $displayTime;
    
    // @TODO: replace it. Add corresponding formatter to the Rest controller
    public $createdTs;
    
    // @TODO: replace it. Add corresponding formatter to the Rest controller
    protected function afterFind() {
        $this->displayTime = Yii::app()->dateFormatter->formatDateTime($this->created_ts, 'medium', 'medium');
        $this->createdTs = strtotime($this->created_ts);
        return parent::afterFind();
    }
    
    public function onUsersPage($userId) {
        $this->getDbCriteria()->mergeWith(array(
            'join' => 'INNER JOIN post_placement ON post_placement.post_id = t.id AND post_placement.target_id = '. intval($userId) . ' AND post_placement.target_type = ' . PostPlacement::TYPE_USER_PAGE
        ));
        
        return $this;
    }
    
    public function usersOnly($userId) {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 't.user_id = ' . (int)$userId
        ));
    }
    
    public function scopes() {
        return array(
            'postOnly' => array(
                'condition' => 't.reply_to IS NULL' 
            )
        );
    }
}