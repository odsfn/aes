<?php

/**
 * Should be used as superclass for all classes of models with role assignment per object
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class BaseObjectAuthAssignment extends CActiveRecord {
    /**
     * @return string
     */
    public function getObjectName() {
        $className = get_class($this);
        return str_replace('AuthAssignment', '', $className);
    }
    
    /**
     * Pk in object table
     * @return string
     */
    public function getObjectPk() {
        return 'id';
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return lcfirst($this->objectName) . '_auth_assignment';
    }    
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ElectionComment the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('auth_assignment_id, object_id', 'required'),
            array('id, auth_assignment_id, object_id', 'numerical', 'integerOnly'=>true)
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
            'authAssignment' => array(self::BELONGS_TO, 'AuthAssignment', 'auth_assignment_id'),
            'profile' => array(self::HAS_ONE, 'Profile', array('userid'=>'user_id'), 'through' => 'authAssignment'),
            'object' => array(self::BELONGS_TO, $this->getObjectName(), 'object_id')
        );
    }

    public function getRight() {
        return $this->authAssignment->itemname;
    }
    
    public function criteriaWithRole($authItem, $traverse = true) {
        
        $auth = Yii::app()->authManager;
        $authItems = array();
        
        if(!($item = $auth->getAuthItem($authItem)))
            throw new Exception ('Specified authItem "'. $authItem .'" was not found');
        
        $authItems[] = $authItem;
        
        if($traverse) {
            $ancestors = $auth->getAncestors($authItem);
            $authItems = array_merge($authItems, $ancestors);
        }
        
        foreach ($authItems as $index => $item) {
            $authItems[$index] = '"'.$item.'"';
        }
        
        $authItems = implode(',', $authItems);
        
        $this->getDbCriteria()->mergeWith(array(
            'join' => 'INNER JOIN AuthAssignment aa ON aa.id = t.auth_assignment_id AND aa.itemname IN ('. $authItems .')'
        ));
        
        return $this;
    }
}
