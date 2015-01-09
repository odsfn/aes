<?php

class m150109_215057_add_ability_to_control_comments_by_targetOwner extends EDbMigration
{
    public function safeUp()
    {
        $bizRule = <<<'RULE'
return (
    isset($params['target']) && $params['target']->hasAttribute('user_id') 
    && $params['target']->user_id == $params['userId']
);
RULE;
        $auth = Yii::app()->authManager;
        $task = $auth->createTask('moderateOwnTargetsComments', '', $bizRule);
        $task->addChild('commentModeration');
        
        $auth->getAuthItem('authenticated')->addChild('moderateOwnTargetsComments');
        
        $task = $auth->createTask('moderateOwnMandateComments', '', 
            'return ('
                . 'isset($params["mandate"]) && $params["mandate"]->candidate->user_id == $params["userId"]'
            . ');'
        );
        $task->addChild('commentModeration');
        $auth->getAuthItem('authenticated')->addChild('moderateOwnMandateComments');
    }

    public function safeDown()
    {
        $auth = Yii::app()->authManager;
        $auth->removeAuthItem('moderateOwnTargetsComments');
        $auth->removeAuthItem('moderateOwnMandateComments');
    }

}