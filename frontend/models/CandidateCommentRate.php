 <?php
/**
 * This is the model class for table "candidate_comment_rate".
 *
 * The followings are the available columns in table 'candidate_comment_rate':
 * @property integer $id
 * @property integer $user_id
 * @property integer $target_id
 * @property string $created_ts
 * @property integer $score
 *
 * The followings are the available model relations:
 * @property CandidateComment $target
 * @property UserProfile $user
 */
class CandidateCommentRate extends Rate
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CandidateCommentRate the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getRateableEntity() {
        return 'CandidateComment';
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'candidate_comment_rate';
    }

}