<?php

class m141023_211203_init extends EDbMigration
{

    public function up()
    {
        $this->execute("CREATE TABLE `album` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `name` varchar(255) DEFAULT NULL,
              `description` text,
              `path` varchar(255) DEFAULT NULL,
              `permission` int(5) NOT NULL DEFAULT '0',
              `update` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            CREATE TABLE `file` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `album_id` int(11) DEFAULT NULL,
              `filename` varchar(255) DEFAULT NULL,
              `path` varchar(255) DEFAULT NULL,
              `update` datetime DEFAULT NULL,
              `description` text,
              `permission` int(5) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            CREATE TABLE `file_tag` (
              `fid` int(10) unsigned NOT NULL,
              `tid` int(10) unsigned NOT NULL,
              PRIMARY KEY (`fid`,`tid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            CREATE TABLE `tags` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
              `frequency` int(11) DEFAULT '1',
              PRIMARY KEY (`id`),
              KEY `id` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
        
        $this->addForeignKey('fk_file_album', 'file', 'album_id', 'album', 'id', 'SET NULL', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('file');
        $this->dropTable('album');
        $this->dropTable('file_tag');
        $this->dropTable('tags');
    }
}
