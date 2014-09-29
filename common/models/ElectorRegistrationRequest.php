<?php
/**
 * This is the model class for table "elector_registration_request".
 *
 * The followings are the available columns in table 'elector_registration_request':
 * @property integer $id
 * @property string $created_ts
 * @property integer $initiator_id
 * @property integer $election_id
 * @property integer $user_id
 * @property string $data
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property Election $election
 * @property Profile $initiator
 * @property Profile $profile
 * @property Elector $elector
 */
class ElectorRegistrationRequest extends CActiveRecord
{
    const STATUS_AWAITING_ADMIN_DECISION = 0;
    const STATUS_AWAITING_USERS_DECISION = 1;
    const STATUS_REGISTERED = 9;
    const STATUS_DECLINED = 10;
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ElectorRegistrationRequest the static model class
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
        return 'elector_registration_request';
    }

    public function behaviors()
    {
        return array(
            'UpdateDateBehavior' => array(
                'class' => 'UpdateDateBehavior',
                'fields' => array(
                    'create'=> array('created_ts')
                )
            ),
            'attrsChangeHandler' => array(
                'class' => 'AttrsChangeHandlerBehavior',
                'track' => array('status')
            ),
            'transformAttributes' => array(
                'class' => 'TransformAttributesBehavior',
                'transformations' => array('data')
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
            array('election_id, user_id', 'required'),
            array('initiator_id, election_id, user_id, status', 'numerical', 'integerOnly'=>true),
            array('created_ts, data', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, created_ts, initiator_id, election_id, user_id, data, status', 'safe', 'on'=>'search'),
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
            'election' => array(self::BELONGS_TO, 'Election', 'election_id'),
            'initiator' => array(self::BELONGS_TO, 'Profile', 'initiator_id'),
            'profile' => array(self::BELONGS_TO, 'Profile', 'user_id'),
            'elector' => array(self::HAS_ONE, 'Elector', 'id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'created_ts' => 'Created Ts',
            'initiator_id' => 'Initiator',
            'election_id' => 'Election',
            'user_id' => 'User',
            'data' => 'Data',
            'status' => 'Status',
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
        $criteria->compare('created_ts',$this->created_ts,true);
        $criteria->compare('initiator_id',$this->initiator_id);
        $criteria->compare('election_id',$this->election_id);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('data',$this->data,true);
        $criteria->compare('status',$this->status);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    public function afterStoredAttrChanged_status($value, $oldValue, $attrName)
    {
        if ($value == self::STATUS_REGISTERED) {
            $this->createElector();
            $this->addUserToGroups();
        }
    }

    protected function beforeSave()
    {
        if ($this->isNewRecord) {
            $this->initiator_id = Yii::app()->user->id;
            //Force status to this value for prevent ability of users to send 
            //"Registered" status on registration apply
            $this->status = self::STATUS_AWAITING_ADMIN_DECISION;
        } else {
            $this->initiator_id = $this->storedValue('initiator_id');
        }
        
        if ($this->isNewRecord && !self::isAvailable($this->election, $this->user_id))
            throw new CException('ElectorRegistrationRequest is unavailable');

        if ($this->isStoredDiffers('status') && !$this->election->isElectorsRegistrationOpen())
            throw new CException('ElectorRegistrationRequest.status can\'t be '
                    . 'changed because electors registration is closed');
        
        if ($this->isNewRecord
            && $this->election->voter_reg_confirm == Election::VOTER_REG_CONFIRM_NOTNEED) 
        {
            $this->status = self::STATUS_REGISTERED;
        }
        
        return parent::beforeSave();
    }
    
    public function afterInsert()
    {
        if ($this->election->voter_reg_confirm == Election::VOTER_REG_CONFIRM_NOTNEED
            && $this->status = self::STATUS_REGISTERED) 
        {
            $this->createElector();
        }
        
        if($this->election->voter_reg_confirm == Election::VOTER_REG_CONFIRM_NOTNEED)
            $this->addUserToGroups();
    }
    
    protected function createElector()
    {
        $elector = new Elector;
        $elector->user_id = $this->user_id;
        $elector->election_id = $this->election_id;
        $elector->status = Elector::STATUS_ACTIVE;
        $elector->registration_request_id = $this->id;
        $elector->save();
        return $elector;
    }
    
    protected function addUserToGroups()
    {
        if ($this->election->voter_group_restriction != Election::VGR_GROUPS_ADD)
            return;
        
        $dataAttr = $this->getUnserializedAttr('data');
        if(!isset($dataAttr['groups']))
            return;
        
        $groupIds = $dataAttr['groups'];
        
        if(count($groupIds) == 0)
            return;
        
        $groups = $this->election->getRelated(
            'localVoterGroups', true, array(
                'condition' => 'localVoterGroups.id IN (' . implode(',', $groupIds) . ')'
            )
        );
        
        foreach ($groups as $group) {
            $group->addMember($this->user_id);
        }
    }
    
    /**
     * Return TRUE if registration request is available to user
     * for specified election
     * 
     * @param Election|int $election
     * @param int $user userId
     * @return boolean
     */
    public static function isAvailable($election, $user)
    {
        $userId=$user;
        if (is_numeric($election)) {
            $electionId = $election; 
            $election = Election::model()->findByPk($electionId);
        } else {
            $electionId = $election->id;
        }
        
        if( !$election->isElectorsRegistrationOpen() ) 
            return false;
        
        $elector = Elector::model()->findByAttributes(array(
            'user_id' => $userId,
            'election_id' => $electionId
        ));

        if($elector)
            return false;

        $registration = self::model()->findAllByAttributes(array(
            'user_id' => $userId,
            'election_id' => $electionId
        ));

        if($registration)
            return false;
        
        return true;
    }
}
