<?php
return array(
  array('parent' => 'Election_1_commentModerator','child' => 'commentModerator'),
  array('parent' => 'Election_2_commentModerator','child' => 'commentModerator'),
  array('parent' => 'commentModerator','child' => 'commentor'),
  array('parent' => 'commentor','child' => 'commentReader'),
  array('parent' => 'commentor','child' => 'createComment'),
  array('parent' => 'commentModerator','child' => 'deleteComment'),
  array('parent' => 'manageOwnComment','child' => 'deleteComment'),
  array('parent' => 'commentor','child' => 'manageOwnComment'),
  array('parent' => 'commentReader','child' => 'readComment'),
  array('parent' => 'manageOwnComment','child' => 'updateComment')
);

