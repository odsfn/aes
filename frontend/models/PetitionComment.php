<?php
/**
 * This is the model class for table "petition_comment".
 *
 * The followings are the available columns in table 'petition_comment':
 * @property integer $id
 * @property integer $target_id
 * @property integer $user_id
 * @property string $content
 * @property string $created_ts
 * @property string $last_update_ts
 *
 * The followings are the available model relations:
 * @property Petition $target
 * @property UserProfile $user
 * @property PetitionCommentRate[] $rates
 */
class PetitionComment extends Comment
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return PetitionComment the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getCommentableEntity() {
        return 'Petition';
    }
        
}