<?php

/**
 * This is the model class for table "election".
 *
 * The followings are the available columns in table 'election':
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property integer $status
 * @property string $mandate
 * @property integer $quote
 * @property integer $validity
 * @property integer $cand_reg_type
 * @property integer $cand_reg_confirm
 * @property integer $voter_reg_type
 * @property integer $voter_reg_confirm
 * @property integer $unassigned_access_level Access level for users that were not assigned to this election
 * @property integet $target_id
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Target $target AR from base parent table.
 */
class Election extends CActiveRecord implements iPostable, iCommentable
{
    const IMAGE_WIDTH = 400;
    const IMAGE_HEIGHT = 400;
    const IMAGE_QUALITY = 95;
    const IMAGE_SAVE_PATH = '/www/uploads/elections/';
    
    const UNASSIGNED_CAN_POST = 2;
    const UNASSIGNED_CAN_READ = 1;
    const UNASSIGNED_CAN_NONE = 0;

    const STATUS_PUBLISHED = 0;
    const STATUS_REGISTRATION = 1;
    const STATUS_ELECTION = 2;
    const STATUS_FINISHED = 3;
    const STATUS_CANCELED = 4;
    
    public static $statuses = array(
        Election::STATUS_PUBLISHED    => 'Published',
        Election::STATUS_REGISTRATION => 'Registration',
        Election::STATUS_ELECTION => 'Election',
        Election::STATUS_FINISHED => 'Finished',
        Election::STATUS_CANCELED => 'Canceled',
    );

    public static $cand_reg_types = array(
        '0'=>'Myself',
        '1'=>'Other',
    );

    public static $cand_reg_confirms = array(
        '0'=>'No',
        '1'=>'Yes',
    );

    public static $voter_reg_types = array(
        '0'=>'Myself',
        '1'=>'Other',
    );

    public static $voter_reg_confirms = array(
        '0'=>'No',
        '1'=>'Yes',
    );

    public $uploaded_file = null;

    private $_text_status = null;

    public function getText_status() {
        if ($this->_text_status === null)
            $this->_text_status = Yii::t('aes',self::$statuses[$this->status]);
        return $this->_text_status;
    }

    private $_have_pic = null;

    public function getHave_pic() {
        if ($this->_have_pic === null)
            $this->_have_pic = (int)is_file($this->creratePicPath());
        return $this->_have_pic;
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Election the static model class
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
            return 'election';
    }

    public function behaviors() {
        return array(
            'ObjectAuthAssignmentBehavior',
            'childTable' => array(
                'class' => 'ChildTableBehavior',
                'parentTable'   => 'target',
                'parentTablePk' => 'target_id',
                'childConstraint' => 'target_id'
            ),

            'galleryBehavior' => array(
                'class' => 'GalleryBehavior',
                'idAttribute' => 'gallery_id',
                'versions' => array(
                    'small' => array(
                        'centeredpreview' => array(98, 98),
                    ),
                    'medium' => array(
                        'resize' => array(800, null),
                    )
                ),
                'name' => true,
                'description' => true,
            ),

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
                array('user_id, name, mandate, quote, validity, cand_reg_type, cand_reg_confirm, voter_reg_type, voter_reg_confirm', 'required'),

                array('user_id', 'exist', 'className' => 'UserAccount', 'attributeName' => 'id'),
                
                array('quote, validity', 'numerical', 'integerOnly'=>true, 'min'=>1),

                array('cand_reg_type, cand_reg_confirm, voter_reg_type, voter_reg_confirm', 'in', 'range'=>array(0,1)),
                array('name, mandate', 'length', 'max'=>1000),

                array('status', 'in', 'range'=>array(0,1,2,3,4)),
                array('uploaded_file', 'file', 'types' => 'jpg,jpeg,jpe,png,gif', 'maxSize' => 5000000, 'allowEmpty'=>true),

                array('unassigned_access_level', 'numerical', 'integerOnly'=>true, 'min'=>0),
                array('unassigned_access_level', 'default', 'setOnEmpty'=>true, 'value'=>self::UNASSIGNED_CAN_POST),
                
                array('id, user_id, name, status, mandate, quote, validity, cand_reg_type, cand_reg_confirm, voter_reg_type, voter_reg_confirm', 'safe', 'on'=>'search'),

                array('name, status', 'safe', 'on'=>'search'),

                array('id, name, status, text_status, have_pic', 'safe', 'on' => 'rest'),

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
                    'user' => array(self::BELONGS_TO, 'UserAccount', 'user_id'),
                    'target' => array(self::BELONGS_TO, 'Target', 'target_id'),
                    'candidates' => array(self::HAS_MANY, 'Candidate', 'election_id')
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
                    'name' => 'Title',
                    'status' => 'Status',
                    'mandate' => 'Mandate',
                    'quote' => 'Quote',
                    'validity' => 'Validity',
                    'cand_reg_type' => 'Candidate Registration Type',
                    'cand_reg_confirm' => 'Candidate Registration Confirmation',
                    'voter_reg_type' => 'Voter Registration Type',
                    'voter_reg_confirm' => 'Voter Registration Confirmation',
        'uploaded_file'=>'Image',
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
            $criteria->compare('user_id',$this->user_id);
            $criteria->compare('name',$this->name,true);
            $criteria->compare('status',$this->status);
            $criteria->compare('mandate',$this->mandate,true);
            $criteria->compare('quote',$this->quote);
            $criteria->compare('validity',$this->validity);
            $criteria->compare('cand_reg_type',$this->cand_reg_type);
            $criteria->compare('cand_reg_confirm',$this->cand_reg_confirm);
            $criteria->compare('voter_reg_type',$this->voter_reg_type);
            $criteria->compare('voter_reg_confirm',$this->voter_reg_confirm);

            return new CActiveDataProvider($this, array(
                    'criteria'=>$criteria,
            ));
    }
    
    public function canUnassignedPost() {
        return ($this->unassigned_access_level >= self::UNASSIGNED_CAN_POST);
    }

    public function canUnassignedReadPost() {
        return ($this->unassigned_access_level >= self::UNASSIGNED_CAN_READ);
    }
    
    public function canUnassignedComment() {
        return true;
    }

    public function canUnassignedRead() {
        return true;
    }

    protected function creratePicPath() {
        return Yii::app()->basePath.'/www/uploads/elections/'.$this->id.'.jpg';
    }
    
    public function getPicUrl() {
        if($this->have_pic) {
            return Yii::app()->getBaseUrl(true) . '/uploads/elections/' . $this->id . '.jpg';
        }
        
        return false;
    }
}