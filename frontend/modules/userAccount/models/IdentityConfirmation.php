<?php

/**
 * This is the model class for table "user_identity_confirmation".
 *
 * The followings are the available columns in table 'user_identity_confirmation':
 * @property integer $user_identity_id
 * @property integer $type
 * @property string $key
 * @property string $sent_ts
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property UserIdentity $userIdentity
 */
class IdentityConfirmation extends CActiveRecord {

    const TYPE_ACTIVATION_EMAIL = 1;
    
    const TYPE_ACTIVATION_PHONE = 2;
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return IdentityConfirmation the static model class
     */
    public static function model($className = __CLASS__) {
	return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
	return 'user_identity_confirmation';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
	// NOTE: you should only define rules for those attributes that
	// will receive user inputs.
	return array(
	    array('key', 'required'),
	    array('type, status', 'numerical', 'integerOnly' => true),
	    array('key', 'length', 'max' => 128),
	    array('sent_ts', 'safe'),
	);
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
	// NOTE: you may need to adjust the relation name and the related
	// class name for the relations automatically generated below.
	return array(
	    'identity' => array(self::BELONGS_TO, 'Identity', 'user_identity_id'),
	);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
	return array(
	    'user_identity_id' => 'User Identity',
	    'type' => 'Type',
	    'key' => 'Key',
	    'sent_ts' => 'Sent Ts',
	    'status' => 'Status',
	);
    }
    
    public function confirm(){
	$this->identity->status = Identity::STATUS_CONFIRMED;
	$this->identity->save();
	
	$userAccount = $this->identity->userAccount; 
	$userAccount->status = UserAccount::STATUS_ACTIVE;
	$userAccount->save();
	
	$this->delete();
    }
}