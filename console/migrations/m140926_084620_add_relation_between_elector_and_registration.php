<?php

class m140926_084620_add_relation_between_elector_and_registration extends EDbMigration
{

    public function up()
    {
        $this->addColumn('elector', 'registration_request_id', 'int(11) default null'
        );

        $this->addForeignKey('fk_elector_registration_request', 'elector', 
            'registration_request_id', 'elector_registration_request', 'id', 
            'SET NULL', 'NO ACTION'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_elector_registration_request', 'elector');
        $this->dropColumn('elector', 'registration_request_id');
    }

}
