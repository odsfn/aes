<?php

/**
 * This is the model class for table "elector".
 *
 * The followings are the available columns in table 'elector':
 * @property integer $id
 * @property integer $election_id
 * @property integer $user_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property Profile $user
 * @property Election $election
 * @property ElectorRegistrationRequest $registrationRequest
 */
class Elector extends CActiveRecord
{
    const STATUS_ACTIVE = 0;

    const STATUS_BLOCKED = 2;

    public static $statusLabels = array(
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_BLOCKED => 'Blocked'
    );

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Elector the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'elector';
    }

    public function init()
    {
        parent::init();
        $this->status = self::STATUS_ACTIVE;
    }

    public function behaviors()
    {
        return array(
            'attrsChangeHandler' => array(
                'class' => 'AttrsChangeHandlerBehavior'
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
        return array(
            array('election_id, user_id, status', 'required'),
            array('election_id, user_id, status', 'numerical', 'integerOnly' => true),
            array('status', 'in', 'range' => array_keys(self::$statusLabels)),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, election_id, user_id', 'safe', 'on' => 'search'),
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
            'profile' => array(self::BELONGS_TO, 'Profile', 'user_id'),
            'election' => array(self::BELONGS_TO, 'Election', 'election_id'),
            'registrationRequest' => array(self::HAS_ONE, 
                'ElectorRegistrationRequest', 'registration_request_id'
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
            'election_id' => 'Election',
            'user_id' => 'User',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('election_id', $this->election_id);
        $criteria->compare('user_id', $this->user_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    protected function beforeSave()
    {
        $result = parent::beforeSave();
        $params = array('election'=>$this->election);
        
        if ($this->isStoredDiffers('status') 
                && $this->status == self::STATUS_BLOCKED 
                && !Yii::app()->user->checkAccess('election_blockElector', $params))
        {
            throw new Exception('Only electors manager can block electors');
        }
        
        
        if ($this->isStoredDiffers('user_id') || $this->isStoredDiffers('election_id')) {
            throw new Exception('user_id or election_id can\'t be changed');
        }
        
        return $result;
    }


    protected function afterSave()
    {

        if (!$this->election->checkUserInRole($this->user_id, 'election_elector'))
            $this->election->assignRoleToUser($this->user_id, 'election_elector');

        parent::afterSave();
    }

    protected function beforeDelete()
    {

        $this->election->revokeRoleFromUser($this->user_id, 'election_elector');

        return parent::beforeDelete();
    }

}
