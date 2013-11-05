<?php
/**
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ObjectAuthAssignmentDbManagerHelper {
    
    protected $migration;
    
    protected $relatedTable;
    
    public $relatedTablePk = 'id';
            
    function __construct($relatedEntity, CDbMigration $migration) {
        $this->migration = $migration;
        $this->relatedTable = $relatedEntity;
    }


    public function createTables() {
        $this->migration->createTable($this->getTableName(), array(
                'id' => 'pk',
                'auth_assignment_id' => 'INT(11) NOT NULL',
                'object_id'      => 'INT(11) NOT NULL',
        ));
        
        $this->migration->addForeignKey('fk_' . $this->getTableName() . '_auth_item', $this->getTableName(), 'auth_assignment_id', 'AuthAssignment', 'id', 'CASCADE', 'NO ACTION');
        $this->migration->addForeignKey('fk_' . $this->getTableName() . '_object_id', $this->getTableName(), 'object_id', $this->relatedTable, $this->relatedTablePk, 'CASCADE', 'NO ACTION');
        $this->migration->createIndex('ux_' . $this->getTableName() . '_auth_assignment_id_object_id', $this->getTableName(), 'auth_assignment_id, object_id', true);
    }
    
    public function dropTables() {
        $this->migration->dropTable($this->getTableName());
    }
    
    public function getTableName() {
        return $this->relatedTable . '_auth_assignment';
    }
}
