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
    
    public function testFormatWithSameKeysOnDifferentLevelsWithOneMissed() {
        
        $source = array (
              0 => 
              array (
                'id' => '13',
                'title' => '',
                'created_ts' => '2013-10-09 14:50:19',
                'initiator_id' => '1',
                'participants' => 
                array (
                  0 => 
                  array (
                    'id' => '25',
                    'conversation_id' => '13',
                    'user_id' => '1',
                    'last_view_ts' => '0000-00-00 00:00:00',
                    'user' => 
                    array (
                      'user_id' => '1',
                      'first_name' => 'Vasiliy',
                      'last_name' => 'Pedak',
                      'photo' => '1_0_73787400_1377002601.jpg',
                      'photo_thmbnl_64' => '1_0_73787400_1377002601_64x64.jpg',
                      'birth_place' => NULL,
                      'birth_day' => NULL,
                      'gender' => NULL,
                      'mobile_phone' => NULL,
                      'email' => NULL,
                      'displayName' => 'Vasiliy Pedak',
                      'photoThmbnl64' => 'http://aes.dev/uploads/photos/1_0_73787400_1377002601_64x64.jpg',
                      'pageUrl' => 'http://aes.dev/index-test.php/userPage/1',
                    ),
                  ),
                  1 => 
                  array (
                    'id' => '26',
                    'conversation_id' => '13',
                    'user_id' => '7',
                    'last_view_ts' => '0000-00-00 00:00:00',
                    'user' => 
                    array (
                      'user_id' => '7',
                      'first_name' => 'Steve',
                      'last_name' => 'Jobs',
                      'photo' => NULL,
                      'photo_thmbnl_64' => NULL,
                      'birth_place' => NULL,
                      'birth_day' => NULL,
                      'gender' => NULL,
                      'mobile_phone' => NULL,
                      'email' => NULL,
                      'displayName' => 'Steve Jobs',
                      'photoThmbnl64' => '',
                      'pageUrl' => 'http://aes.dev/index-test.php/userPage/7',
                    ),
                  ),
                ),
              ),
            );
        
        $tsFormatter = function($value) {
            
            if(strstr($value,'0000-00-00'))
                return 0;
            
            return (int)strtotime($value) * 1000;
        };
        
        $formatters = array(
            'created_ts' => $tsFormatter,
            'messages.created_ts' => $tsFormatter,
            'participants.last_view_ts' => $tsFormatter
        );
        
        $result = ArrayHelper::format($source, $formatters);
        
        $this->assertEquals((int)strtotime($source[0]['created_ts'])*1000, $result[0]['created_ts']);
        $this->assertEquals(0, $result[0]['participants'][0]['last_view_ts']);
        $this->assertEquals(0, $result[0]['participants'][1]['last_view_ts']);
    }
    
    public function testFormatWithSameKeysOnDifferentLevels() {
        
        $source = array (
              0 => 
              array (
                'id' => '13',
                'title' => '',
                'created_ts' => '2013-10-09 14:50:19',
                'initiator_id' => '1',
                'participants' => 
                array (
                  0 => 
                  array (
                    'id' => '25',
                    'conversation_id' => '13',
                    'user_id' => '1',
                    'last_view_ts' => '0000-00-00 00:00:00',
                    'user' => 
                    array (
                      'user_id' => '1',
                      'first_name' => 'Vasiliy',
                      'last_name' => 'Pedak',
                      'photo' => '1_0_73787400_1377002601.jpg',
                      'photo_thmbnl_64' => '1_0_73787400_1377002601_64x64.jpg',
                      'birth_place' => NULL,
                      'birth_day' => NULL,
                      'gender' => NULL,
                      'mobile_phone' => NULL,
                      'email' => NULL,
                      'displayName' => 'Vasiliy Pedak',
                      'photoThmbnl64' => 'http://aes.dev/uploads/photos/1_0_73787400_1377002601_64x64.jpg',
                      'pageUrl' => 'http://aes.dev/index-test.php/userPage/1',
                    ),
                  ),
                  1 => 
                  array (
                    'id' => '26',
                    'conversation_id' => '13',
                    'user_id' => '7',
                    'last_view_ts' => '0000-00-00 00:00:00',
                    'user' => 
                    array (
                      'user_id' => '7',
                      'first_name' => 'Steve',
                      'last_name' => 'Jobs',
                      'photo' => NULL,
                      'photo_thmbnl_64' => NULL,
                      'birth_place' => NULL,
                      'birth_day' => NULL,
                      'gender' => NULL,
                      'mobile_phone' => NULL,
                      'email' => NULL,
                      'displayName' => 'Steve Jobs',
                      'photoThmbnl64' => '',
                      'pageUrl' => 'http://aes.dev/index-test.php/userPage/7',
                    ),
                  ),
                ),
                'messages' => array(
                    array(
                        'id' => 1,
                        'created_ts' => '2013-10-09 15:10:02',
                        'text' => 'foo'
                    ),
                    array(
                        'id' => 2,
                        'created_ts' => '2013-10-09 15:12:02',
                        'text' => 'bar'
                    )
                )
              ),
            );
        
        $tsFormatter = function($value) {
            
            if(strstr($value,'0000-00-00'))
                return 0;
            
            return (int)strtotime($value) * 1000;
        };
        
        $formatters = array(
            'created_ts' => $tsFormatter,
            'messages.created_ts' => $tsFormatter,
            'participants.last_view_ts' => $tsFormatter
        );
        
        $result = ArrayHelper::format($source, $formatters);
        
        $this->assertEquals((int)strtotime($source[0]['created_ts'])*1000, $result[0]['created_ts']);
        $this->assertEquals(0, $result[0]['participants'][0]['last_view_ts']);
        $this->assertEquals(0, $result[0]['participants'][1]['last_view_ts']);
        $this->assertEquals((int)strtotime($source[0]['messages'][0]['created_ts'])*1000, $result[0]['messages'][0]['created_ts']);
        $this->assertEquals((int)strtotime($source[0]['messages'][1]['created_ts'])*1000, $result[0]['messages'][1]['created_ts']);
    }
}
