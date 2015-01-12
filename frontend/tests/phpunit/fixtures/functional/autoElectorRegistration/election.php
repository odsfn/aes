<?php
$id = 0;
$target_id = 7;

return array(
    array( //1
        'id' => ++$id, 'target_id' => ++$target_id, 'user_id' => '1','name' => 'Election ' . $id,
        'status' => Election::STATUS_REGISTRATION,'mandate' => 'Mandate of Election ' . $id,
        'quote' => '25','validity' => '1','cand_reg_type' => Election::CAND_REG_TYPE_SELF,
        'cand_reg_confirm' => Election::CAND_REG_CONFIRM_NOTNEED,
        'voter_reg_type' => Election::VOTER_REG_TYPE_SELF, 
        'voter_reg_confirm' => Election::VOTER_REG_CONFIRM_NOTNEED,
        'voter_group_restriction' => Election::VGR_NO,
        'unassigned_access_level' => 2
    ),
    array( //2
        'id' => ++$id, 'target_id' => ++$target_id, 'user_id' => '1','name' => 'Election ' . $id,
        'status' => Election::STATUS_REGISTRATION,'mandate' => 'Mandate of Election ' . $id,
        'quote' => '25','validity' => '1','cand_reg_type' => Election::CAND_REG_TYPE_SELF,
        'cand_reg_confirm' => Election::CAND_REG_CONFIRM_NOTNEED,
        'voter_reg_type' => Election::VOTER_REG_TYPE_SELF, 
        'voter_reg_confirm' => Election::VOTER_REG_CONFIRM_NEED,
        'voter_group_restriction' => Election::VGR_NO,
        'unassigned_access_level' => 2
    ),
    array( //3
        'id' => ++$id, 'target_id' => ++$target_id, 'user_id' => '1','name' => 'Election ' . $id,
        'status' => Election::STATUS_REGISTRATION,'mandate' => 'Mandate of Election ' . $id,
        'quote' => '25','validity' => '1','cand_reg_type' => Election::CAND_REG_TYPE_SELF,
        'cand_reg_confirm' => Election::CAND_REG_CONFIRM_NOTNEED,
        'voter_reg_type' => Election::VOTER_REG_TYPE_ADMIN, 
        'voter_reg_confirm' => Election::VOTER_REG_CONFIRM_NOTNEED,
        'voter_group_restriction' => Election::VGR_NO,
        'unassigned_access_level' => 2
    ),
    array( //4
        'id' => ++$id, 'target_id' => ++$target_id, 'user_id' => '1','name' => 'Election ' . $id,
        'status' => Election::STATUS_REGISTRATION,'mandate' => 'Mandate of Election ' . $id,
        'quote' => '25','validity' => '1','cand_reg_type' => Election::CAND_REG_TYPE_SELF,
        'cand_reg_confirm' => Election::CAND_REG_CONFIRM_NEED,
        'voter_reg_type' => Election::VOTER_REG_TYPE_ADMIN, 
        'voter_reg_confirm' => Election::VOTER_REG_CONFIRM_NOTNEED,
        'voter_group_restriction' => Election::VGR_NO,
        'unassigned_access_level' => 2
    ),
    array( //5
        'id' => ++$id, 'target_id' => ++$target_id, 'user_id' => '1','name' => 'Election ' . $id,
        'status' => Election::STATUS_REGISTRATION,'mandate' => 'Mandate of Election ' . $id,
        'quote' => '25','validity' => '1','cand_reg_type' => Election::CAND_REG_TYPE_ADMIN,
        'cand_reg_confirm' => Election::CAND_REG_CONFIRM_NEED,
        'voter_reg_type' => Election::VOTER_REG_TYPE_ADMIN, 
        'voter_reg_confirm' => Election::VOTER_REG_CONFIRM_NOTNEED,
        'voter_group_restriction' => Election::VGR_NO,
        'unassigned_access_level' => 2
    ),
);
