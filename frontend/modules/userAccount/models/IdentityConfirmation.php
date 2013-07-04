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
     * Confirmation for replacing activated email by new one
     */
    const TYPE_EMAIL_REPLACE_CONFIRMATION = 3;
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
    
    /**
     * Calls before changing status of the confirmation
     * @return boolean	Whether to proceed confirmation
     */
    protected function beforeConfirm(){
	return true;
    }

    /**
     * @todo Move code of this method to RegistrationConfirmation
     */
    protected function afterConfirm(){
	$userAccount = $this->identity->userAccount; 
	$userAccount->status = UserAccount::STATUS_ACTIVE;
	$userAccount->save();
    }

    public function confirm(){
	
	$this->beforeConfirm();
	
	$this->identity->status = Identity::STATUS_CONFIRMED;
	$this->identity->save();
	
	$this->afterConfirm();
	
	$this->delete();
	
	return true;
    }
    
    /**
     * Instantiates particular subclass of IdentityConfirmation based on it's type
     * @todo:
     * - Replace this method by overriding afterFind method, where we can attach corresponding behaviour
     * @param type $id
     */
    public static function create($id){
	
	$particularConfirmation = null;
	
	if(is_string($id)){ //creating by key
	    
	    $confirmation = IdentityConfirmation::model()->findByAttributes(array('key'=>$id));
	    if(!$confirmation){
		return null;
	    }
	    
	    if($confirmation->type == IdentityConfirmation::TYPE_EMAIL_REPLACE_CONFIRMATION){
		$particularConfirmation = new ReplaceEmailConfirmation;
		$particularConfirmation->setAttributes($confirmation->attributes, false);
		$particularConfirmation->isNewRecord = false;
	    }else{
		$particularConfirmation = $confirmation;
	    }
	}
	
	return $particularConfirmation;
    }
}