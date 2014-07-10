<?php

class m140709_133529_add_ability_to_became_candidate extends EDbMigration
{
    public function up()
    {
        $this->update('AuthItem', 
            array(
                'bizrule'=>'return (isset($params["election"]) && isset($params["userId"])'
                    . '&& $params["election"]->cand_reg_type == Election::CAND_REG_TYPE_SELF'
                    . '&& $params["election"]->status == Election::STATUS_REGISTRATION '
                    . '&& $params["candidate_user_id"] == $params["userId"] '
                    . '&& !count($params["election"]->candidates('
                            . 'array("condition"=>"user_id=".$params["userId"])'
                        . '))'
                . ');'
            ),
            'name = "election_selfAppointment"'
        );
        
        Yii::app()->authManager
            ->getAuthItem('authenticated')
            ->addChild('election_selfAppointment');
    }

    public function down()
    {
        Yii::app()->authManager
            ->getAuthItem('authenticated')
            ->removeChild('election_selfAppointment');
        
        $this->update('AuthItem', 
            array(
                'bizrule'=>'return (isset($params["election"]) '
                    . '&& $params["election"]->cand_reg_type == 0);'
            ),
            'name = "election_selfAppointment"'
        );        
    }
}