 <?php

/**
 * This is the model class for table "target".
 *
 * The followings are the available columns in table 'target':
 * @property integer $target_id
 * @property string $target_type related model's class name 
 */
class Target extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Target the static model class
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
        return 'target';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('target_type', 'required'),
            array('target_type', 'length', 'max'=>64),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('target_id, target_type', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'target_id' => 'Target',
            'target_type' => 'Target Type',
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

        $criteria->compare('target_id',$this->target_id);
        $criteria->compare('target_type',$this->target_type,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    public function getType() {
        return $this->target_type;
    }
    
    public function getRow() {
        $targetClass = $this->type;
        $target = new $targetClass;
        $target = $target->findByAttributes(array('target_id' => $this->target_id));
        
        return $target;
    }
} 