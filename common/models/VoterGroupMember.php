<?php
/**
 * This is the model class for table "voter_group_member".
 *
 * The followings are the available columns in table 'voter_group_member':
 * @property integer $id
 * @property integer $voter_group_id
 * @property integer $user_id
 * @property string $created_ts
 *
 * The followings are the available model relations:
 * @property Profile $userProfile
 * @property VoterGroup $voterGroup
 */
class VoterGroupMember extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return VoterGroupMember the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
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
        return 'voter_group_member';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('voter_group_id, user_id, created_ts', 'required'),
            array('voter_group_id, user_id', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, voter_group_id, user_id, created_ts', 'safe', 'on'=>'search'),
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
            'userProfile' => array(self::BELONGS_TO, 'Profile', 'user_id'),
            'voterGroup' => array(self::BELONGS_TO, 'VoterGroup', 'voter_group_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'voter_group_id' => 'Voter Group',
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
        $criteria->compare('voter_group_id',$this->voter_group_id);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('created_ts',$this->created_ts,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}