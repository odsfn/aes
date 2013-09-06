<?php

class m130906_083257_add_post_placement extends EDbMigration
{
    public function up()
    {
        $this->createTable('post_placement', array(
            'id'      => 'pk',
            'post_id' => 'INT(11) NOT NULL',
            'target_id'     => 'INT(11) UNSIGNED NOT NULL',
            'target_type'   => 'TINYINT UNSIGNED NOT NULL DEFAULT 0',  // user_page, group, page
            'placed_ts'     => 'TIMESTAMP NOT NULL DEFAULT "0000-00-00"',
            'placer_id'     => 'int(11) NULL DEFAULT NULL'
        ));

        $this->addForeignKey('fk_post_placement_post_id', 'post_placement', 'post_id', 'post', 'id', 'CASCADE', 'NO ACTION');
        $this->addForeignKey('fk_post_placement_placer_id', 'post_placement', 'placer_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('post_placement');
    }
}