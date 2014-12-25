<?php

class m141223_001717_add_ability_to_control_gallery_by_manager extends EDbMigration
{
    public function safeUp()
    {
        $bizRule = <<<'CODE'
if(!isset($params["item"]) && !isset($params["targetId"]))
    $result = false;

$targetId = (isset($params["targetId"])) ? $params["targetId"] : $params["item"]->target_id;

$election = Election::model()->findByAttributes(array(
    "target_id" => $targetId
));

if(!$election) { 
    $result = false;
} else {    
    $result = Yii::app()->user->checkAccess('election_administration', 
        array("election" => $election)
    );
}

return $result;
CODE;
        $manager = Yii::app()->authManager;
        $t = $manager->createTask('election_galleryManagement', 
            'Adapter to AlbumModule auth items. This task allows to manage gallary '
            . 'for election managers', 
            $bizRule
        );
        $t->addChild('album_manageGItem');
        $t->addChild('album_viewGItem');
    }

    public function safeDown()
    {
        Yii::app()->authManager->removeAuthItem('election_galleryManagement');
    }
}