<?php

/**
 * This is the model class for table "album".
 *
 * The followings are the available columns in table 'album':
 * @property integer $id
 * @property integer $target_id
 * @property string $name
 * @property string $description
 * @property integer $permission
 * @property string $created Created date
 * @property string $update Updated date
 * @property File[] $files
 * @property File $cover
 */
class Album extends CActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Album the static model class
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
        return 'album';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, target_id', 'required'),
            array('id, permission, target_id, cover_id', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 255),
            array('description', 'length', 'max' => 255),
            array('user_id, update, permission', 'safe'),
            array('cover_id', 'exist', 'className'=>'File', 'attributeName'=>'id'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, name, description, permission', 'safe', 'on' => 'search'),
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
            'files' => array(self::HAS_MANY, 'File', array('album_id' => 'id')),
            'cover'=> array(self::HAS_ONE, 'File', array('id' => 'cover_id'))
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
            'name' => 'Альбом',
            'description' => 'Description',
            'permission' => 'Permission',
        );
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), array(
            'attrsChangeHandler' => array(
                'class' => 'AttrsChangeHandlerBehavior',
                'track' => array('cover_id')
            ),
            'UpdateDateBehavior' => array(
                'class' => 'UpdateDateBehavior',
                'fields' => array(
                    'create'=> array('created'),
                    'update'=> array('update')
                )
            )
        ));
    }

    protected function beforeSave()
    {
        parent::beforeSave();
        if ($this->isNewRecord) {
            $this->user_id = Yii::app()->user->id;
        }

        return true;
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

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('permission', $this->permission);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getRecords($condition, $params = array(), $page = 1, $limit = 30)
    {

        $criteria = new CDbCriteria;
        $criteria->condition = $condition;
        $criteria->params = $params;
        $criteria->limit = ($page ? $page * $limit : $limit);
        //$criteria->offset = $page * $limit;
        $criteria->order = '`update` DESC';
        return self::model()->findAll($criteria);
    }

    public function getAlbums($Album)
    {
        $criteria = new CDbCriteria;
        $criteria->condition = 'user_id = :user_id';
        $criteria->params = array(':user_id' => $Album->user_id);
        return CHtml::listData(self::model()->findAll($criteria), 'id', 'name');
    }

    public function isCover(File $image)
    {
        if($image->album_id != $this->id || !$this->cover)
            return false;
        
        return $this->cover->id == $image->id;
    }
    
    /**
     * Checks whether $image can be cover of the album
     * @param File $image Potential new cover
     * @return boolean
     */
    public function acceptsCover(File $image)
    {
        if($image->album_id != $this->id)
            return false;
        
        return !$this->isCover($image);
    }
    
    public function getCoverUrl()
    {
        $module = Yii::app()->getModule('album');
        if ($this->cover) 
            $path = $module->getComponent('image')->createAbsoluteUrl('360x220', $this->cover->path);
        else
            $path = $module->getAssetsUrl('img/no_album.png');
        
        return $path;
    }
    
    public static function checkCoverAcceptance($albumId, $image)
    {
        $album = self::model()->findByPk($albumId);
        
        if(!$album)
            return false;
        
        return $album->acceptsCover($image);
    }
    
    public static function getAvailableAlbumsCriteria($target_id, $user_id = null)
    {
        if (!$user_id)
            $user_id = (!Yii::app()->user->isGuest ? Yii::app()->user->id : 0);
        
        $params = $condition = array();
                   
        $condition[] = 't.target_id = :target_id';
        $params[':target_id'] = $target_id;

        // !Доступно только мне
        $condition[] = 't.id NOT IN (SELECT id FROM `album` f WHERE f.user_id <> 0 AND f.user_id <> :user_id AND f.permission = :perm2)';
        $params[':user_id'] = $user_id;
        $params[':perm2'] = AlbumModule::GALLERY_PERM_PER_OWNER;

        if (!$user_id) {
            // !Доступно только зарегестрированным
            $condition[] = 't.id NOT IN (SELECT id FROM `album` f WHERE f.permission = :perm1)';
            $params[':perm1'] = AlbumModule::GALLERY_PERM_PER_REGISTERED;
        }
        
        return new CDbCriteria(array(
            'condition' => implode(' AND ', $condition),
            'params' => $params
        ));
    }
    
    public function afterStoredAttrChanged_cover_id($currentValue, $oldValue, $attrName)
    {
        $this->createThumbnail();
    }
    
    public function afterInsert()
    {
        if ($this->cover)
            $this->createThumbnail();
    }

    protected function createThumbnail()
    {
        if(!$this->cover)
            return false;
        
        Yii::app()->getModule('album')->getComponent('image')->createPath('360x220', $this->cover->path);
    }
    
    public function photosUpdated()
    {
        $this->updateCover(false);
        $this->save();
    }
    
    /**
     * Automatically sets cover if it is not specified. Cover will be the last 
     * image in the album
     */
    public function updateCover($save = true)
    {
        if ($this->getRelated('cover', true))
            return;
        
        $result = $this->files(array(
            'order' => 'id DESC',
            'limit' => 1
        ));
        
        if (count($result)) {
            $this->cover_id = $result[0]->id;
        }
        
        if ($save)
            $this->save();
    }
}
