<?php

/**
 * This is the model class for table "album".
 *
 * The followings are the available columns in table 'album':
 * @property integer $id
 * @property integer $target_id
 * @property string $name
 * @property string $description
 * @property string $path
 * @property integer $permission
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
            array('id, permission, target_id', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 255),
            array('description', 'length', 'max' => 255),
            array('user_id, path, update, permission', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, name, description, path, permission', 'safe', 'on' => 'search'),
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
            'file' => array(self::HAS_MANY, 'File', array('album_id' => 'id')),
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
            'path' => 'Default path',
            'permission' => 'Permission',
        );
    }

    protected function beforeSave()
    {
        parent::beforeSave();
        if ($this->isNewRecord) {
            $this->user_id = Yii::app()->user->id;
        }
        $this->update = date('Y-m-d H:i:s');
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
        $criteria->compare('path', $this->path);
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
        if($image->album_id != $this->id)
            return false;
        
        return $this->path === $image->path;
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
    
    public static function checkCoverAcceptance($albumId, $image)
    {
        $album = self::model()->findByPk($albumId);
        
        if(!$album)
            return false;
        
        return $album->acceptsCover($image);
    }
}
