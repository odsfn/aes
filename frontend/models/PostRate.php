<?php

/**
 * This is the model class for table "post_rate".
 *
 * The followings are the available columns in table 'post_rate':
 * @property integer $id
 * @property integer $user_id
 * @property integer $post_id
 * @property string $created_ts
 * @property integer $score
 *
 * The followings are the available model relations:
 * @property Post $post
 * @property UserProfile $user
 */
class PostRate extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return PostRate the static model class
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
        return 'post_rate';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, post_id, score', 'required'),
            array('user_id, post_id, score', 'numerical', 'integerOnly'=>true),
            array('created_ts', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, post_id, created_ts, score', 'safe', 'on'=>'search'),
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
            'post' => array(self::BELONGS_TO, 'Post', 'post_id'),
            'user' => array(self::BELONGS_TO, 'UserProfile', 'user_id'),
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
            'post_id' => 'Post',
            'created_ts' => 'Created Ts',
            'score' => 'Score',
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
        $criteria->compare('post_id',$this->post_id);
        $criteria->compare('created_ts',$this->created_ts,true);
        $criteria->compare('score',$this->score);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}