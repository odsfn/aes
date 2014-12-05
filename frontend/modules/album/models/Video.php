<?php
use TubeLink\Exception\ServiceNotFoundException;

class Video extends File
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return File the static model class
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
        return 'video';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('url, target_id', 'required'),
            array('id, user_id, album_id, permission, target_id', 'numerical', 'integerOnly' => true),
            array('updated, user_id, description, permission', 'safe'),
            array('url', 'validateUrl'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('uid, user_id, album_id, filename, path, permission', 'safe', 'on' => 'search'),
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
            'album' => array(self::BELONGS_TO, 'VideoAlbum', array('album_id' => 'id')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'target_id' => 'Target Id',
            'user_id' => 'User',
            'album_id' => 'Album',
            'url' => 'Url видео',
            'path' => 'Путь к thumbnail',
            'description' => 'Описание'
        );
    }
    
    public function validateUrl($attribute, $params)
    {
        try {
            $tube = Yii::app()->getComponent('tubeLink')->parse($this->url);
        } catch(ServiceNotFoundException $e) {
            $this->addError($attribute, 'Video can\'t be located on the service');
            return;
        }
    }

    public function show()
    {
        $tube = Yii::app()->getComponent('tubeLink')->parse($this->url);
        $embed = $tube->render(array('allowfullscreen'=>'allowfullscreen'));
        $embed = preg_replace('~(width|height)="\d+"~', '', $embed);
        
        return '<div class="video-container">' . $embed . '</div>';
    }

    public function afterInsert()
    {
        $this->preparePreview();
        parent::afterInsert();
    }

    public static function getAvailablePhotosCriteria($withoutAlbums = false, $target_id, $user_id = null, $album = null, $tableName = null)
    {
        return parent::getAvailablePhotosCriteria($withoutAlbums, $target_id,
            $user_id, $album, self::model()->tableName()
        );
    }
    
    protected function preparePreview()
    {
        $tube = Yii::app()->getComponent('tubeLink')->parse($this->url);
        $thumbnailUrl = $tube->thumbnail();
        
        $matches = array();
        if( preg_match('~\.(png|jpg|jpeg)$~', $thumbnailUrl, $matches) !== FALSE )
            $ext = $matches[1];
        else
            throw new CException('Unexpected thumbnail preview extension "' . $ext . '", in thumbnail url: "' . $thumbnailUrl . '"');

        $thumbsDir = Yii::getPathOfAlias('webroot.uploads.album.original');
        
        $image = Yii::app()->getModule('album')->getComponent('image');
        
        $fileName = $image->prepareUniqueFileName('', $ext);
        $filePath = $thumbsDir . DIRECTORY_SEPARATOR . $fileName; 
        
        $ch = curl_init($thumbnailUrl);
        $fp = fopen($filePath, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        
        $image->createPath('160x100', $filePath, false);
        
        $this->setIsNewRecord(false);
        $this->saveAttributes(array(
            'path' => str_replace(Yii::getPathOfAlias('webroot') . DIRECTORY_SEPARATOR, '', $filePath)
        ));
    }
}

