<?php

class m141122_133818_add_created_updated_fields extends EDbMigration
{
    public function up()
    {
        $this->addColumn('album', 'created', 'timestamp NULL DEFAULT NULL');
        $this->addColumn('file', 'created', 'timestamp NULL DEFAULT NULL');
        
        $this->alterColumn('album', 'update', 'timestamp NULL DEFAULT NULL');
        $this->alterColumn('file', 'update', 'timestamp NULL DEFAULT NULL');
        
        $this->update('album', array('created'=>new CDbExpression('`update`')), 'id > 0');
        $this->update('file', array('created'=>new CDbExpression('`update`')), 'id > 0');
    }

    public function down()
    {
        $this->dropColumn('album', 'created');
        $this->dropColumn('file', 'created');
        
        $this->alterColumn('album', 'update', 'datetime NULL DEFAULT NULL');
        $this->alterColumn('file', 'update', 'datetime NULL DEFAULT NULL');
    }
}