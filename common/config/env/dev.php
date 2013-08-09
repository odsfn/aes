<?php
/**
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
return array(
	'modules' => array(
		'gii' => array(
			'class' => 'system.gii.GiiModule',
			'password' => 'yii',
			'ipFilters' => array('127.0.0.1','::1'),
		),
	),
    
	'params' => array(
		'yii.handleErrors' => true,
		'yii.debug' => true,
		'yii.traceLevel' => 3,
	    
		'YiiMailer'=>array(
		    'Mailer'=>'smtp',
		    'Host'=>'smtp.mail.ru',
		    'Port'=>'2525',
		    'Username'=>'vptester@mail.ru',
		    'Password'=>'vptester_qwerty',
		    'SMTPAuth'=>true,
		)
	)
);