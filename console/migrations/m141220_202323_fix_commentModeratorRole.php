<?php

class m141220_202323_fix_commentModeratorRole extends EDbMigration
{
	public function up()
	{
            $this->update('AuthItem',array('bizrule'=>
                'return ('
                    . '!in_array("commentModerator", $params["disabledRoles"]) '
                        . '&& isset($params["target"]) '
                        . '&& $params["target"]->checkUserInRole($params["userId"], "commentModerator")'
                . ');'),
                'name = "commentModerator"'
            );
	}

	public function down()
	{   
            $this->update('AuthItem',array('bizrule'=>
                'return ('
                    . 'isset($params["target"]) '
                        . '&& $params["target"]->checkUserInRole($params["userId"], "commentModerator")'
                . ');'),
                'name = "commentModerator"'
            );
	}
}