<?php
return array(
    array('itemname' => 'Election_1_commentModerator', 'userid' => '3', 'bizrule' => 'return ($data[\'targetType\']==$params[\'targetType\'] && $data[\'targetId\']==$params[\'targetId\']);', 'data' => serialize(array('targetType'=>'Election', 'targetId' => 1))),
    array('itemname' => 'Election_2_commentModerator', 'userid' => '1', 'bizrule' => 'return ($data[\'targetType\']==$params[\'targetType\'] && $data[\'targetId\']==$params[\'targetId\']);', 'data' => serialize(array('targetType'=>'Election', 'targetId' => 2))),
    
    array('itemname' => 'Election_1_commentModerator', 'userid' => '4', 'bizrule' => 'return ($data[\'targetType\']==$params[\'targetType\'] && $data[\'targetId\']==$params[\'targetId\']);', 'data' => serialize(array('targetType'=>'Election', 'targetId' => 1))),
    array('itemname' => 'Election_2_commentModerator', 'userid' => '4', 'bizrule' => 'return ($data[\'targetType\']==$params[\'targetType\'] && $data[\'targetId\']==$params[\'targetId\']);', 'data' => serialize(array('targetType'=>'Election', 'targetId' => 2))),    
);
