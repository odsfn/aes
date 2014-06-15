<?php

/**
 * This is the model class for table "personIdentifier".
 *
 * The followings are the available columns in table 'personIdentifier':
 * @property integer $id
 * @property integer $profile_id
 * @property integer $status
 * @property string $last_update_ts
 * @property integer $type
 * @property string $data
 *
 * The followings are the available model relations:
 * @property UserProfile $profile
 */
class PersonIdentifier extends CActiveRecord
{
    const STATUS_APPLIED = 0;
    const STATUS_REJECTED = 1;
    const STATUS_VERIFIED = 2;

    public $uploadingImage;
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return PersonIdentifier the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function getTypes()
    {        
        return array_keys(self::getPersonIdentifiersConfig());
    }

    public static function getTypesCaptions()
    {
        $values = self::getTypes();
        
        $labels = array();
        
        foreach ($values as $type) {
            $typeConf = self::getPersonIdentifiersConfig($type);
            
            if (isset($typeConf['caption']) && !empty($typeConf['caption'])) {
                $labels[] = $typeConf['caption'];
            } else {
                $labels[] = $type;
            }
        }
        
        return array_combine($values, $labels);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'personIdentifier';
    }

    public function behaviors()
    {
        return array(            
            'uploadingImage' => array(
                'class' => 'common.components.UploadingImageBehavior',
                'imagesDir' => Yii::app()->getModule('personIdentifier')->imagesDir,
                'uploadOnBeforeSave' => false
            ),
            'updateDateBehavior' => array(
                'class' => 'UpdateDateBehavior',
                'fields' => array(
                    'create'=> array('last_update_ts'),
                    'update'=> array('last_update_ts')
                )
            )
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return CMap::mergeArray(array(
            array('profile_id', 'required', 'except' => 'userApply'),
            array('status', 'default', 'value' => PersonIdentifier::STATUS_APPLIED),
            array('status, type', 'required'),
            array('type', 'length', 'max'=>256),
            array('type', 'in', 'range'=> self::getTypes(), 'allowEmpty' => false),
            array('profile_id, status', 'numerical', 'integerOnly'=>true),
            array('image', 'required', 'on' => 'update'),
            array('image', 'length', 'max'=>512),
            array('last_update_ts, data', 'safe'),
//            array('uploadingImage', 'required', 'on' => 'userApply'),
            array('uploadingImage', 'file', 'except' => 'update',
                'types' => implode(',', Yii::app()->getModule('personIdentifier')->photoExtensions), 
                'maxSize' => Yii::app()->getModule('personIdentifier')->photoMaxSize,
                'safe' => true, 'allowEmpty' => false
            ),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, profile_id, status, last_update_ts, type', 'safe', 'on'=>'search'),
        ), $this->getTypeRules());
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'profile' => array(self::BELONGS_TO, 'Profile', 'profile_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array_merge(
            array(
                'id' => 'ID',
                'profile_id' => 'Profile',
                'status' => 'Status',
                'last_update_ts' => 'Last Update Ts',
                'type' => 'Type',
                'uploadingImage' => 'Scan copy or photo of document'
            ),
            $this->getTypeAttributeLabels()
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
        $criteria->compare('profile_id',$this->profile_id);
        $criteria->compare('status',$this->status);
        $criteria->compare('last_update_ts',$this->last_update_ts,true);
        $criteria->compare('type',$this->type);
        $criteria->compare('data',$this->data,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    protected function beforeSave()
    {
        $this->data = serialize((object)$this->data);
        
        return parent::beforeSave();
    }

    protected function getTypeRules()
    {
        $attrs = array();
        
        if ($this->type) {            
            $identifiers = self::getPersonIdentifiersConfig();
            
            if (!isset($identifiers[$this->type]) || !isset($identifiers[$this->type]['rules'])) {
                throw new CException("There is no attributes config for PersonIdentifier with type '{$this->type}'");
            }
            
            $attrs = $identifiers[$this->type]['rules'];
        } else {
            throw new CException('Type is undefined for PersonIdentifier');
        }
        
        return $attrs;
    }

    public function getTypeAttributeNames()
    {
        $attrs = array();
        
        foreach ($this->getTypeRules() as $rule) {
            $currRuleAttrs = AESHelper::explode($rule[0]);
            foreach ($currRuleAttrs as $attr) {
                if (!in_array($attr, $attrs)) {
                    $attrs[] = $attr;
                }
            }
        }
        
        return $attrs;
    }
    
    public function getTypeAttributeLabels()
    {
        $labels = array();
        
        $conf = self::getPersonIdentifiersConfig($this->type);
        
        if (isset($conf['labels'])) {
            $labels = $conf['labels'];
        }
        
        return $labels;
    }

    public function getData()
    {
        if (is_string($this->data)) {
            $this->data = (object)unserialize($this->data);
        } elseif (!$this->data) {
            $this->data = new stdClass;
        }
        
        return $this->data;
    }
    
    public function __get($name)
    {
        if ($this->isSerializedAttr($name)) {
            return $this->data->$name;
        } else {
            return parent::__get($name);
        }
    }
    
    public function __set($name, $value)
    {
        $isSerialized = $this->isSerializedAttr($name);
        if ($isSerialized) {
            $this->getData()->$name = $value;
        } else {
            parent::__set($name, $value);
        }
    }
    
    public function getTypeConfig()
    {
        return self::getPersonIdentifiersConfig($this->type);
    }
    
    protected function isSerializedAttr($name)
    {
        return (!in_array($name, $this->attributeNames()) && in_array($name, $this->getTypeAttributeNames()));
    }
    
    protected static function getPersonIdentifiersConfig($type = null)
    {
        $identifiers = Yii::app()->getModule('personIdentifier')->personIdentifiers;
        
        if (!$identifiers || count($identifiers) === 0) {
            throw new CException('PersonIdentifier types not specified in config!');
        }
        
        if ($type) {
            return $identifiers[$type];
        }
        
        return $identifiers;
    }
}