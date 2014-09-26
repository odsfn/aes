<?php

class m140925_095632_remove_permissions_to_create_elector_from_users extends EDbMigration
{    
    public function up()
    {
        $this->update('AuthItem', 
            array(
                'bizrule'=>
                    'return (isset($params["election"]) && isset($params["userId"]) '
                    . '&& ElectorRegistrationRequest::isAvailable($params["election"], $params["userId"]) '
                . ');'
            ),
            'name = "election_askToBecameElector"'
        );
        
        Yii::app()->authManager
            ->removeItemChild('election_askToBecameElector', 'election_addElector');
    }

    public function down()
    {
        $this->update('AuthItem', 
            array(
                'bizrule'=>'return (isset($params["election"]) && isset($params["userId"]) '
                    . '&& $params["election"]->voter_reg_type == Election::VOTER_REG_TYPE_SELF '
                    . '&& ( $params["election"]->status == Election::STATUS_REGISTRATION '
                        . '|| $params["election"]->status == Election::STATUS_ELECTION ) '
                    . '&& $params["elector_user_id"] == $params["userId"] '
                    . '&& !count($params["election"]->electors('
                            . 'array("condition"=>"user_id=".$params["userId"])'
                        . '))'
                . ');'
            ),
            'name = "election_askToBecameElector"'
        );

        Yii::app()->authManager
            ->addItemChild('election_askToBecameElector', 'election_addElector');     
    }
}