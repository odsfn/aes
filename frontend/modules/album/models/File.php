<?php

/**
 * This is the model class for table "file".
 *
 * The followings are the available columns in table 'file':
 * @property integer $id
 * @property integer $user_id
 * @property integer $album_id
 * @property string $filename
 * @property string $path
 * @property string $type
 */
class File extends CActiveRecord
{
    public $tags;

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
            array('filename, path', 'required'),
            array('id, user_id, album_id, permission', 'numerical', 'integerOnly' => true),
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
        $this->update = date('Y-m-d H:i:s');
        $this->Taggable->setTags($this->tags);
        return true;
    }

    protected function afterDelete()
    {
        if ($this->album->path == $this->path) {
            $this->album->path = null;
            $this->album->save();
        }
        
        parent::afterDelete();
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

    public function getRecords($condition, $params = array(), $page = 1, $limit = 30, $order = '')
    {

        $criteria = new CDbCriteria;
        $criteria->condition = $condition;
        $criteria->params = $params;
        $criteria->limit = ($page ? $page * $limit : $limit);
        //$criteria->offset = $page * $limit;
        $criteria->order = $order;
        //$criteria->with = $this->with;
        return self::model()->findAll($criteria);
    }

    /* public function getTags(){
      return array();
      } */

    public function behaviors()
    {
        return array_merge(
                array(
                    'Taggable' => array(
                        'class' => 'album.components.taggableBehavior.EARTaggableBehavior',
                        'tagTable' => 'tags',
                        'tagModel' => 'Tags',
                        'tagBindingTable' => 'file_tag',
                        'modelTableFk' => 'fid',
                        'tagTablePk' => 'id',
                        'tagTableName' => 'name',
                        'tagTableCount' => 'frequency',
                        'tagBindingTableTagId' => 'tid',
                        'cacheID' => 'cache',
                        'createTagsAutomatically' => true,
                        'scope' => array(),
                        'insertValues' => array(),
                    ),
                ), parent::behaviors()
        );
    }

}
