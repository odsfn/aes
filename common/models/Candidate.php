<?php
/**
 * This is the model class for table "candidate".
 *
 * The followings are the available columns in table 'candidate':
 * @property integer $id
 * @property integer $user_id
 * @property integer $election_id
 * @property integer $status
 * @property integer $electoral_list_pos
 * @property integer $appointer_id
 *
 * The followings are the available model relations:
 * @property Election $election
 * @property Profile $user
 * @property Vote[] $votes
 * @property int $acceptedVotesCount Votes count that were passed to this candidate
 */
class Candidate extends CActiveRecord implements iCommentable
{

    const STATUS_INVITED = 0;

    const STATUS_AWAITING_CONFIRMATION = 1;

    const STATUS_REGISTERED = 2;

    const STATUS_REFUSED = 3;

    const STATUS_BLOCKED = 4;

    protected $transaction;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Candidate the static model class
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
        return 'candidate';
    }

    public function behaviors() {
        return array('AttrsChangeHandlerBehavior');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, election_id', 'required'),
            array('user_id, election_id, status, electoral_list_pos, appointer_id ', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, election_id, status, electoral_list_pos, appointer_id ', 'safe', 'on'=>'search'),
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
            'election' => array(self::BELONGS_TO, 'Election', 'election_id'),
            'profile' => array(self::BELONGS_TO, 'Profile', 'user_id'),
            'votes' => array(self::HAS_MANY, 'Vote', 'candidate_id'),
            'acceptedVotesCount' => array(self::STAT, 'Vote', 'candidate_id', 'condition' => 'status = ' . Vote::STATUS_PASSED),
        );
        
        Rate::applyRelations($relations, $this);
        
        return $relations;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'appointer_id ' => 'Appointer Id',
            'election_id' => 'Election',
            'status' => 'Status',
            'status_changed_ts' => 'Status Changed Time',
            'electoral_list_pos' => 'Electoral List Pos',
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
        $criteria->compare('appointer_id ',$this->appointer_id );
        $criteria->compare('election_id',$this->election_id);
        $criteria->compare('status',$this->status);
        $criteria->compare('electoral_list_pos',$this->electoral_list_pos);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    protected function beforeSave() {

        $this->appointer_id  = Yii::app()->user->id;
        if ($this->isNewRecord 
            && $this->election->cand_reg_type == Election::CAND_REG_TYPE_SELF 
            && $this->appointer_id == $this->user_id) 
        { 
            if ($this->election->cand_reg_confirm == Election::CAND_REG_CONFIRM_NOTNEED) {
                $this->status = self::STATUS_REGISTERED;
            } else {
                $this->status = self::STATUS_AWAITING_CONFIRMATION;
            }
        }
        
        if ($this->status == Candidate::STATUS_REGISTERED && !$this->electoral_list_pos) {

            $db = Yii::app()->db;

            $this->transaction = $db->beginTransaction();

            $maxListPos = $db->createCommand(
                    'SELECT MAX(electoral_list_pos) FROM candidate WHERE election_id = ' . $this->election_id
                        . ' LOCK IN SHARE MODE'
            )->queryScalar();

            $this->electoral_list_pos = ++$maxListPos;
        }

        if ($this->isNewRecord || $this->isStoredDiffers('status'))
            $this->status_changed_ts = date('Y-m-d H:i:s');

        return parent::beforeSave();
    }

    protected function afterSave() {

        if(isset($this->transaction))
            $this->transaction->commit();

        return parent::afterSave();
    }

    public function criteriaWithStatusOnly($status) {

        $this->getDbCriteria()->mergeWith(self::getCriteriaWithStatusOnly($status));

        return $this;
    }

    public static function getCriteriaWithStatusOnly($status) {
        return new CDbCriteria(array('condition' => 'status = ' . (int)$status));
    }

    /**
     * @return bool
     */
    public function canUnassignedComment(){
        return true;
    }

    /**
     * @return bool
     */
    public function canUnassignedRead(){
        return true;
    }

    public function checkUserInRole($userId, $role) {
        return false;
    }

    /**
     * Checks whether specified user has voted for candidate
     * @param int $userId
     * @return boolean
     */
    public function isAdherent($userId) {
        /**
         * Note! Should be like this $votes = $this->votes(array('condition' => 'status = ' . Vote::STATUS_PASSED . ' AND user_id = ' . (int)$userId));
         * but it is not work during creation PetitionRate from Rest controller.
         *
         * Quick fix for unexplained bug.
         */
        $votes = $this->getRelated('votes', $this->isNewRecord, array('condition' => 'status = ' . Vote::STATUS_PASSED . ' AND user_id = ' . (int)$userId));
        if($votes && count($votes) > 0)
            return true;

        return false;
    }
}