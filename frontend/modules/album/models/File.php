<?php

/**
 * This is the model class for table "file".
 *
 * The followings are the available columns in table 'file':
 * @property integer $id
 * @property integer $target_id
 * @property integer $user_id
 * @property integer $album_id
 * @property integer $permission
 * @property string $created Created date
 * @property string $update Updated date
 * @property string $filename
 * @property string $path
 * @property string $type
 */
class File extends CActiveRecord implements iGalleryItem, iDownloadable
{

    public $tags;

    protected $coverShouldBeUpdated = false;

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
        return 'file';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('filename, path, target_id', 'required'),
            array('id, user_id, album_id, permission, target_id', 'numerical', 'integerOnly' => true),
            array('filename', 'length', 'max' => 50),
            array('filename, path', 'length', 'max' => 255),
            array('update, user_id, description, permission', 'safe'),
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
            'album' => array(self::BELONGS_TO, 'Album', array('album_id' => 'id')),
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
            'filename' => 'Filename',
            'path' => 'Path',
            'type' => 'Type',
            'description' => 'Описание',
        );
    }

    protected function beforeSave()
    {
        parent::beforeSave();
        if ($this->isNewRecord) {
            $this->user_id = Yii::app()->user->id;
        }
        
        $this->inheritAlbumPermissions();
        
//        $this->Taggable->setTags($this->tags);
        
        return true;
    }

    protected function beforeDelete()
    {
        $result = parent::beforeDelete();
        
        if (!$result)
            return false;
            
        if ($this->album && $this->album->isCover($this)) {
            $this->coverShouldBeUpdated = true;
        }
        
        return $result;
    }


    protected function afterDelete()
    {
        if ($this->coverShouldBeUpdated) {
            //Notify album about cover deletion
            $this->album->updateCover();
        }
        
        parent::afterDelete();
    }
    
    protected function inheritAlbumPermissions()
    {
        if ($this->album && $this->album->permission != $this->permission) {
            $this->permission = $this->album->permission;
        }
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
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('album_id', $this->album_id);
        $criteria->compare('filename', $this->filename, true);
        $criteria->compare('path', $this->path, true);
        $criteria->compare('type', $this->type, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function show()
    {
        $url = Yii::app()->getModule('album')->getComponent('image')->createAbsoluteUrl('1150x710', $this->path);
        return CHtml::image($url);
    }

    static function dateRange($first, $step = '-1 day', $limit = '-10 day')
    {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($limit, $current);

        while ($current >= $last) {

            //$dates[] = date( $format, $current );
            $dates[] = $current;
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    public function getPermissionLabel()
    {
        return AlbumModule::getPermissionLabel($this->permission);
    }
    
    public function getRecords($condition, $params = array(), $page = 1, $limit = 30, $order = '')
    {

        $criteria = new CDbCriteria;
        $criteria->condition = $condition;
        $criteria->params = $params;
        $criteria->limit = ($page ? $page * $limit : $limit);
        //$criteria->offset = $page * $limit;
        $criteria->order = $order;
        //$criteria->with = $this->with;
        return $this->model()->findAll($criteria);
    }

    public function getAbsolutePath()
    {
        return Yii::getPathOfAlias('webroot') . DIRECTORY_SEPARATOR . $this->path;
    }

    public function getPreviewUrl()
    {
        $module = Yii::app()->getModule('album');
        $url = $module->getAssetsUrl('img/no_album.png');
        if( $this->path ) 
            $url = $module->getComponent('image')->createAbsoluteUrl('160x100', $this->path);
        
        return $url;
    }
    
    public static function getAvailableCriteria($withoutAlbums = false, $target_id, $user_id = null, $album = null, $tableName = null)
    {
        if (!$user_id)
            $user_id = (!Yii::app()->user->isGuest ? Yii::app()->user->id : 0);
        
        $params = $condition = array();

        $condition[] = 't.target_id = :target_id';
        $params[':target_id'] = $target_id;

        if (!$tableName)
            $tableName = self::model()->tableName();
        
        // !Доступно только мне
        $condition[] = 't.id NOT IN (SELECT id FROM `' . $tableName . '` f WHERE f.user_id <> 0 AND f.user_id <> :user_id AND f.permission = :perm2)';
        $params[':user_id'] = $user_id;
        $params[':perm2'] = AlbumModule::GALLERY_PERM_PER_OWNER;

        if (!$user_id) {
            // !Доступно только зарегестрированным
            $condition[] = 't.id NOT IN (SELECT id FROM `' . $tableName . '` f WHERE f.permission = :perm1)';
            $params[':perm1'] = AlbumModule::GALLERY_PERM_PER_REGISTERED;
        }
        
        if(!$withoutAlbums && $album) {
            $condition[] = 'album_id = :album_id';
            $params[':album_id'] = $album;
        }
        
        $criteria = new CDbCriteria(array(
            'condition' => implode(' AND ', $condition),
            'params' => $params            
        ));
        
        if ($withoutAlbums)
            $criteria->addCondition('album_id IS NULL');
        
        return $criteria;
    }

    public function behaviors()
    {
        return array_merge(
                array(
//                    'Taggable' => array(
//                        'class' => 'album.components.taggableBehavior.EARTaggableBehavior',
//                        'tagTable' => 'tags',
//                        'tagModel' => 'Tags',
//                        'tagBindingTable' => 'file_tag',
//                        'modelTableFk' => 'fid',
//                        'tagTablePk' => 'id',
//                        'tagTableName' => 'name',
//                        'tagTableCount' => 'frequency',
//                        'tagBindingTableTagId' => 'tid',
//                        'cacheID' => 'cache',
//                        'createTagsAutomatically' => true,
//                        'scope' => array(),
//                        'insertValues' => array(),
//                    ),
                    'attrsChangeHandler' => array(
                        'class' => 'AttrsChangeHandlerBehavior',
                        'track' => array('album_id')
                    ),
                    'UpdateDateBehavior' => array(
                        'class' => 'UpdateDateBehavior',
                        'fields' => array(
                            'create'=> array('created'),
                            'update'=> array('update')
                        )
                    )
                ), parent::behaviors()
        );
    }

    public function afterStoredAttrChanged_album_id($currentValue, $oldValue, $attrName)
    {
        if ($this->album)
            $this->album->gitemsUpdated();
    }
    
    public function afterInsert()
    {
        if ($this->album)
            $this->album->gitemsUpdated();
    }
    
    public function canBeDownloaded()
    {
        return true;
    }
    
    public function getDownloadUrl()
    {
        return Yii::app()->getModule('album')
                ->getComponent('image')
                ->createAbsoluteUrl('original', $this->path);
    }
}
