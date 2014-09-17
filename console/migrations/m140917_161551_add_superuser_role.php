<?php

class m140917_161551_add_superuser_role extends EDbMigration
{
	public function up()
	{
            $auth = Yii::app()->authManager;
            $auth->createRole('superadmin', '', 
                'return (!empty($params["userId"])'
                    . ' && $params["userId"] == Yii::app()->params["superAdminId"]'
                . ');'
            );
	}

	public function down()
	{
            Yii::app()->authManager
                ->removeAuthItem('superadmin');
	}
}