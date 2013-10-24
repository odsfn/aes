<?php
Yii::import('frontend.widgets.MarionetteWidget');
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class MarionetteWidgetTest extends PHPUnit_Framework_TestCase {

    public function testPerformRolesCheck() {
        $widget = new MarionetteWidget;
        
        $widget->checkForRoles = array(
            'commentsAdmin' => function($params) {
                    return true;
            }
        );
        
        $class = new ReflectionClass('MarionetteWidget');
        $method = $class->getMethod('performRolesCheck');
        $method->setAccessible(true);
        
        $result = $method->invokeArgs($widget, array($widget->checkForRoles));
        
        $this->assertEquals(array('commentsAdmin'), $result);
    }
    
    public function testPerformRoleCheckAndConvertionToJsRole() {
        
        $webUserMock = $this->getMock('CWebUser', array('checkAccess', 'init', 'getId'));

        $webUserMock->expects($this->at(1))
                ->method('checkAccess')
                ->with($this->equalTo('commentsAdmin'))
                ->will($this->returnValue(true));
        
        $webUserMock->expects($this->at(2))
                ->method('checkAccess')
                ->with($this->equalTo('yiiRole1'))
                ->will($this->returnValue(false));       

        $webUserMock->expects($this->at(3))
                ->method('checkAccess')
                ->with($this->equalTo('yiiRole2'))
                ->will($this->returnValue(true));         
        
        $webUserMock->expects($this->any())
                ->method('getId')
                ->will($this->returnValue(1));
        
        Yii::app()->setComponent('user', $webUserMock, false);
        
        $widget = new MarionetteWidget;
        
        $widget->checkForRoles = array(
            'commentsAdmin',
            'jsRole1' => 'yiiRole1',
            'jsRole2' => 'yiiRole2'
        );
        
        $class = new ReflectionClass('MarionetteWidget');
        $method = $class->getMethod('performRolesCheck');
        $method->setAccessible(true);
        
        $result = $method->invokeArgs($widget, array($widget->checkForRoles));
        
        $this->assertEquals(array('commentsAdmin', 'jsRole2'), $result);        
        
    }
    
}
