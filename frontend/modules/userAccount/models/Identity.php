<?php

/**
 * This is the model class for table "user_identity".
 *
 * The followings are the available columns in table 'user_identity':
 * @property integer $user_id
 * @property string $identity
 * @property string $type
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property UserAccount $userAccount
 * @property UserIdentityConfirmation $userIdentityConfirmation
 */
class Identity extends CActiveRecord {

    const STATUS_NEED_CONFIRMATION = 1;

    const STATUS_CONFIRMED = 2;
    
    const TYPE_EMAIL = 'email';
    
    const TYPE_PHONE = 'phone';

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Identity the static model class
     */
    public static function model($className = __CLASS__) {
	return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
	return 'user_identity';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
	$rules = array(
	    array('identity, type', 'required'),
	    array('status', 'numerical', 'integerOnly' => true),
	    array('identity, type', 'length', 'max' => 128),
	    
	    array('identity', 'unique'),
	    // The following rule is used by search().
	    // Please remove those attributes that should not be searched.
	    array('user_id, identity, type, status', 'safe', 'on' => 'search'),
	);
	
	//Additional rules for particular types. Can do this also by creating subclass
	if($this->type == self::TYPE_EMAIL){
	    $rules = array_merge($rules, array(
		array('identity', 'email'),
	    ));
	}
	
	return $rules;
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
	// NOTE: you may need to adjust the relation name and the related
	// class name for the relations automatically generated below.
	return array(
	    'userAccount' => array(self::BELONGS_TO, 'UserAccount', 'user_id'),
	    'userIdentityConfirmation' => array(self::HAS_ONE, 'UserIdentityConfirmation', 'user_identity_id'),
	);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
	$attributeLabels = array(
	    'user_id' => 'User',
	    'identity' => 'Identity',
	    'type' => 'Type',
	    'status' => 'Status',
	);
	
	//Additional rules for particular types. Can do this also by creating subclass
	if($this->type == self::TYPE_EMAIL){
	    $attributeLabels['identity'] = 'Email';
	}
	
	return $attributeLabels;
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
	// Warning: Please modify the following code to remove attributes that
	// should not be searched.

	$criteria = new CDbCriteria;

	$criteria->compare('user_id', $this->user_id);
	$criteria->compare('identity', $this->identity, true);
	$criteria->compare('type', $this->type, true);
	$criteria->compare('status', $this->status);

	return new CActiveDataProvider($this, array(
	    'criteria' => $criteria,
	));
    }

    /**
     * Updates status, generates confirmation row
     * @return IdentityConfirmation 
     */
    public function startConfirmation($type = IdentityConfirmation::TYPE_ACTIVATION_EMAIL){
	$identityConfirmation = new IdentityConfirmation;
	$identityConfirmation->user_identity_id = $this->id;
	$identityConfirmation->sent_ts = date('Y-m-d H:i:s');
	$identityConfirmation->type = $type;
	$identityConfirmation->key = md5($identityConfirmation->type . $this->identity . microtime());
	$identityConfirmation->save();
	return $identityConfirmation;
    }
}