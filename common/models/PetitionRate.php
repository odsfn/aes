<?php
/**
 * This is the model class for table "petition_rate". This rate displays how
 * much electors of mandate which related petition addressed support or not 
 * support last one.
 *
 * The followings are the available columns in table 'petition_rate':
 * @property integer $id
 * @property integer $user_id
 * @property integer $target_id
 * @property string $created_ts
 * @property integer $score
 *
 * The followings are the available model relations:
 * @property Petition $target
 * @property Profile $user
 */
class PetitionRate extends Rate
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
        return 'Petition';
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'petition_rate';
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array_merge(parent::rules(), array(
            array('user_id', 'validateCreatorIsAdherent'),
        ));
    }    
    
    protected function beforeSave() {
        
        if(!parent::beforeSave())
            return false;
        
        if(!$this->checkCreatorIsAdherent()) {
            throw new PetitionRateException('Petition can be rated by mandate\'s adherents only');
        }
        
        return true;
    }
    
    public function validateCreatorIsAdherent() {
        if(!$this->checkCreatorIsAdherent())
            $this->addError('user_id', 'Petition can be rated by mandate\'s adherents only');
    }
    
    protected function checkCreatorIsAdherent() {
        return $this->target->mandate->acceptsPetitionFrom($this->user_id);
    }    
}

class PetitionRateException extends CException {}