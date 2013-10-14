<?php
/*
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ConversationParticipantController extends RestController {  
    
    public $nestedModels = array(
       
        'user' => array(
            'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64'
        )
        
    );
        
    public $virtualAttrs = array(
        'user'
    );
    
    protected function getOutputFormatters() {
        
        return array(
            'last_view_ts' => function($value) {
                if(strstr($value,'0000-00-00'))
                    return 0;

                return (int)strtotime($value);
            }
        );
        
    }
    
    protected function getInputFormatters() {
        return array(
            'last_view_ts' => function($value) {
                return date('Y-m-d H:i:s', (int)$value);
            }
        );
    }
    
}