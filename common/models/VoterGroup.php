<?php
/**
 * This is the model class for table "voter_group".
 *
 * The followings are the available columns in table 'voter_group':
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property integer $status
 * @property integer $user_id
 * @property integer $election_id Related Election if Group is not global
 * @property string $created_ts
 *
 * The followings are the available model relations:
 * @property ElectionVoterGroup[] $electionGroups
 * @property Profile $userProfile
 * @property VoterGroupMember[] $voterGroupMembers
 * @property Election $election
 */
class VoterGroup extends CActiveRecord
{
    const TYPE_LOCAL = 1;
    
    const TYPE_GLOBAL = 0;
    
    const STATUS_INACTIVE = 0;
    
    const STATUS_ACTIVE = 1;
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return VoterGroup the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function init()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->type = self::TYPE_GLOBAL;
    }

    public function behaviors()
    {
        return array(
            'updateDateBehavior' => array(
                'class' => 'UpdateDateBehavior',
                'fields' => array(
                    'create'=> array('created_ts'),
                )
            )
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'voter_group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, user_id', 'required'),
            array('type, status, user_id', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>512),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, type, status, user_id, created_ts', 'safe', 'on'=>'search'),
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
            'electionGroups' => array(self::HAS_MANY, 'ElectionGroup', 'voter_group_id'),
            'userProfile' => array(self::BELONGS_TO, 'Profile', 'user_id'),
            'voterGroupMembers' => array(self::HAS_MANY, 'VoterGroupMember', 'voter_group_id'),
            'election' => array(self::BELONGS_TO, 'Election', 'election_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
            'status' => 'Status',
            'user_id' => 'User',
            'created_ts' => 'Created Ts',
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
        $criteria->compare('name',$this->name,true);
        $criteria->compare('type',$this->type);
        $criteria->compare('status',$this->status);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('created_ts',$this->created_ts,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}