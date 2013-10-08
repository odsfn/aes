<?php

/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ArrayHelperTest extends PHPUnit_Framework_TestCase {
    
    public function testFormat() {
        $source = array(
            'a' => 1,
            'b' => array(
                'b1' => 2,
                'b2' => 3
            ),
            'c' => 4
        );
        
        $formatters = array(
            'c' => function($x){ 
                return ++$x;
            },
            'b.b2' => function($x){ 
                return ++$x;
            }
        );
        
        $result = ArrayHelper::format($source, $formatters);
        
        $this->assertEquals(array(
            'a' => 1,
            'b' => array(
                'b1' => 2,
                'b2' => 4
            ),
            'c' => 5
        ), $result);
    }
    
    public function testFormatWithNotAssociative() {
        
        $source = array(
            'a' => 1,
            'b' => array(
                array( 'c' => 2, 'd' => 3 ),
                array( 'c' => 4, 'd' => 5 )
            ),
            'c' => array( 'e' => 6, 'f' => 7 )
        );
        
        $result = ArrayHelper::format($source, array(
            'a' => function($x) {return ++$x;},
            'b.d' => function($x) {return $x += 2;},
            'c.e' => function($x) {return --$x;}
        ));
        
        $this->assertEquals(array(
            'a' => 2,
            'b' => array(
                array('c'=> 2, 'd' => 5),
                array('c'=> 4, 'd' => 7),
            ),
            'c' => array('e' => 5, 'f'=>7)
        ), $result);
    }
}
