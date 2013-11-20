<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */

class TestChildTable extends CActiveRecord {
    
    public static function model($className = __CLASS__) {
        parent::model($className);
    }
    
    public function tableName() {
        return 'test_child_table';
    }
    
    public function behaviors() {
        return array(
            'childTable' => array(
                'class' => 'common.components.ChildTableBehavior',
                'parentTable' => 'test_parent_table',
                'parentTablePk' => 'parent_id',
                'childConstraint' => 'parent_id',
                'typeFieldName'   => 'type'
            )
        );
    }
    
}

class ChildTableBehaviorTest extends CDbTestCase {
    
    protected function setUp() {
        parent::setUp();
        
        Yii::app()->db->createCommand("
            
            DROP TABLE IF EXISTS test_child_table;
            DROP TABLE IF EXISTS test_parent_table;

            CREATE TABLE test_parent_table (
                parent_id INT(11) NOT NULL AUTO_INCREMENT,
                type VARCHAR(64) NOT NULL,
                PRIMARY KEY (parent_id)
            ) ENGINE = InnoDb ;
            
            CREATE TABLE test_child_table (
                parent_id INT(11) NOT NULL,
                data VARCHAR(64) NOT NULL,
                PRIMARY KEY (parent_id),
                FOREIGN KEY (parent_id) REFERENCES test_parent_table (parent_id)
            ) ENGINE = InnoDb ;
        ")->execute();
    }

    public function testCreatesAndDeletesParentRow() {
        $child = new TestChildTable();
        $child->data = 'foo';
        $this->assertTrue($child->save());
        
        $parent_id = $child->parent_id;
        
        $this->assertGreaterThan(0, $parent_id);
        
        $result = Yii::app()->db->createCommand('SELECT * FROM test_parent_table WHERE parent_id = ' . $parent_id)->queryAll();
        
        $this->assertEquals(1, count($result));
        $this->assertEquals(array('parent_id' => $parent_id, 'type' => 'TestChildTable'), $result[0]);
        
        //delete
        
        $child->delete();
        
        $result = Yii::app()->db->createCommand('SELECT * FROM test_parent_table UNION SELECT * FROM test_child_table')->queryAll();
        
        $this->assertEquals(0, count($result));
    }

}
