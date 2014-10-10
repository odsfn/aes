<?php

class m141010_135447_add_election_image_column extends EDbMigration
{
    public function up()
    {
        $this->addColumn('election', 'image', 'varchar(256) NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('election', 'image');
    }
}