<?php

Yii::import('console.helpers.ObjectAuthAssignmentDbManagerHelper');

class m131101_155627_add_election_auth_assignment_table extends EDbMigration
{
    public function up()
    {
        $manager = new ObjectAuthAssignmentDbManagerHelper('election', $this);
        $manager->createTables();
    }

    public function down()
    {
        $manager = new ObjectAuthAssignmentDbManagerHelper('election', $this);
        $manager->dropTables();
    }
}