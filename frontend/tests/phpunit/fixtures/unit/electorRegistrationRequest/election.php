<?php
return array(
  array('id' => '1', 'target_id' => 8, 'user_id' => '3','name' => 'Election 1','status' => '1','mandate' => 'Mandate of Election 1','quote' => '25','validity' => '1','cand_reg_type' => '1','cand_reg_confirm' => '1','voter_reg_type' => '0','voter_reg_confirm' => '1', 'unassigned_access_level' => 2),
  array('id' => '2', 'target_id' => 9, 'user_id' => '1','name' => 'Election 2','status' => '1','mandate' => 'Mandate of Election 2','quote' => '25','validity' => '1','cand_reg_type' => '1','cand_reg_confirm' => '1','voter_reg_type' => '0','voter_reg_confirm' => '0', 'unassigned_access_level' => 2),
  array('id' => '3', 'target_id' => 10, 'user_id' => '3','name' => 'Election 3',
      'status' => '1','mandate' => 'Mandate of Election 3','quote' => '25',
      'validity' => '1','cand_reg_type' => '1','cand_reg_confirm' => '1','voter_reg_type' => '0',
      'voter_reg_confirm' => '1', 'voter_group_restriction' => Election::VGR_GROUPS_ADD, 
      'unassigned_access_level' => 2
  ),    
);
