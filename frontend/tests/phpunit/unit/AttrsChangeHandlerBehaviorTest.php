<?php

/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */

class AttrsChangeHandlerBehaviorTest extends PHPUnit_Framework_TestCase
{
    const doomyTableName = 'doomy_attrs_change_handler';

    protected $db;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        Yii::app()->db->createCommand()->createTable(self::doomyTableName, array(
            'id' => 'pk',
            'value_str' => 'varchar(128) null default null',
            'value_int' => 'int(11) null default null'
        ));
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        Yii::app()->db->createCommand()->dropTable(self::doomyTableName);
    }

    protected function setUp()
    {
        $this->db = Yii::app()->db;
        $this->db->createCommand()->truncateTable(self::doomyTableName);
        $this->db->createCommand()->insert(self::doomyTableName, array(
            'value_str' => 'foo',
            'value_int' => 1
        ));

        parent::setUp();
    }

    protected function getDoomy()
    {
        return new Doomy();
    }

    function testIsStoredDiffersBeforeFirstSave()
    {
        $doomy = $this->getDoomy();

        $doomy->value_str = 'bar';
        $this->assertTrue($doomy->save());

        $this->assertFalse($doomy->isStoredDiffers('value_str'));
    }

    function testIsStoredDiffers()
    {
        $doomy = Doomy::model()->find();

        $this->assertFalse($doomy->isStoredDiffers('value_str'));

        $doomy->value_str = 'bar';
        $this->assertTrue($doomy->isStoredDiffers('value_str'));
        $doomy->save();
        $this->assertFalse($doomy->isStoredDiffers('value_str'));

        $doomy->value_str = 'zar';
        $this->assertTrue($doomy->isStoredDiffers('value_str'));
    }

    function testOnAfterStoredAttrChange()
    {
        $doomy = $this->getMock('Doomy', array('afterStoredAttrChanged_value_str', 'afterStoredAttrChanged_value_int'));

        $doomy->isNewRecord = false;
        $doomy->id = 1;
        $doomy->refresh();

        $test = $this;

        $doomy->expects($this->never())
                ->method('afterStoredAttrChanged_value_int');

        $doomy->expects($this->exactly(1))
                ->method('afterStoredAttrChanged_value_str')
                ->with(
                        $this->equalTo('bar'), $this->equalTo('foo'), $this->equalTo('value_str')
        );

        $doomy->value_str = 'bar';
        $this->assertTrue($doomy->save());
        $this->assertTrue($doomy->save());
    }

    function testOnAfterInsert()
    {
        $doomy = $this->getMock('Doomy', array('afterInsert'));
        $doomy->value_int = uniqid();
        $doomy->value_str = 'Foo';
        
        $doomy->expects($this->exactly(1))
            ->method('afterInsert');
        
        $this->assertTrue($doomy->save());
        $doomy->value_int = uniqid();
        $this->assertTrue($doomy->save());
    }

}

class Doomy extends CActiveRecord
{

    public function behaviors()
    {
        return array(
            'attrsChangeHandler' => array(
                'class' => 'AttrsChangeHandlerBehavior',
                'track' => array('value_str', 'value_int')
            )
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * @return CActiveRecord the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return AttrsChangeHandlerBehaviorTest::doomyTableName;
    }

    public function afterStoredAttrChanged_value_str($currentValue, $oldValue, $attrName)
    {
        return false;
    }

    public function afterStoredAttrChanged_value_int($currentValue, $oldValue, $attrName)
    {
        return false;
    }

    public function afterInsert()
    {
        return false;
    }
}
