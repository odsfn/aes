 <?php
/**
 * This is the model class for table "petition".
 *
 * The followings are the available columns in table 'petition':
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $mandate_id
 * @property integer $creator_id
 * @property string $created_ts
 *
 * The followings are the available model relations:
 * @property Profile $creator
 * @property Mandate $mandate
 * @property PetitionComment[] $comments
 * @property PetitionRate[] $rates
 * @property int $positiveRatesCount
 * @property int $negativeRatesCount
 */
class Petition extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Petition the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'petition';
    }

    public function behaviors() {
        return array(
            'UpdateDateBehavior' => array(
                'class' => 'UpdateDateBehavior',
                'fields' => array(
                    'create'=> array('created_ts'),
                )
            )
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title, content, mandate_id, creator_id', 'required'),
            array('creator_id', 'validateCreatorIsAdherent'),
            array('mandate_id, creator_id', 'numerical', 'integerOnly'=>true),
            array('title', 'length', 'max'=>1024),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, title, content, mandate_id, creator_id, created_ts', 'safe', 'on'=>'search'),
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
            'creator' => array(self::BELONGS_TO, 'Profile', 'creator_id'),
            'mandate' => array(self::BELONGS_TO, 'Mandate', 'mandate_id'),
            'comments' => array(self::HAS_MANY, 'PetitionComment', 'target_id'),
            'rates' => array(self::HAS_MANY, 'PetitionRate', 'target_id'),
            'positiveRatesCount' => array(self::STAT, 'PetitionRate', 'target_id', 'condition' => 'score = 1'),
            'negativeRatesCount' => array(self::STAT, 'PetitionRate', 'target_id', 'condition' => 'score = -1'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'mandate_id' => 'Mandate',
            'creator_id' => 'Creator',
            'created_ts' => 'Created Ts',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('title',$this->title,true);
        $criteria->compare('content',$this->content,true);
        $criteria->compare('mandate_id',$this->mandate_id);
        $criteria->compare('creator_id',$this->creator_id);
        $criteria->compare('created_ts',$this->created_ts,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    protected function beforeSave() {
        
        if(!$this->checkCreatorIsAdherent()) {
            throw new PetitionException('Petition can be created by mandate\'s adherents only');
        }
        
        return parent::beforeSave();
    }
    
    public function validateCreatorIsAdherent() {
        if(!$this->checkCreatorIsAdherent())
            $this->addError('creator_id', 'Petition can be created by mandate\'s adherents only');
    }
    
    protected function checkCreatorIsAdherent() {
        return $this->mandate->acceptsPetitionFrom($this->creator_id);
    }
}

class PetitionException extends CException {
    
}