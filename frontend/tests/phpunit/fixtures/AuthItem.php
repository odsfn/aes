<?php
return array(
  array('name' => 'election_commentModerator','type' => '2','description' => '','bizrule' => 'return (isset($params["election"]) && $params["election"]->checkUserInRole($params["userId"], "election_commentModerator"));','data' => 'N;'),
  array('name' => 'commentor','type' => '2','description' => '','bizrule' => 'return !Yii::app()->user->isGuest;','data' => 'N;'),
  array('name' => 'commentReader','type' => '2','description' => '','bizrule' => 'return Yii::app()->user->isGuest;','data' => 'N;'),
  array('name' => 'createComment','type' => '0','description' => '','bizrule' => NULL,'data' => 'N;'),
  array('name' => 'deleteComment','type' => '0','description' => '','bizrule' => NULL,'data' => 'N;'),
  array('name' => 'manageOwnComment','type' => '1','description' => '','bizrule' => 'return ($params["userId"]==$params["comment"]->user_id);','data' => 'N;'),
  array('name' => 'readComment','type' => '0','description' => '','bizrule' => NULL,'data' => 'N;'),
  array('name' => 'updateComment','type' => '0','description' => '','bizrule' => NULL,'data' => 'N;')
);