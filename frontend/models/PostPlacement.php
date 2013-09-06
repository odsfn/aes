 <?php

/**
 * This is the model class for table "post_placement".
 *
 * The followings are the available columns in table 'post_placement':
 * @property integer $id
 * @property integer $post_id
 * @property string $target_id
 * @property integer $target_type
 * @property string $placed_ts
 * @property integer $placer_id
 *
 * The followings are the available model relations:
 * @property UserProfile $placer
 * @property Post $post
 */
class PostPlacement extends CActiveRecord
{
    const TYPE_USER_PAGE = 0;
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return PostPlacement the static model class
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
        return 'post_placement';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('post_id, target_id', 'required'),
            array('post_id, target_type, placer_id', 'numerical', 'integerOnly'=>true),
            array('target_id', 'length', 'max'=>11),
            array('placed_ts', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, post_id, target_id, target_type, placed_ts, placer_id', 'safe', 'on'=>'search'),
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
            'placer' => array(self::BELONGS_TO, 'UserProfile', 'placer_id'),
            'post' => array(self::BELONGS_TO, 'Post', 'post_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'post_id' => 'Post',
            'target_id' => 'Target',
            'target_type' => 'Target Type',
            'placed_ts' => 'Placed Ts',
            'placer_id' => 'Placer',
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
        $criteria->compare('post_id',$this->post_id);
        $criteria->compare('target_id',$this->target_id,true);
        $criteria->compare('target_type',$this->target_type);
        $criteria->compare('placed_ts',$this->placed_ts,true);
        $criteria->compare('placer_id',$this->placer_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    public function postsOnUsersPage($userId) {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 't.target_id = '. intval($userId) . ' AND t.target_type = ' . PostPlacement::TYPE_USER_PAGE
        ));
        
        return $this;
    }
    
    public function usersOnly($userId) {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 't.placer_id = ' . (int)$userId
        ));
    }
}