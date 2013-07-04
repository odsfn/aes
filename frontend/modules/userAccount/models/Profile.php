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
class Profile extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Profile the static model class
     */
    public static function model($className = __CLASS__) {
	return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
	return 'user_profile';
    }

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
	    array('birth_day', 'date', 'format'=>array('MM/dd/yyyy', 'yyyy-MM-dd')),
	    
	    array('email', 'unique', 'attributeName'=>'identity', 'className'=>'Identity', 'on'=>'registration'),
	    array('mobile_phone', 'unique', 'attributeName'=>'mobile_phone', 'className'=>'Profile', 'allowEmpty'=>true, 'on'=>'registration'),
	    // The following rule is used by search().
	    // Please remove those attributes that should not be searched.
	    array('user_id, first_name, last_name, birth_place, birth_day, gender, mobile_phone, email', 'safe', 'on' => 'search'),
	);
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
	// NOTE: you may need to adjust the relation name and the related
	// class name for the relations automatically generated below.
	return array(
	    'user' => array(self::BELONGS_TO, 'User', 'user_id'),
	);
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

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
	// Warning: Please modify the following code to remove attributes that
	// should not be searched.

	$criteria = new CDbCriteria;

	$criteria->compare('user_id', $this->user_id);
	$criteria->compare('first_name', $this->first_name, true);
	$criteria->compare('last_name', $this->last_name, true);
	$criteria->compare('birth_place', $this->birth_place, true);
	$criteria->compare('birth_day', $this->birth_day, true);
	$criteria->compare('gender', $this->gender);
	$criteria->compare('mobile_phone', $this->mobile_phone, true);
	$criteria->compare('email', $this->email, true);

	return new CActiveDataProvider($this, array(
	    'criteria' => $criteria,
	));
    }

    protected function beforeSave() {
	if($this->isNewRecord){
	    //format date
	    $date = new DateTime($this->birth_day);
	    $this->birth_day = $date->format('Y-m-d');
	}
	return parent::beforeSave();
    }
    
    public function getUsername(){
	return $this->first_name . ' ' . $this->last_name;
    }
}