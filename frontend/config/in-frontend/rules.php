<?php
/**
 * Router rules. Will be included in frontend.php config file
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
return array(
    
        // Restfullyii routes

        'api/<target_type:\w+>_<controller:(comment|rate|post)>'=>array('api/<controller>/restList', 'verb'=>'GET'),
        'api/<target_type:\w+>_<controller:(comment|rate|post)>/<id:\w*>'=>array('api/<controller>/restView', 'verb'=>'GET'),
        array('api/<controller>/restUpdate', 'pattern'=>'api/<target_type:\w+>_<controller:(comment|rate|post)>/<id:\w*>', 'verb'=>'PUT'),
        array('api/<controller>/restDelete', 'pattern'=>'api/<target_type:\w+>_<controller:(comment|rate|post)>/<id:\w*>', 'verb'=>'DELETE'),
        array('api/<controller>/restCreate', 'pattern'=>'api/<target_type:\w+>_<controller:(comment|rate|post)>', 'verb'=>'POST'),
        array('api/<controller>/restCreate', 'pattern'=>'api/<target_type:\w+>_<controller:(comment|rate|post)>/<id:\w+>', 'verb'=>'POST'),                          


        'api/<controller:\w+>'=>array('api/<controller>/restList', 'verb'=>'GET'),
        'api/<controller:\w+>/<id:\w*>'=>array('api/<controller>/restView', 'verb'=>'GET'),
        'api/<controller:\w+>/<id:\w*>/<var:\w*>'=>array('api/<controller>/restView', 'verb'=>'GET'),
        'api/<controller:\w+>/<id:\w*>/<var:\w*>/<var2:\w*>'=>array('api/<controller>/restView', 'verb'=>'GET'),

        array('api/<controller>/restUpdate', 'pattern'=>'api/<controller:\w+>/<id:\w*>', 'verb'=>'PUT'),
        array('api/<controller>/restUpdate', 'pattern'=>'api/<controller:\w+>/<id:\w*>/<var:\w*>', 'verb'=>'PUT'),
        array('api/<controller>/restUpdate', 'pattern'=>'api/<controller:\w*>/<id:\w*>/<var:\w*>/<var2:\w*>', 'verb'=>'PUT'),   

        array('api/<controller>/restDelete', 'pattern'=>'api/<controller:\w+>/<id:\w*>', 'verb'=>'DELETE'),
        array('api/<controller>/restDelete', 'pattern'=>'api/<controller:\w+>/<id:\w*>/<var:\w*>', 'verb'=>'DELETE'),
        array('api/<controller>/restDelete', 'pattern'=>'api/<controller:\w+>/<id:\w*>/<var:\w*>/<var2:\w*>', 'verb'=>'DELETE'),

        array('api/<controller>/restCreate', 'pattern'=>'api/<controller:\w+>', 'verb'=>'POST'),
        array('api/<controller>/restCreate', 'pattern'=>'api/<controller:\w+>/<id:\w+>', 'verb'=>'POST'),

        // default rules
        '<controller:\w+>/<id:\d+>' => '<controller>',
        '<controller:\w+>/<action:\w+>/<id:\d+>/*' => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
    
        '<controller:mandate>/<action:index>/*' => '<controller>/<action>'
);
