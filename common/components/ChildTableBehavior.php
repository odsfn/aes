<?php
/**
 * This behavior should be attached to the Active Record wich contains attribute
 * that is foreign key to the Base Parent Table primary key. See slide 35 of the
 * presentation http://www.slideshare.net/billkarwin/practical-object-oriented-models-in-sql
 * to know more details about this Base Parent Table pattern
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ChildTableBehavior extends CActiveRecordBehavior {
    /**
     * The name of the parent table ( from which current record depends )
     * 
     * @var string 
     */
    public $parentTable;
    
    /**
     * The name of the primary key of the parent table
     * 
     * @var string
     */
    public $parentTablePk;
    
    /**
     * The name of the foreign key that refers to the parentTablePk 
     * @var string 
     */
    public $childConstraint;
    
    /**
     * The name of the column which stores type of the child row model
     * @var string 
     */
    public $typeFieldName = 'target_type';
    
    /**
     * Current transation instance
     * 
     * @var CDbTransaction 
     */
    protected $transaction;
    
    public function beforeSave($event) {
        
        if($this->owner->isNewRecord) {
            $db = Yii::app()->db;

            $this->transaction = $db->beginTransaction();
            
            $db->createCommand()->insert($this->parentTable, array($this->typeFieldName => get_class($this->owner)));
            
            $parentRecordId = $db->getLastInsertID();
            
            $this->setParentRecordId($parentRecordId);
        }
    }
    
    public function afterSave($event) {
        if(!$this->transaction)
            return;
        
        $this->transaction->commit();
    }

    public function afterDelete() {
        $parentRecordId = $this->getParentRecordId();
        
        Yii::app()->db->createCommand()->delete($this->parentTable, $this->parentTablePk . ' = ' . $parentRecordId);
    }
    
    public function getParentRecordId() {
        return $this->owner->{$this->childConstraint};
    }
    
    public function setParentRecordId($value) {
        $this->owner->{$this->childConstraint} = $value;
    }
}