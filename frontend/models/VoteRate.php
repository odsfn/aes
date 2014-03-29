<?php
/**
 * This is the model class for table "vote_rate".
 *
 * The followings are the available columns in table 'vote_rate':
 * @property integer $id
 * @property integer $user_id
 * @property integer $target_id
 * @property string $created_ts
 * @property integer $score
 *
 * The followings are the available model relations:
 * @property Vote $target
 * @property Profile $user
 */
class VoteRate extends Rate
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
        return 'Vote';
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'vote_rate';
    }

}