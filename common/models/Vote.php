<?php

/**
 * This is the model class for table "vote".
 *
 * The followings are the available columns in table 'vote':
 * @property integer $id
 * @property string $date
 * @property integer $candidate_id
 * @property integer $user_id
 * @property integer $status
 * @property integer $election_id
 * 
 * The followings are the available model relations:
 * @property Profile $profile
 * @property Candidate $candidate
 * @property Election $election
 */
class Vote extends CActiveRecord implements iCommentable
{
    const STATUS_PASSED = 0;    //Default status after vote

    const STATUS_DECLINED = 1;  //When candidate declined this vote

    const STATUS_REVOKED = 2;   //Voter revoked this vote

    public function behaviors()
    {
        return array(
            'UpdateDateBehavior' => array(
                'class' => 'UpdateDateBehavior',
                'fields' => array('date')
            ),
            'AttrsChangeHandlerBehavior' => array(
                'class' => 'AttrsChangeHandlerBehavior'
            )
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Vote the static model class
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
        return 'vote';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('candidate_id, user_id, election_id', 'required'),
            array('candidate_id, user_id, status, election_id', 'numerical', 'integerOnly' => true),
            array('date', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, date, candidate_id, user_id, status', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        $relations = array(
            'profile' => array(self::BELONGS_TO, 'Profile', 'user_id'),
            'candidate' => array(self::BELONGS_TO, 'Candidate', 'candidate_id'),
            'elector' => array(self::BELONGS_TO, 'Elector', 'user_id'),
            'election' => array(self::BELONGS_TO, 'Election', 'election_id')
        );
        
        Rate::applyRelations($relations, $this);
        Comment::applyRelations($relations, $this);
        
        return $relations;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'date' => 'Date',
            'candidate_id' => 'Candidate',
            'user_id' => 'User',
            'status' => 'Status',
            'election_id' => 'Election'
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
        $criteria->compare('date', $this->date, true);
        $criteria->compare('candidate_id', $this->candidate_id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    protected function beforeSave()
    {
        if ($this->candidate->status != Candidate::STATUS_REGISTERED)
            throw new Exception('Vote can\'t be passed or changed for not registered candidate');

        if ($this->candidate->election->status != Election::STATUS_ELECTION)
            throw new Exception('Vote can\'t be created or changed for inactive election');

        if ($this->isStoredDiffers('user_id') || $this->isStoredDiffers('candidate_id'))
            throw new Exception('Vote can\'t be reassigned');

        if ($this->isStoredDiffers('status') && $this->storedValue('status') != Vote::STATUS_PASSED)
            throw new Exception('Status of declined or revoked vote can\'t be changed');

        $lastVote = $this->election->getLastVote($this->user_id);
        if($this->isNewRecord && !empty($lastVote) && $lastVote->status == Vote::STATUS_PASSED)
            throw new Exception('Vote has already been passed');

        if ($this->election->isRevotesLimitReached($this->user_id))
            throw new Exception('Revote limit has been reached');

        if ($this->election->isRevokeVoteTimeoutReached($this->user_id))
            throw new Exception('Revoke vote timeout has been reached');

        if ($this->election->isRevoteTimeoutReached($this->user_id))
            throw new Exception('Revote timeout has been reached');
        
        return parent::beforeSave();
    }

    protected function beforeDelete()
    {
        throw new Exception('Vote can\'t be deleted');
    }

    /**
     * @return bool 
     */
    public function canUnassignedComment()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canUnassignedRead()
    {
        return true;
    }

    public function checkUserInRole($userId, $role)
    {
        return false;
    }

}