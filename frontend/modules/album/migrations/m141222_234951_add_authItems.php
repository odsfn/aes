<?php
class m141222_234951_add_authItems extends EDbMigration
{
	public function safeUp()
	{
            /**
             * @var CAuthManager
             */
            $manager = Yii::app()->authManager;

            $manager->createOperation('album_createGItem');
            $manager->createOperation('album_editGItem');
            $manager->createOperation('album_deleteGItem');
            $manager->createOperation('album_viewGItem');

            $t = $manager->createTask('album_manageGItem');
            $t->addChild('album_createGItem');
            $t->addChild('album_editGItem');
            $t->addChild('album_deleteGItem');                

            $code = <<<'CODE'
if(!isset($params["target"]) && isset($params["targetId"])) {
    $target = Target::model()->findByPk((int)$params["targetId"]);
    if(!$target) 
        return false;
    $target = $target->getRow();
} else if (isset($params["target"])) {
    $target = $params["target"];
} else 
    return false;

return $target->user_id == $params["userId"];
CODE;
            
            $targetOwner = $manager->createRole('album_targetOwner', '', $code);
            $targetOwner->addChild('album_createGItem');
            
            $manager->createTask('album_manageOwnGItem', '', 'return ('
                    . 'isset($params["item"]) && '
                    . '$params["item"]->user_id == $params["userId"]'
                . ');'
            )->addChild('album_manageGItem');

            $authenticatedRole = $manager->createRole('album_authenticated', 
                'Default role to indicate authenticated gallery visitor', 
                'return (!empty($params["userId"]));'
            );
            $authenticatedRole->addChild('album_manageOwnGItem');
            $authenticatedRole->addChild('album_targetOwner');

            $manager->createTask('album_viewNotRestrictedGItem', '', 'return ('
                    . 'isset($params["item"]) && '
                    . '$params["item"]->permission == ' . AlbumModule::GALLERY_PERM_PER_ALL
                . ');'
            )->addChild('album_viewGItem');

            $notAuthenticatedRole = $manager->createRole('album_notAuthenticated',
                'Default role to indicate not authenticated gallery visitor',
                'return (empty($params["userId"]));'
            );
            
            $notAuthenticatedRole->addChild('album_viewNotRestrictedGItem');
            $authenticatedRole->addChild('album_viewNotRestrictedGItem');

            $manager->createTask('album_viewRestrictedAuthenticated', 
                'Viewing of iGalleryItem is available only for authenticated gallery visitor', 
                'return ('
                    . 'isset($params["item"]) && '
                    . '$params["item"]->permission == ' . AlbumModule::GALLERY_PERM_PER_REGISTERED
                . ');'
            )->addChild('album_viewGItem');

            $authenticatedRole->addChild('album_viewRestrictedAuthenticated');

            $manager->createTask('album_viewRestrictedOwner',
                'Viewing of iGalleryItem is available only for owner',
                'return ('
                    . 'isset($params["item"]) && '
                    . '$params["item"]->permission == ' . AlbumModule::GALLERY_PERM_PER_OWNER
                . ');'
            )->addChild('album_viewGItem');

            $manager->createRole('album_GItemOwner', '', 'return ('
                    . '$params["item"]->user_id == $params["userId"]'
                . ');'
            )->addChild('album_viewRestrictedOwner');

            $authenticatedRole->addChild('album_GItemOwner');
	}

	public function safeDown()
	{
            $manager = Yii::app()->authManager;
            
            $items = array(
                'album_GItemOwner', 'album_viewRestrictedOwner', 'album_viewRestrictedAuthenticated',
                'album_notAuthenticated', 'album_viewNotRestrictedGItem', 'album_authenticated', 
                'album_manageOwnGItem', 'album_targetOwner', 'album_manageGItem', 
                'album_viewGItem', 'album_deleteGItem', 'album_editGItem', 'album_createGItem'
            );
            
            foreach ($items as $item) {
                $manager->removeAuthItem($item);
            }
	}
}