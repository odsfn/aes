 <?php
/**
 * This is the base class for a Rate model
 *
 * The followings are the available attributes
 * @property integer $id
 * @property integer $user_id
 * @property integer $target_id
 * @property string $created_ts
 * @property integer $score
 *
 * The followings are the available model relations:
 * @property Rateable $target
 * @property UserProfile $user
 */
class Rate extends CActiveRecord
{
    const SCORE_POSITIVE = 1;
    const SCORE_NEGATIVE = -1;
    
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
     * Should specify certain Rateable entity, like Election, Page, Order or other
     * models that can be rated.
     * 
     * Such model should have id attribute
     * 
     * @return string
     */
    public function getRateableEntity() {
        return '';
    }
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ElectionCommentRate the static model class
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
        return lcfirst($this->rateableEntity) . '_rate';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, target_id, score', 'required'),
            array('user_id, target_id, score', 'numerical', 'integerOnly'=>true),
            array('created_ts', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, target_id, created_ts, score', 'safe', 'on'=>'search'),
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
            'target' => array(self::BELONGS_TO, $this->rateableEntity, 'target_id'),
            'profile' => array(self::BELONGS_TO, 'Profile', 'user_id'),
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
            'target_id' => 'Target',
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
        $criteria->compare('target_id',$this->target_id);
        $criteria->compare('created_ts',$this->created_ts,true);
        $criteria->compare('score',$this->score);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    protected function beforeSave() {
        if($this->isNewRecord) {

            $lastRate = $this->model()->find('user_id = ' . $this->user_id . ' AND target_id = ' . $this->target_id);
            if($lastRate) 
                $lastRate->delete();
        }
        
        return parent::beforeSave();
    }    
}