<?php

class m141121_174444_update_albumCover extends EDbMigration
{
    public function up()
    {
        $this->addColumn('album', 'cover_id', 'int(11) null default null');
        $this->addForeignKey('fk_album_cove_id', 'album', 'cover_id', 'file', 'id', 'SET NULL', 'CASCADE');
        
        $this->execute(
            'UPDATE album AS a, file AS f '
                . 'SET a.cover_id = f.id '
                . 'WHERE f.path = a.path'
        );
        
        $this->dropColumn('album', 'path');
    }

    public function down()
    {
        echo "m141121_174444_update_albumCover does not support migration down.\n";
        return false;
    }
}