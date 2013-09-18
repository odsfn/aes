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
 * 
 * The followings are the available model relations:
 * @property UserAccount $user
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class Profile extends CActiveRecord {
    
    const GENDER_NOT_SET = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    
    public $uploadingPhoto;
    
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
	    
	    array('birth_day', 'date', 'format'=>'yyyy-MM-dd'),
	    
	    array('birthDayFormated', 'safe', 'on' => 'edit, registration'),
	    // @TODO: брать значение формата из настроек локали ( CLocale ), формат
	    // должен соответствовать формату виртуального атрибуты birthDayFormated 
	    array('birthDayFormated', 'date', 'format'=>'MM/dd/yyyy'),
	    
	    array('uploadingPhoto', 'file', 'types' => implode(',', $module->photoExtensions), 'safe' => true, 'maxSize' => $module->photoMaxSize, 'allowEmpty'=>true),
	    
	    array('email', 'unique', 'attributeName'=>'identity', 'className'=>'Identity', 'on'=>'registration'),
	    array('mobile_phone', 'unique', 'attributeName'=>'mobile_phone', 'className'=>'Profile', 'allowEmpty'=>true),
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
    
    public function beforeSave() {
	$uploadingPhoto = CUploadedFile::getInstance($this, 'uploadingPhoto');
	
	if($uploadingPhoto) {
	    $this->changePhoto($uploadingPhoto);
	}
	
	return parent::beforeSave();
    }
    
    public function getUsername(){
	return $this->first_name . ' ' . $this->last_name;
    }
    
    public function getBirthDayFormated() {
	if($this->birth_day && $this->birth_day != '0000-00-00') {
//	    @TODO: Переключится на эту реализацию, когда будем воплощать http://tstdomain.com/jira/browse/AISVII-34 
//	    return Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($this->birth_day, 'yyyy-MM-dd'), 'short', null));
	    
	    $date = new DateTime($this->birth_day);
	    return $date->format('m/d/Y');
	} else {
	    return '';
	}
    }
    
    public function setBirthDayFormated($value) {
	$date = new DateTime($value);
	$this->birth_day = $date->format('Y-m-d');
    }
    
    public function getDisplayGender() {
	
	$genders = array(
	    self::GENDER_NOT_SET => 'Not set',
	    self::GENDER_MALE => 'Man',
	    self::GENDER_FEMALE => 'Woman',
	);
	
	return Yii::t('common', $genders[$this->gender]);
    }
    
    /**
     * Provides link to the user's photo with specified size. Makes resizing if
     * needed
     * @param int $size
     * @return string	Path to the user's photo
     * @throws CException
     */
    public function getPhoto($width = 64, $height = null)
    {
        $width = intval($width);
        $width || ($width = 32);

	if(!$height) {
	    $height = $width;
	}
	
	$photosDir = Yii::app()->getModule('userAccount')->photosDir;
	$basePath   = Yii::app()->basePath . '/www' . $photosDir . '/';
	
        if($this->photo)
            $image = $this->photo;
        else
            $image = Yii::app()->getModule('userAccount')->defaultPhoto;
        
        if ($image)
        {
            $sizedFile  = str_replace('.', '_' . $width . 'x' . $height . '.', $image);

            // Checks whather photo with specified size already exists
            if (file_exists($basePath . $sizedFile))
                return Yii::app()->getBaseUrl(true) . $photosDir . "/" . $sizedFile;
            
            if($this->resizePhoto($basePath . $image, $basePath . $sizedFile, $width, $height))
                return Yii::app()->getBaseUrl(true) . $photosDir . "/" . $sizedFile;
        }
    }
    
    public function getPhotoThmbnl64() {
        if(!$this->photo)
            return '';
        
        if(!$this->photo_thmbnl_64) {
            $this->createPhotoThumbnail(64);
            $this->save(false, array('photo_thmbnl_64'));
        }
                
        return Yii::app()->getBaseUrl(true) . Yii::app()->getModule('userAccount')->photosDir . "/" . $this->photo_thmbnl_64;
    }
    
    public function getPageUrl() {
        return Yii::app()->createAbsoluteUrl('userPage', array('id'=>$this->user_id));
    }
    
    /**
     * Sets up new photo
     * @param CUploadedFile $uploadedFile
     */
    public function changePhoto(CUploadedFile $uploadedFile) {
        $photosDir = Yii::app()->getModule('userAccount')->photosDir;

        $basePath   = Yii::app()->basePath . '/www' . $photosDir ;

        //создаем каталог, если не существует
        if(!file_exists($basePath)) {
            mkdir($basePath);
        }

        $basePath .= '/';

        $filename = $this->user_id . '_' . str_replace( array('.', ' '), '_', microtime() ) . '.' . $uploadedFile->extensionName;

        if($this->photo) {
            //remove old resized photos
            if(file_exists($basePath . $this->photo))
                unlink($basePath . $this->photo);

            foreach (glob($basePath . $this->user_id . '_*.*') as $oldThumbnail) {
                unlink($oldThumbnail);
            }
        }

        $uploadedFile->saveAs($basePath . $filename);

        $this->photo = $filename;
        
        $this->createPhotoThumbnail(64);
    }
    
    public function createPhotoThumbnail($size) {
        $photosDir = Yii::app()->getModule('userAccount')->photosDir;
	$basePath   = Yii::app()->basePath . '/www' . $photosDir . '/';
	
        $width = $height = $size;
        
        if ($this->photo)
        {
            $sizedFile  = str_replace('.', '_' . $width . 'x' . $height . '.', $this->photo);

            if($this->resizePhoto($basePath . $this->photo, $basePath . $sizedFile, $width, $height))
                $this->photo_thmbnl_64 = $sizedFile;
        }
    }
    
    /**
     * Helper function for resizing photos
     * 
     * @TODO: replace it to some image helper
     * 
     * @param string $inputFile Path to the source image
     * @param string $outputFile Path to the destination image
     * @param int $width
     * @param int $height
     * @return boolean
     */
    protected function resizePhoto($inputFile, $outputFile, $width, $height) {
        if (file_exists($inputFile))
        {
            $image = Yii::app()->image->load($inputFile);
            if ($image->ext != 'gif' || $image->config['driver'] == "ImageMagick")
                $image->resize($width, $height, CImage::WIDTH)
                      ->crop($width, $height)
                      ->quality(85)
                      ->sharpen(15)
                      ->save($outputFile);
            else
                @copy($inputFile, $outputFile);
            
            return true;
        }
        
        return false;
    }
    
    public function getAttributes($names = true) {
        $attrs = parent::getAttributes($names);
        $attrs['displayName'] = $this->username;
        $attrs['photoThmbnl64'] = $this->getPhotoThmbnl64();
        $attrs['pageUrl'] = $this->pageUrl;
        return $attrs;
    }
}