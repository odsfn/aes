<?php

class m140620_104559_add_election_revote_config_fields extends EDbMigration
{
	public function up()
	{
            $this->addColumn('election', 'revotes_count', 'tinyint unsigned not null default 0');
            $this->addColumn('election', 'remove_vote_time', 'int unsigned not null default 0');
            $this->addColumn('election', 'revote_time', 'int unsigned not null default 0');
            
            $this->update('AuthItem', 
                array(
                    'bizrule' => 'return (isset($params["vote"]) && ($params["vote"]->candidate->user_id == $params["userId"] || $params["vote"]->user_id == $params["userId"]));'
                ), 
                'name = "election_updateVoteStatus"'
            );
	}

	public function down()
	{
            $this->dropColumn('election', 'revotes_count');
            $this->dropColumn('election', 'remove_vote_time');
            $this->dropColumn('election', 'revote_time');
            
            $this->update('AuthItem', 
                array(
                    'bizrule' => 'return (isset($params["vote"]) && $params["vote"]->candidate->user_id == $params["userId"]);'
                ), 
                'name = "election_updateVoteStatus"'
            );            
	}
}