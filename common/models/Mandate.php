 <?php

/**
 * This is the model class for table "mandate".
 *
 * The followings are the available columns in table 'mandate':
 * @property integer $id
 * @property integer $election_id
 * @property integer $candidate_id
 * @property string $name
 * @property string $submiting_ts
 * @property string $expiration_ts
 * @property integer $validity
 * @property integer $votes_count
 *
 * The followings are the available model relations:
 * @property Candidate $candidate
 * @property Election $election
 */
class Mandate extends CActiveRecord implements iCommentable
{
    
    const STATUS_ACTIVE = 0;
    const STATUS_EXPIRED = 1;
    const STATUS_REVOKED = 2;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Mandate the static model class
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
        return 'mandate';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('election_id, candidate_id, name, validity, votes_count', 'required'),
            array('election_id, candidate_id, validity, votes_count', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>1000),
            array('submiting_ts, expiration_ts', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, election_id, candidate_id, name, submiting_ts, expiration_ts, validity, votes_count', 'safe', 'on'=>'search'),
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
            'candidate' => array(self::BELONGS_TO, 'Candidate', 'candidate_id'),
            'election' => array(self::BELONGS_TO, 'Election', 'election_id'),
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
            'election_id' => 'Election',
            'candidate_id' => 'Candidate',
            'name' => 'Name',
            'submiting_ts' => 'Submiting Date',
            'expiration_ts' => 'Expiration Date',
            'validity' => 'Validity',
            'votes_count' => 'Votes Count',
            'status'      => 'Status'
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
        $criteria->compare('election_id',$this->election_id);
        $criteria->compare('candidate_id',$this->candidate_id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('submiting_ts',$this->submiting_ts,true);
        $criteria->compare('expiration_ts',$this->expiration_ts,true);
        $criteria->compare('validity',$this->validity);
        $criteria->compare('votes_count',$this->votes_count);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    public function canUnassignedComment() {
        return true;
    }
    
    public function canUnassignedRead() {
        return true;
    }
    
    public function checkUserInRole($userId, $role) {
        return false;
    }
    
    public function isActive() {
        
        $expDate = new DateTime($this->expiration_ts);
        $curDate = new DateTime;
        
        if($this->status == self::STATUS_ACTIVE && $expDate > $curDate)
            return true;
        
        return false;
    }
    
    /**
     * Checks whether specified user can create petition for this mandate
     * @param int $userId
     * @return boolean
     */
    public function acceptsPetitionFrom($userId) {
        
        if($this->isActive() && $this->candidate->isAdherent($userId))
            return true;
        
        return false;
    }
    
    public function getUsersMandates($userId)
    {
        $candidates = Candidate::model()->findAllByAttributes(array('user_id' => (int)$userId));
        $cand_ids = array();
        
        foreach ($candidates as $candidate) {
            $cand_ids[] = $candidate->id;
        }
        
        return $this->findAllByAttributes(array('candidate_id'=>$cand_ids));
    }
}