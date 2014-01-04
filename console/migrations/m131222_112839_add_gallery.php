<?php

class m131222_112839_add_gallery extends EDbMigration
{
    public function up() {
        $this->createTable( 'gallery', array(
            'id' => 'pk',
            'versions_data' => 'text NOT NULL',
            'name' => 'boolean NOT NULL DEFAULT 1',
            'description' => 'boolean NOT NULL DEFAULT 1'
        ) );

        $this->createTable( 'gallery_photo', array(
            'id' => 'pk',
            'gallery_id' => 'integer NOT NULL',
            'rank' => 'integer NOT NULL DEFAULT 0',
            'name' => 'string NOT NULL',
            'description' => 'text',
            'file_name' => 'string NOT NULL'
        ) );

        $this->addForeignKey( 'fk_gallery_photo_gallery1', 'gallery_photo', 'gallery_id', 'gallery', 'id', 'NO ACTION', 'NO ACTION' );

        $this->addColumn('election', 'gallery_id', 'INT DEFAULT NULL');
        $this->addForeignKey( 'fk_election_gallery1', 'election', 'gallery_id', 'gallery', 'id', 'SET NULL', 'CASCADE' );

    }

    public function down() {

        $this->dropForeignKey('fk_election_gallery1', 'election');
        $this->dropColumn('election', 'gallery_id');
        $this->dropTable( 'gallery_photo' );
        $this->dropTable( 'gallery' );

    }
}