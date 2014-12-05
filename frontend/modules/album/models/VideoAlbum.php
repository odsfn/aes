<?php

class VideoAlbum extends Album
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
        return 'video_album';
    }
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[5]['className'] = 'Video';
        
        return $rules;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'files' => array(self::HAS_MANY, 'Video', array('album_id' => 'id')),
            'cover'=> array(self::HAS_ONE, 'Video', array('id' => 'cover_id'))
        );
    }    
    
    public static function getAvailableAlbumsCriteria($target_id, $user_id = null, $tableName = null)
    {
        return parent::getAvailableAlbumsCriteria($target_id, $user_id, self::model()->tableName());
    }
}
