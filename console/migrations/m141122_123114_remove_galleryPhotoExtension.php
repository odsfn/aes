<?php

class m141122_123114_remove_galleryPhotoExtension extends EDbMigration
{
	public function up()
	{
            $galleryMigration = Yii::app()->db->createCommand()
                ->select()
                ->from('tbl_migration')
                ->where('version = "m131222_112839_add_gallery"')
                ->queryRow();
            
            if ($galleryMigration) {
            
                $this->dropForeignKey('fk_election_gallery1', 'election');
                $this->dropColumn('election', 'gallery_id');
                $this->dropTable( 'gallery_photo' );
                $this->dropTable( 'gallery' );

                $this->delete('tbl_migration', 'version = "m131222_112839_add_gallery"');
                
            } else {
                echo 'galleryManager module migration was no found. Going next.';
            }
	}

	public function down()
	{
            echo "m141122_123114_remove_galleryPhotoExtension does not support migration down.\n";
            return false;
	}
}