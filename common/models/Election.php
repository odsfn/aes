<?php

Yii::import('stateMachine.*');

/**
 * This is the model class for table "election".
 *
 * The followings are the available columns in table 'election':
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property integer $status
 * @property string $mandate
 * @property integer $quote
 * @property integer $validity
 * @property integer $cand_reg_type
 * @property integer $cand_reg_confirm
 * @property integer $voter_reg_type
 * @property integer $voter_reg_confirm
 * @property integer $voter_group_restriction
 * @property integer $unassigned_access_level Access level for users that were not assigned to this election
 * @property integer $target_id
 * @property integer $revotes_count How many times voter can revote in the election. If 0 - it means 
 *                                  electors can't remove their votes. If 1 - it means voter can remove
 *                                  his vote and add to any candidate again only once.
 * @property integer $remove_vote_time Specifies how much time in minutes voter can to remove his vote. 
 *                                      Time calculates from a moment when user's last vote was assigned.
 *                                      If it equals 0 then time is unlimited before the election will finish.
 * @property integet $revote_time Specifies how much time in minutes voter can to assign his removed vote 
 *                                      to another candidate. Time calculates from a moment when user 
 *                                      removes his last vote. If it equals 0 then time is unlimited before 
 *                                      the election will finish.
 * 
 * The followings are the available model relations:
 * @property User $user
 * @property Target $target AR from base parent table.
 * @property Candidate[] $candidates Candidates
 * @property Elector[] $electors Electors
 * @property VoterGroup[] $voterGroups
 */
class Election extends CActiveRecord implements iPostable, iCommentable
{
    const UNASSIGNED_CAN_POST = 2;

    const UNASSIGNED_CAN_READ = 1;

    const UNASSIGNED_CAN_NONE = 0;

    const STATUS_PUBLISHED = 0;

    const STATUS_REGISTRATION = 1;

    const STATUS_ELECTION = 2;

    const STATUS_FINISHED = 3;

    const STATUS_CANCELED = 4;
    
    const CAND_REG_TYPE_ADMIN = 1;
    
    const CAND_REG_TYPE_SELF = 0;
    
    const CAND_REG_CONFIRM_NOTNEED = 0;
    
    const CAND_REG_CONFIRM_NEED = 1;
    
    const VOTER_REG_TYPE_SELF = 0;
    
    const VOTER_REG_TYPE_ADMIN = 1;
    
    const VOTER_REG_CONFIRM_NOTNEED = 0;
    
    const VOTER_REG_CONFIRM_NEED = 1;
    
    // Voter Group Restriction
    const VGR_NO = 0;
    
    const VGR_GROUPS_ONLY = 1;
    
    const VGR_GROUPS_ADD = 2;
    
    public static $statuses = array(
        self::STATUS_PUBLISHED => 'Published',
        self::STATUS_REGISTRATION => 'Registration',
        self::STATUS_ELECTION => 'Election',
        self::STATUS_FINISHED => 'Finished',
        self::STATUS_CANCELED => 'Canceled',
    );

    public static $cand_reg_types = array(
        self::CAND_REG_TYPE_SELF => 'Myself',
        self::CAND_REG_TYPE_ADMIN => 'By Admin',
    );

    public static $cand_reg_confirms = array(
        self::CAND_REG_CONFIRM_NOTNEED => 'No',
        self::CAND_REG_CONFIRM_NEED => 'Yes',
    );

    public static $voter_reg_types = array(
        self::VOTER_REG_TYPE_SELF => 'Myself',
        self::VOTER_REG_TYPE_ADMIN => 'By Admin',
    );

    public static $voter_reg_confirms = array(
        self::VOTER_REG_CONFIRM_NOTNEED => 'No',
        self::VOTER_REG_CONFIRM_NEED => 'Yes',
    );

    public static $voter_group_restrictions = array(
        self::VGR_NO => 'No',
        self::VGR_GROUPS_ONLY => 'From specified groups only',
        self::VGR_GROUPS_ADD => 'From specified groups or adding into them',
    );
    
    public $uploaded_file = null;

    private $_text_status = null;

    public function getText_status()
    {
        if ($this->_text_status === null)
            $this->_text_status = Yii::t('aes', self::$statuses[$this->status]);
        return $this->_text_status;
    }

    public function getStatusName()
    {
        if ($this->status !== null)
            return self::$statuses[$this->status];
        else
            return null;
    }

    private $_have_pic = null;

    public function getHave_pic()
    {
        if ($this->_have_pic === null)
            $this->_have_pic = !empty($this->image);
        
        return $this->_have_pic;
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Election the static model class
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
        return 'election';
    }

    public function behaviors()
    {
        return array(
            'ObjectAuthAssignmentBehavior',
            'childTable' => array(
                'class' => 'ChildTableBehavior',
                'parentTable' => 'target',
                'parentTablePk' => 'target_id',
                'childConstraint' => 'target_id'
            ),
            'galleryBehavior' => array(
                'class' => 'GalleryBehavior',
                'idAttribute' => 'gallery_id',
                'versions' => array(
                    'small' => array(
                        'centeredpreview' => array(98, 98),
                    ),
                    'medium' => array(
                        'resize' => array(800, null),
                    )
                ),
                'name' => true,
                'description' => true,
            ),
            'attrsChangeHandler' => array(
                'class' => 'AttrsChangeHandlerBehavior',
                'track' => array('status')
            ),
            "statusState" => array(
                "class" => "AStateMachine",
                "states" => array(
                    array(
                        'name' => 'not_saved',
                        'transitsTo' => 'Published'
                    ),
                    array(
                        'name' => 'Published',
                        'transitsTo' => 'Registration, Canceled'
                    ),
                    array(
                        'name' => 'Registration',
                        'transitsTo' => 'Published, Election, Canceled'
                    ),
                    array(
                        'name' => 'Election',
                        'transitsTo' => 'Finished, Canceled'
                    ),
                    array(
                        'name' => 'Finished',
                        'class' => 'ElectionFinishedState'
                    ),
                    array('name' => 'Canceled')
                ),
                "defaultStateName" => "not_saved",
                "checkTransitionMap" => true,
                "stateName" => $this->statusName,
            ),
            'uploadingImage' => array(
                'class' => 'common.components.UploadingImageBehavior',
                'uploadingImageAttr' => 'uploaded_file',
                'imagePathAttr' => 'image',
                'imagesDir' => '/uploads/elections',
                'useDefault' => false,
                'thumbnailsToCreate' => array(96),
                'resize' => array(400, 400)
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
            array('user_id, name, mandate, quote, validity, cand_reg_type,'
                . 'voter_group_restriction, cand_reg_confirm, voter_reg_type, '
                . 'voter_reg_confirm', 'required'),
            array('user_id', 'exist', 'className' => 'UserAccount', 'attributeName' => 'id'),
            array('quote, validity', 'numerical', 'integerOnly' => true, 'min' => 1),
            array('cand_reg_type, cand_reg_confirm, voter_reg_type, voter_reg_confirm', 
                'in', 'range' => array(0, 1)),
            array('name, mandate', 'length', 'max' => 1000),
            array('status', 'in', 'range' => array(0, 1, 2, 3, 4)),
            array('uploaded_file', 'file', 'types' => 'jpg,jpeg,jpe,png,gif', 
                'maxSize' => 5000000, 'allowEmpty' => true),
            array('unassigned_access_level', 'numerical', 'integerOnly' => true,
                'min' => 0),
            array('unassigned_access_level', 'default', 'setOnEmpty' => true, 
                'value' => self::UNASSIGNED_CAN_POST),
            array('id, user_id, name, status, mandate, quote, validity,'
                . ' cand_reg_type, cand_reg_confirm, voter_reg_type, voter_reg_confirm', 
                'safe', 'on' => 'search'),
            array('name, status', 'safe', 'on' => 'search'),
            array('revotes_count', 'default', 'setOnEmpty' => true, 'value' => 
                (isset(Yii::app()->params->revotes_count) ? Yii::app()->params->revotes_count : 1)),
            array('remove_vote_time', 'default', 'setOnEmpty' => true, 'value' => 
                (isset(Yii::app()->params->remove_vote_time) ? Yii::app()->params->remove_vote_time : 60 * 6)),
            array('revote_time', 'default', 'setOnEmpty' => true, 'value' => 
                (isset(Yii::app()->params->revote_time) ? Yii::app()->params->revote_time : 60 * 6)),
            array('revote_time, remove_vote_time, revotes_count', 'numerical', 
                'integerOnly' => true, 'min' => 0),
            array('voter_group_restriction', 'in', 'range' => array_keys(self::$voter_group_restrictions)),
            array('id, name, status, text_status, have_pic, revotes_count,'
                . ' remove_vote_time, revote_time, imageThmbnl96', 'safe', 'on' => 'rest'),
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
            'user' => array(self::BELONGS_TO, 'UserAccount', 'user_id'),
            'target' => array(self::BELONGS_TO, 'Target', 'target_id'),
            'candidates' => array(self::HAS_MANY, 'Candidate', 'election_id'),
            'candidatesWithVotes' => array(self::HAS_MANY, 'Candidate', 'election_id',
                'condition' => 'status = ' . Candidate::STATUS_REGISTERED,
                'with' => 'acceptedVotesCount'
            ),
            'electors' => array(self::HAS_MANY, 'Elector', 'election_id'),
            'voterGroups' => array(self::MANY_MANY, 'VoterGroup', 
                'election_voter_group(election_id, voter_group_id)'
            ),
            'localVoterGroups' => array(self::MANY_MANY, 'VoterGroup', 
                'election_voter_group(election_id, voter_group_id)',
                'condition' => 'type = ' . VoterGroup::TYPE_LOCAL
            ),
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
            'name' => 'Title',
            'status' => 'Status',
            'mandate' => 'Mandate',
            'quote' => 'Quote',
            'validity' => 'Validity',
            'cand_reg_type' => 'Candidate Registration Type',
            'cand_reg_confirm' => 'Candidate Registration Confirmation',
            'voter_group_restriction' => 'Voter Group Restriction',
            'voter_reg_type' => 'Voter Registration Type',
            'voter_reg_confirm' => 'Voter Registration Confirmation',
            'uploaded_file' => 'Image',
            'revotes_count' => 'Revote Count',
            'remove_vote_time' => 'Remove Vote Time (minutes after vote added)',
            'revote_time' => 'Revote Time (minutes after last vote revoked)',
            'image' => 'Uploaded image relative path'
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
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('mandate', $this->mandate, true);
        $criteria->compare('quote', $this->quote);
        $criteria->compare('validity', $this->validity);
        $criteria->compare('cand_reg_type', $this->cand_reg_type);
        $criteria->compare('cand_reg_confirm', $this->cand_reg_confirm);
        $criteria->compare('voter_reg_type', $this->voter_reg_type);
        $criteria->compare('voter_reg_confirm', $this->voter_reg_confirm);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function init()
    {
        $this->cand_reg_type = Election::CAND_REG_TYPE_ADMIN;
        
        $this->cand_reg_confirm = Election::CAND_REG_CONFIRM_NEED;
        
        $this->voter_reg_type = Election::VOTER_REG_TYPE_ADMIN;
        
        $this->voter_reg_confirm = Election::VOTER_REG_CONFIRM_NOTNEED;
        
        $this->voter_group_restriction = self::VGR_NO;
        
        $this->revotes_count = (isset(Yii::app()->params->revotes_count) ? Yii::app()->params->revotes_count : 1);

        $this->remove_vote_time = (isset(Yii::app()->params->remove_vote_time) ? Yii::app()->params->remove_vote_time : 60 * 6);

        $this->revote_time = (isset(Yii::app()->params->revote_time) ? Yii::app()->params->revote_time : 60 * 6);
    }

    public function canUnassignedPost()
    {
        return ($this->unassigned_access_level >= self::UNASSIGNED_CAN_POST);
    }

    public function canUnassignedReadPost()
    {
        return ($this->unassigned_access_level >= self::UNASSIGNED_CAN_READ);
    }

    public function canUnassignedComment()
    {
        return true;
    }

    public function canUnassignedRead()
    {
        return true;
    }

    public function getPicUrl()
    {
        if ($this->have_pic) {
            return Yii::app()->getBaseUrl(true) . '/uploads/elections/' . $this->image;
        }

        return false;
    }

    public function afterStoredAttrChanged_status($currentValue, $oldValue, $attrName)
    {
        $this->transition($this->getStatusName());
    }

    public function getAvailableStatuses()
    {

        $result = array();

        $statesAllowed = $this->availableStates;

        //remove default state
        $index = array_search($this->statusState->defaultStateName, $statesAllowed);
        if ($index !== FALSE)
            unset($statesAllowed[$index]);

        //add current state if not present
        $currentStatus = $this->getStatusName();
        $index = array_search($currentStatus, $statesAllowed);
        if ($index === FALSE && $currentStatus != $this->statusState->defaultStateName)
            array_unshift($statesAllowed, $currentStatus);

        //transform to select options acceptable format
        foreach ($statesAllowed as $stateName) {
            $statusId = array_search($stateName, self::$statuses);
            $result["$statusId"] = $stateName;
        }

        return $result;
    }

    public function isRevotesLimitReached($userId)
    {
        return ( $this->revotes_count - $this->getActualRevotesCount($userId) <= 0 ) ? true : false;
    }

    public function isRevokeVoteTimeoutReached($userId)
    {
        $lastVote = $this->getLastVote($userId);

        if (!$lastVote)
            return false;

        $lastVoteDate = new DateTime($lastVote->date);
        $timeRemains = $lastVoteDate->getTimestamp() + $this->remove_vote_time * 60 - time();

        return ($timeRemains <= 0);
    }

    public function isRevoteTimeoutReached($userId)
    {
        $lastVote = $this->getLastVote($userId);

        if (!$lastVote)
            return false;

        $lastVoteDate = new DateTime($lastVote->date);
        $timeRemains = $lastVoteDate->getTimestamp() + $this->revote_time * 60 - time();

        return ($timeRemains <= 0);
    }

    public function isElectorsRegistrationOpen()
    {
        return ($this->voter_reg_type == self::VOTER_REG_TYPE_SELF 
            &&  in_array($this->status, 
                    array(self::STATUS_REGISTRATION, self::STATUS_ELECTION)
                )
        );
    }

    public function getLastVote($userId = null)
    {
        return Vote::model()->find(new CDbCriteria(
                        array(
                    'condition' => 'election_id = ' . $this->id
                    . (!empty($userId) ? ' AND user_id = ' . $userId : '' ),
                    'order' => 'id DESC'
                        )
        ));
    }

    public function getActualRevotesCount($userId)
    {
        $revokedCount = Vote::model()->count($condition = new CDbCriteria(
                array(
            'condition' => 'election_id = ' . $this->id
            . ' AND user_id = ' . $userId
                )
        ));

        $revotesCount = $revokedCount;

        $lastVote = $this->getLastVote($userId);

        if (!$lastVote || $lastVote->status !== Vote::STATUS_PASSED)
            $revotesCount--;

        return $revotesCount;
    }


    public function getAllowedVoterRegTypes() 
    {
        if ( !is_null($this->voter_group_restriction) 
            && $this->voter_group_restriction == self::VGR_GROUPS_ONLY ) {
            $types = array(self::VOTER_REG_TYPE_ADMIN);
        } else if ( !is_null($this->voter_group_restriction) 
            && $this->voter_group_restriction == self::VGR_GROUPS_ADD) {
            $types = array(self::VOTER_REG_TYPE_SELF);
        } else
            $types = array_keys(self::$voter_reg_types);
        
        return $types;
    }

    public function getAllowedVoterRegConfirmTypes() 
    {
        if ( !is_null($this->voter_reg_type) 
            && $this->voter_reg_type == self::VOTER_REG_TYPE_ADMIN ) {
            $types = array(self::VOTER_REG_CONFIRM_NOTNEED);
        } else
            $types = array_keys(self::$voter_reg_confirms);
        
        return $types;
    }    
    
    /**
     * Checks whether actual user can add himself as elector
     * @return boolean
     */
    public function canAddSelfAsElector()
    {
        $userId = Yii::app()->user->id;
        if (!$userId
                || $this->status !== self::STATUS_REGISTRATION 
                || $this->voter_reg_type != self::VOTER_REG_TYPE_SELF
                || count($this->electors(array('condition'=>'user_id='.$userId)))
        ) {
            return false;
        }
        
        return true;
    }
    
    protected function beforeSave()
    {
        if($this->voter_group_restriction == self::VGR_GROUPS_ADD)
            $this->voter_reg_type = self::VOTER_REG_TYPE_SELF;
        
        if($this->voter_reg_type == self::VOTER_REG_TYPE_ADMIN)
            $this->voter_reg_confirm = self::VOTER_REG_CONFIRM_NOTNEED;
        
        return parent::beforeSave();
    }
    
    public function getImageThmbnl96()
    {
        return $this->getImage(96);
    }
    
    /**
     * This method fixes error which appears when ElectorRegistrationRequest model 
     * is saved through RestController
     */
    public function setImageThmbnl96($value) {}

    public function getAttributes($names = true)
    {
        $attrs = parent::getAttributes($names);
        $attrs['imageThmbnl96'] = $this->getImageThmbnl96();
        return $attrs;
    }    
}

class ElectionFinishedState extends AState
{

    public function finish()
    {

        $election = $this->getMachine()->getOwner();

        $candidates = $election->candidatesWithVotes;

        foreach ($candidates as $candidate)
            if ($candidate->acceptedVotesCount >= $election->quote)
                $this->createMandate($candidate);
    }

    /**
     * @param Candidate $candidate
     * @return Mandate Mandate created for candidate
     */
    public function createMandate($candidate)
    {

        $election = $this->getMachine()->getOwner();

        $mandate = new Mandate();
        $mandate->name = $election->mandate;
        $mandate->validity = $election->validity;
        $mandate->election_id = $election->id;
        $mandate->candidate_id = $candidate->id;
        $mandate->votes_count = $candidate->acceptedVotesCount;
        $mandate->submiting_ts = date('Y-m-d');

        $exp = new DateTime;
        $exp->add(new DateInterval('P' . $election->validity . 'M'));
        $mandate->expiration_ts = $exp->format('Y-m-d');

        if (!$mandate->save())
            throw new Exception('Can\'t create mandate. Validation errors: ' . print_r($mandate->getErrors(), true));

        return $mandate;
    }

    public function afterEnter(AState $from)
    {
        parent::afterEnter($from);
        $this->getMachine()->getOwner()->finish();
    }

}
