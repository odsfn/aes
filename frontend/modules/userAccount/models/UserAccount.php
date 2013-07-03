<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property string $created_ts
 * @property string $last_visit_ts
 * @property integer $superuser
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property UserIdentity $userIdentity
 * @property UserProfile $userProfile
 */
class UserAccount extends CActiveRecord {

    const STATUS_NEED_ACTIVATION = 1;
    
    const STATUS_ACTIVE = 2;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return UserAccount the static model class
     */
    public static function model($className = __CLASS__) {
	return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
	return 'user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
	// NOTE: you should only define rules for those attributes that
	// will receive user inputs.
	return array(
	    array('superuser, status', 'numerical', 'integerOnly' => true),
	    array('login', 'length', 'max' => 64),
	    array('password', 'length', 'max' => 128),
	    array('created_ts', 'safe'),
	    // The following rule is used by search().
	    // Please remove those attributes that should not be searched.
	    array('id, login, password, created_ts, last_visit_ts, superuser, status', 'safe', 'on' => 'search'),
	);
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
	// NOTE: you may need to adjust the relation name and the related
	// class name for the relations automatically generated below.
	return array(
	    'identities' => array(self::HAS_MANY, 'Identity', 'user_id'),
	    'profile' => array(self::HAS_ONE, 'Profile', 'user_id'),
	);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
	return array(
	    'id' => 'ID',
	    'login' => 'Login',
	    'password' => 'Password',
	    'created_ts' => 'Created Ts',
	    'last_visit_ts' => 'Last Visit Ts',
	    'superuser' => 'Superuser',
	    'status' => 'Status',
	);
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
	// Warning: Please modify the following code to remove attributes that
	// should not be searched.

	$criteria = new CDbCriteria;

	$criteria->compare('id', $this->id);
	$criteria->compare('login', $this->login, true);
	$criteria->compare('password', $this->password, true);
	$criteria->compare('created_ts', $this->created_ts, true);
	$criteria->compare('last_visit_ts', $this->last_visit_ts, true);
	$criteria->compare('superuser', $this->superuser);
	$criteria->compare('status', $this->status);

	return new CActiveDataProvider($this, array(
	    'criteria' => $criteria,
	));
    }

    public function beforeSave() {
	if($this->isNewRecord && !$this->created_ts){
	    $this->created_ts = date('Y-m-d H:i:s');
	    $this->last_visit_ts = '0000-00-00 00:00:00';
	}
	
	return parent::beforeSave();
    }
    
    public function setPassword($password){
	$this->password = $this->encryptPassword($password);
    }
    
    /**
     * Checks whether password property is equals to provided password
     * @param string $password
     */
    public function passwordEquals($password){
	return $this->password === $this->encryptPassword($password);
    }
    
    /**
     * @param string $password Password to encrypt
     * @return string Encrypted password
     */
    public function encryptPassword($password){
	return md5($password);
    }
    
    /**
     * @returns string Active email
     */
    public function getActiveEmail(){
	$identity = Identity::model()->findByAttributes(array(
	   'status'=>Identity::STATUS_CONFIRMED,
	   'type'=>Identity::TYPE_EMAIL,
	   'user_id'=>$this->id
	));
	
	return (!$identity) ? null : $identity->identity;
    }
}