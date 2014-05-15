<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class AESHelperTest extends PHPUnit_Framework_TestCase
{
    public function testExplode()
    {
        $expArr = array('a', 'b', 'c', 'd');
        $source = 'a,b , c, d';
        
        $this->assertEquals($expArr, AESHelper::explode($source));
        
        $expArr = array('a');
        $source = '  a  ';
        
        $this->assertEquals($expArr, AESHelper::explode($source));
        
        $expArr = array('a', 'b', 'c', 'd', 'e', 'f');
        $source = ' a b   c  d e f';
        
        $this->assertEquals($expArr, AESHelper::explode($source, ' '));
    }
}
