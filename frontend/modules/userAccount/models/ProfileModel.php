<?php
/**
 * This is the model class for table "user_profile".
 *
 * The followings are the available columns in table 'user_profile':
 * @property integer $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $birth_place
 * @property string $birth_day
 * @property integer $gender
 * @property string $mobile_phone
 * @property string $email
 * 
 * The followings are the available model relations:
 * @property UserAccount $user
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ProfileModel extends CModel {

    public $user_id;
    
    public $first_name;
    
    public $last_name;
    
    public $birth_place;
    
    public $birth_day;
    
    public $mobile_phone;

    public $email;
    
    public $gender;
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
	// NOTE: you should only define rules for those attributes that
	// will receive user inputs.
	return array(
	    array('email, gender, first_name, last_name, birth_place, birth_day', 'required'),
	    array('first_name, last_name', 'match', 'pattern'=>'/^[[:alpha:]]{2,}$/u'),
	    array('email', 'email'),
	    array('gender', 'numerical', 'integerOnly' => true),
	    array('first_name, last_name, birth_place, email', 'length', 'max' => 128),
	    array('mobile_phone', 'length', 'max' => 18),
	    array('birth_day', 'date'),
	    
	    array('email', 'unique', 'attributeName'=>'identity', 'className'=>'Identity'),
	    array('mobile_phone', 'unique', 'attributeName'=>'mobile_phone', 'className'=>'Profile', 'allowEmpty'=>true),
	    // The following rule is used by search().
	    // Please remove those attributes that should not be searched.
	    array('user_id, first_name, last_name, birth_place, birth_day, gender, mobile_phone, email', 'safe', 'on' => 'search'),
	);
    }

    public function attributeNames() {
	return array('user_id', 'first_name', 'last_name', 'birth_place', 'birth_day', 'gender', 'mobile_phone', 'email');
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
	return array(
	    'user_id' => 'User',
	    'first_name' => 'First Name',
	    'last_name' => 'Last Name',
	    'birth_place' => 'Birth Place',
	    'birth_day' => 'Birth Day',
	    'gender' => 'Gender',
	    'mobile_phone' => 'Mobile Phone',
	    'email' => 'Email',
	);
    }
}