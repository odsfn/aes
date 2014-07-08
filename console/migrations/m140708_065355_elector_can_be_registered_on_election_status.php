<?php

class m140708_065355_elector_can_be_registered_on_election_status extends EDbMigration
{
    public function up()
    {
        $this->update('AuthItem', 
            array(
                'bizrule'=>'return (isset($params["election"]) && isset($params["userId"])'
                    . '&& $params["election"]->voter_reg_type == Election::VOTER_REG_TYPE_SELF'
                    . '&& ( $params["election"]->status == Election::STATUS_REGISTRATION '
                        . '|| $params["election"]->status == Election::STATUS_ELECTION )'
                    . '&& $params["elector_user_id"] == $params["userId"] '
                    . '&& !count($params["election"]->electors('
                            . 'array("condition"=>"user_id=".$params["userId"])'
                        . '))'
                . ');'
            ),
            'name = "election_askToBecameElector"'
        );
        
        $this->update('AuthItem', 
            array(
                'bizrule'=>'return (isset($params["election"]) '
                    . '&& ( $params["election"]->status == Election::STATUS_REGISTRATION '
                        . '|| $params["election"]->status == Election::STATUS_ELECTION )'
                    . ');'
            ),
            'name IN (\'election_activateElector\',\'election_addElector\')'
        );        
    }

    public function down()
    {
        $this->update('AuthItem', 
            array(
                'bizrule'=>'return (isset($params["election"]) && isset($params["userId"])'
                    . '&& $params["election"]->voter_reg_type == Election::VOTER_REG_TYPE_SELF'
                    . '&& $params["election"]->status == Election::STATUS_REGISTRATION'
                    . '&& $params["elector_user_id"] == $params["userId"] '
                    . '&& !count($params["election"]->electors('
                            . 'array("condition"=>"user_id=".$params["userId"])'
                        . '))'
                . ');'
            ),
            'name = "election_askToBecameElector"'
        );
        
        $this->update('AuthItem', 
            array(
                'bizrule'=>'return (isset($params["election"]) '
                    . '&& $params["election"]->status == Election::STATUS_REGISTRATION);'
            ),
            'name IN (\'election_activateElector\',\'election_addElector\')'
        );           
    }
}