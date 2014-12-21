 <?php
Yii::import('album.components.iGalleryItem');
Yii::import('album.components.iDownloadable');
Yii::import('album.models.File');
Yii::import('album.models.Video');
/**
 * This is the model class for table "video_comment".
 *
 * The followings are the available columns in table 'video_comment':
 * @property integer $id
 * @property integer $target_id
 * @property integer $user_id
 * @property string $content
 * @property string $created_ts
 * @property string $last_update_ts
 *
 * The followings are the available model relations:
 * @property Video $target
 * @property UserProfile $user
 * @property VideoCommentRate[] $rates
 */
class VideoComment extends Comment
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CandidateComment the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getCommentableEntity() {
        return 'Video';
    }
        
}