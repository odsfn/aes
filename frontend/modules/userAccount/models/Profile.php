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
 * @property string $birthDayFormated Representation of birth_day cell in current time format
 * @property string $photo_thmbnl_64 Filename with photo thumbnail
 * @property integer $target_id Id from parent table
 * 
 * The followings are the available model relations:
 * @property UserAccount $user
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class Profile extends CActiveRecord
{
    const GENDER_NOT_SET = 0;

    const GENDER_MALE = 1;

    const GENDER_FEMALE = 2;

    public $uploadingPhoto;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Profile the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user_profile';
    }

    public function behaviors()
    {
        return array(
            'childTable' => array(
                'class' => 'common.components.ChildTableBehavior',
                'parentTable' => 'target',
                'parentTablePk' => 'target_id',
                'childConstraint' => 'target_id'
            ),
            
            'uploadingImage' => array(
                'class' => 'common.components.UploadingImageBehavior',
                'uploadingImageAttr' => 'uploadingPhoto',
                'imagePathAttr' => 'photo',
                'idAttr' => 'user_id',
                'imagesDir' => Yii::app()->getModule('userAccount')->photosDir,
                'defaultImage' => Yii::app()->getModule('userAccount')->defaultPhoto,
                'thumbnailsToCreate' => array(64, 96)
            )
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $module = Yii::app()->getModule('userAccount');

        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('email, gender, first_name, last_name, birth_place, birth_day', 'required'),
//	    array('first_name, last_name', 'match', 'pattern'=>'/^[:alpha:]{2,}$/u'),
            array('email', 'email'),
            array('gender', 'numerical', 'integerOnly' => true),
            array('first_name, last_name, birth_place, email', 'length', 'max' => 128),
            array('mobile_phone', 'length', 'max' => 18),
            array('birth_day', 'date', 'format' => 'yyyy-MM-dd'),
            array('birthDayFormated', 'safe', 'on' => 'edit, registration'),
            // @TODO: брать значение формата из настроек локали ( CLocale ), формат
            // должен соответствовать формату виртуального атрибуты birthDayFormated 
            array('birthDayFormated', 'date', 'format' => 'MM/dd/yyyy'),
            array('uploadingPhoto', 'file', 'types' => implode(',', $module->photoExtensions), 'safe' => true, 'maxSize' => $module->photoMaxSize, 'allowEmpty' => true),
            array('email', 'unique', 'attributeName' => 'identity', 'className' => 'Identity', 'on' => 'registration'),
            array('mobile_phone', 'unique', 'attributeName' => 'mobile_phone', 'className' => 'Profile', 'allowEmpty' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('user_id, first_name, last_name, birth_place, birth_day, gender, mobile_phone, email', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        $customRelations = Yii::app()->getModule('userAccount')->profileCustomRelations;
        
        $relations = array(
            'user' => array(self::BELONGS_TO, 'UserAccount', 'user_id'),
        );
        
        return CMap::mergeArray($relations, $customRelations);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'user_id' => 'User',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'birth_place' => 'Birth Place',
            'birth_day' => 'Birth Day',
            'birthDayFormated' => 'Birth Day',
            'gender' => 'Gender',
            'mobile_phone' => 'Mobile Phone',
            'email' => 'Email',
            'displayGender' => 'Gender',
            'uploadingPhoto' => 'Your photo'
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

    public function scopes()
    {
        return array(
            'activeOnly' => array(
                'join' => 'INNER JOIN user ON user.id = t.user_id AND user.status = ' . UserAccount::STATUS_ACTIVE
            )
        );
    }

    public function getUsername()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getBirthDayFormated()
    {
        if ($this->birth_day && $this->birth_day != '0000-00-00') {
//	    @TODO: Переключится на эту реализацию, когда будем воплощать http://tstdomain.com/jira/browse/AISVII-34 
//	    return Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($this->birth_day, 'yyyy-MM-dd'), 'short', null));

            $date = new DateTime($this->birth_day);
            return $date->format('m/d/Y');
        } else {
            return '';
        }
    }

    public function setBirthDayFormated($value)
    {
        $date = new DateTime($value);
        $this->birth_day = $date->format('Y-m-d');
    }

    public function getDisplayGender()
    {

        $genders = array(
            self::GENDER_NOT_SET => 'Not set',
            self::GENDER_MALE => 'Man',
            self::GENDER_FEMALE => 'Woman',
        );

        return Yii::t('common', $genders[$this->gender]);
    }

    
    public function getPhoto($width = 64, $height = null)
    {
        return $this->getImage($width, $height);
    }

    public function getPhotoThmbnl64()
    {
        if (!$this->photo)
            return '';

        return Yii::app()->getBaseUrl(true) . Yii::app()->getModule('userAccount')->photosDir . "/" . $this->photo_thmbnl_64;
    }

    public function getPageUrl()
    {
        return Yii::app()->createAbsoluteUrl('userPage', array('id' => $this->user_id));
    }

    public function getAttributes($names = true)
    {
        $attrs = parent::getAttributes($names);
        $attrs['displayName'] = $this->username;
        $attrs['photoThmbnl64'] = $this->getPhotoThmbnl64();
        $attrs['pageUrl'] = $this->pageUrl;
        return $attrs;
    }

    public static function getAvailableGenders()
    {
        return array(self::GENDER_MALE, self::GENDER_FEMALE);
    }

}
