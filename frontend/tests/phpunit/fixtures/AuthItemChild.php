<?php
return array(
  array('parent' => 'election_commentModerator','child' => 'commentor'),
  array('parent' => 'commentor','child' => 'commentReader'),
  array('parent' => 'commentor','child' => 'createComment'),
  array('parent' => 'election_commentModerator','child' => 'deleteComment'),
  array('parent' => 'manageOwnComment','child' => 'deleteComment'),
  array('parent' => 'commentor','child' => 'manageOwnComment'),
  array('parent' => 'commentReader','child' => 'readComment'),
  array('parent' => 'manageOwnComment','child' => 'updateComment')
);

