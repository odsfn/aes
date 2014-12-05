<?php

class m141124_205504_add_video extends EDbMigration
{
    public function up()
    {
        $this->createTable('video_album', array(
            'id' => 'pk',
            'target_id' => 'int(11) NOT NULL',
            'user_id' => 'int(11) NOT NULL',
            'name' => 'varchar(255) DEFAULT NULL',
            'description' => 'text',
            'cover_id' => 'int(11) null default null',
            'permission' => 'int(5) NOT NULL DEFAULT 0',
            'created' => 'timestamp NULL DEFAULT NULL',
            'update' => 'timestamp NULL DEFAULT NULL'
        ));
        
        $this->createTable('video', array(
            'id' => 'pk',
            'target_id' => 'int(11) NOT NULL',
            'user_id' => 'int(11) NOT NULL',
            'album_id' => 'int(11) DEFAULT NULL',
            'url' => 'varchar(512) NOT NULL',
            'path' => 'varchar(255) DEFAULT NULL',
            'description' => 'text',
            'permission' => 'int(5) DEFAULT 0',
            'created' => 'timestamp NULL DEFAULT NULL',
            'update' => 'timestamp NULL DEFAULT NULL',
        ));
        
        $this->addForeignKey('fk_video_album_target', 'video_album', 'target_id', 'target', 'target_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_video_album_user', 'video_album', 'user_id', 'user', 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('fk_video_album_cover', 'video_album', 'cover_id', 'video', 'id', 'SET NULL', 'CASCADE');
        
        $this->addForeignKey('fk_video_target', 'video', 'target_id', 'target', 'target_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_video_user', 'video', 'user_id', 'user', 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('fk_video_album_id', 'video', 'album_id', 'video_album', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk_video_target', 'video');
        $this->dropForeignKey('fk_video_user', 'video');
        $this->dropForeignKey('fk_video_album_id', 'video');
        
        $this->dropForeignKey('fk_video_album_target', 'video_album'); 
        $this->dropForeignKey('fk_video_album_user', 'video_album');
        $this->dropForeignKey('fk_video_album_cover', 'video_album');
        
        $this->dropTable('video');
        $this->dropTable('video_album');
    }
}