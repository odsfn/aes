<?php

/** 
 * Base Model for comment 
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 *
 * The followings are the available columns in table 'election_comment':
 * @property integer $id
 * @property integer $target_id
 * @property integer $user_id
 * @property string $content
 * @property string $created_ts
 * @property string $last_update_ts
 *
 * The followings are the available model relations:
 * @property Commentable $target
 * @property Profile $user
 * @property CommentRate[] $rates
 */
class Comment extends CActiveRecord
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
     * Should specify certain commentable entity, like Election, Page, Order or other
     * models that can be commented.
     * 
     * Such model should have id attribute
     * 
     * @return string
     */
    public function getCommentableEntity() {
        return '';
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return lcfirst($this->commentableEntity) . '_comment';
    }    
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ElectionComment the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('target_id, user_id, content', 'required'),
            array('target_id, user_id', 'numerical', 'integerOnly'=>true),
            array('created_ts, last_update_ts', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, target_id, user_id, content, created_ts, last_update_ts', 'safe', 'on'=>'search'),
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
            'target' => array(self::BELONGS_TO, $this->getCommentableEntity(), 'target_id'),
            'user' => array(self::BELONGS_TO, 'Profile', 'user_id'),
            'rates' => array(self::HAS_MANY, $this->commentableEntity . 'CommentRate', 'target_id'),
            'positiveRatesCount' => array(
                self::STAT, $this->commentableEntity . 'CommentRate', 'target_id',
                'condition' => 'score = 1'
            ),
            'negativeRatesCount' => array(
                self::STAT, $this->commentableEntity . 'CommentRate', 'target_id',
                'condition' => 'score = -1'
            )
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'target_id' => 'Target',
            'user_id' => 'User',
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
        $criteria->compare('target_id',$this->target_id);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('content',$this->content,true);
        $criteria->compare('created_ts',$this->created_ts,true);
        $criteria->compare('last_update_ts',$this->last_update_ts,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    public function criteriaToTarget($targetId) {
        return $this->getDbCriteria()->mergeWith(array('condition' => 't.target_id = ' . (int) $targetId));
    }
}
