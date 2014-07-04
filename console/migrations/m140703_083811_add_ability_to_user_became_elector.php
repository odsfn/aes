<?php

class m140703_083811_add_ability_to_user_became_elector extends EDbMigration
{
    public function up()
    {
        $this->addColumn('elector', 'status', 'tinyint unsigned not null default 0');
        
        $auth = Yii::app()->authManager;
        
        $auth->createOperation('election_addElector', '', 
            $checkStatusRule = 'return (isset($params["election"]) '
                . '&& $params["election"]->status == Election::STATUS_REGISTRATION);'
        );
        $auth->createOperation('election_activateElector', '', $checkStatusRule);
        $auth->createOperation('election_blockElector', '', 
            'return (isset($params["election"]) '
                . '&& $params["election"]->status == Election::STATUS_ELECTION);'
        );
        
        $item = $auth->getAuthItem('election_manage');
        $item->addChild('election_addElector');
        $item->addChild('election_activateElector');
        $item->addChild('election_blockElector');
        
        $task = $auth->createTask('election_askToBecameElector', '', 
            'return (isset($params["election"]) && isset($params["userId"])'
                . '&& $params["election"]->voter_reg_type == Election::VOTER_REG_TYPE_SELF'
                . '&& $params["election"]->status == Election::STATUS_REGISTRATION'
                . '&& $params["elector_user_id"] == $params["userId"] '
                . '&& !count($params["election"]->electors('
                        . 'array("condition"=>"user_id=".$params["userId"])'
                    . '))'
            . ');'
        );
        $task->addChild('election_addElector');
        
        $auth->getAuthItem('authenticated')->addChild('election_askToBecameElector');
    }

    public function down()
    {
        $this->dropColumn('elector', 'status');
        
        $auth = Yii::app()->authManager;
        $authItems = array(
            'election_askToBecameElector','election_addElector',
            'election_activateElector','election_blockElector'
        );
        foreach ($authItems as $item)
            $auth->removeAuthItem($item);
    }
}