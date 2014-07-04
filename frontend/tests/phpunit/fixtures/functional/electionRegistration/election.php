<?php
return array(
    array('id' => '1', 'target_id' => 8, 'user_id' => '3','name' => 'Election 1','status' => '0','mandate' => 'Mandate of Election 1','quote' => '25','validity' => '1','cand_reg_type' => '0','cand_reg_confirm' => '0','voter_reg_type' => '0','voter_reg_confirm' => '0', 'unassigned_access_level' => 2),
    array('id' => '2', 'target_id' => 9, 'user_id' => '1','name' => 'Election 2','status' => '0','mandate' => 'Mandate of Election 2','quote' => '25','validity' => '1','cand_reg_type' => '0','cand_reg_confirm' => '0','voter_reg_type' => '0','voter_reg_confirm' => '0', 'unassigned_access_level' => 2),
    array(
        'id' => '3', 'target_id' => 10, 'user_id' => '1','name' => 'Election 3',
        'status' => '2','mandate' => 'Mandate of Election 3','quote' => '25',
        'validity' => '1','cand_reg_type' => '0','cand_reg_confirm' => '0',
        'voter_reg_type' => Election::VOTER_REG_TYPE_SELF,
        'voter_reg_confirm' => Election::VOTER_REG_CONFIRM_NOTNEED, 
        'unassigned_access_level' => 2
    ),
    array(
        'id' => '4', 'target_id' => 11, 'user_id' => '1','name' => 'Election 4',
        'status' => '1','mandate' => 'Mandate of Election 4','quote' => '25',
        'validity' => '1','cand_reg_type' => '0','cand_reg_confirm' => '0',
        'voter_reg_type' => Election::VOTER_REG_TYPE_SELF,
        'voter_reg_confirm' => Election::VOTER_REG_CONFIRM_NEED, 
        'unassigned_access_level' => 2
    ),    
    array(
        'id' => '5', 'target_id' => 12, 'user_id' => '1','name' => 'Election 5',
        'status' => '1','mandate' => 'Mandate of Election 5','quote' => '25',
        'validity' => '1','cand_reg_type' => '0','cand_reg_confirm' => '0',
        'voter_reg_type' => Election::VOTER_REG_TYPE_SELF,
        'voter_reg_confirm' => Election::VOTER_REG_CONFIRM_NOTNEED, 
        'unassigned_access_level' => 2
    ),     
    array(
        'id' => '6', 'target_id' => 13, 'user_id' => '1','name' => 'Election 6',
        'status' => '1','mandate' => 'Mandate of Election 6','quote' => '25',
        'validity' => '1','cand_reg_type' => '0','cand_reg_confirm' => '0',
        'voter_reg_type' => Election::VOTER_REG_TYPE_ADMIN,
        'voter_reg_confirm' => Election::VOTER_REG_CONFIRM_NOTNEED, 
        'unassigned_access_level' => 2
    ),    
);
