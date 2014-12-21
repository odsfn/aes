 <?php
Yii::import('album.components.iGalleryItem');
Yii::import('album.components.iDownloadable');
Yii::import('album.models.File');
Yii::import('album.models.Video');
/**
 * This is the model class for table "video_comment_rate".
 *
 * The followings are the available columns in table 'video_comment_rate':
 * @property integer $id
 * @property integer $user_id
 * @property integer $target_id
 * @property string $created_ts
 * @property integer $score
 *
 * The followings are the available model relations:
 * @property VideoComment $target
 * @property UserProfile $user
 */
class VideoCommentRate extends Rate
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ElectionCommentRate the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getRateableEntity() {
        return 'VideoComment';
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'video_comment_rate';
    }

}