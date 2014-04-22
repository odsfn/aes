<?php
/**
 * This is the model class for table "petition_comment_rate".
 *
 * The followings are the available columns in table 'petition_comment_rate':
 * @property integer $id
 * @property integer $user_id
 * @property integer $target_id
 * @property string $created_ts
 * @property integer $score
 *
 * The followings are the available model relations:
 * @property PetitionComment $target
 * @property Profile $user
 */
class PetitionCommentRate extends Rate
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return PetitionCommentRate the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getRateableEntity() {
        return 'PetitionComment';
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'petition_comment_rate';
    }

}