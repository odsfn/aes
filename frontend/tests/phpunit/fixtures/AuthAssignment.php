<?php

return array(
    array('itemname' => 'commentModerator', 'userid' => '3', 'bizrule' => 'return ($data[\'targetType\']==$params[\'targetType\'] && $data[\'targetId\']==$params[\'targetId\']);', 'data' => serialize(array('targetType'=>'Election', 'targetId' => 1))),
    array('itemname' => 'commentModerator', 'userid' => '1', 'bizrule' => 'return ($data[\'targetType\']==$params[\'targetType\'] && $data[\'targetId\']==$params[\'targetId\']);', 'data' => serialize(array('targetType'=>'Election', 'targetId' => 2))),
);
