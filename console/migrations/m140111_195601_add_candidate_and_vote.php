<?php

class m140111_195601_add_candidate_and_vote extends EDbMigration
{
	public function up()
	{
            $this->createTable('candidate', array(
                'id' => 'pk',
                'user_id' => 'int(11) NOT NULL',
                'election_id'   => 'int(11) NOT NULL',
                'appointer_id'  => 'int(11) DEFAULT NULL',
                'status'  => 'tinyint NOT NULL DEFAULT 0',
                'electoral_list_pos' => 'int DEFAULT NULL'
            ));
            
            $this->createIndex('ux_candidate_user_id_election_id', 'candidate', 'user_id, election_id', true);
            $this->addForeignKey('fk_candidate_user_id', 'candidate', 'user_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk_candidate_election_id', 'candidate', 'election_id', 'election', 'id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk_candidate_appointer_id', 'candidate', 'appointer_id', 'user_profile', 'user_id', 'NO ACTION', 'NO ACTION');
            
            $this->createTable('vote', array(
                'id'    => 'pk',
                'date'  => 'DATETIME NOT NULL DEFAULT "0000-00-00"',
                'election_id'   => 'int(11) NOT NULL',
                'candidate_id' => 'int(11) NOT NULL',
                'user_id'      => 'int(11) NOT NULL',
                'status'       => 'tinyint NOT NULL DEFAULT 0'
            ));
            
            $this->createIndex('ux_vote_candidate_id_user_id', 'vote', 'candidate_id, user_id', true);
            $this->addForeignKey('fk_vote_election_id', 'vote', 'election_id', 'election', 'id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk_vote_candidate_id', 'vote', 'candidate_id', 'candidate', 'id', 'CASCADE' , 'NO ACTION');
            $this->addForeignKey('fk_vote_user_id', 'vote', 'user_id', 'user_profile', 'user_id', 'CASCADE' , 'NO ACTION');
            
            $auth = Yii::app()->authManager;
            $auth->createOperation('election_createCandidate');
            $auth->createOperation('election_updateCandidateStatus');
            $auth->createOperation('election_deleteCandidate');
            
            $task = $auth->createTask('election_manageCandidates');
            $task->addChild('election_createCandidate');
            $task->addChild('election_updateCandidateStatus');
            $task->addChild('election_deleteCandidate');
            
            $task = $auth->createTask('election_updateCandidateOwnStatus', '',
                'return ( isset($params["candidate"]) && $params["candidate"]->user_id == $params["userId"] 
                    && ( $params["election"]->status == Election::STATUS_REGISTRATION 
                        && ($params["candidate"]->status == Candidate::STATUS_INVITED && $params["status"] == Candidate::STATUS_REGISTERED) 
                        || ($params["candidate"]->status == Candidate::STATUS_REGISTERED || $params["candidate"]->status == Candidate::STATUS_INVITED && $params["status"] == Candidate::STATUS_REFUSED)
                       ) 
                    );');
            $task->addChild('election_updateCandidateStatus');
            
            $item = $auth->getAuthItem('election_administration');
            $item->addChild('election_manageCandidates');
            
            $task = $auth->createTask('election_selfAppointment', '', 'return (isset($params["election"]) && $params["election"]->cand_reg_type == 0);');
            $task->addChild('election_createCandidate');
            
            $task = $auth->createTask('election_selfDeletion', '', 'return (isset($params["candidate"]) && $params["candidate"]->user_id == $params["userId"]);');
            $task->addChild('election_deleteCandidate');
            
            $item = $auth->createOperation('election_createVote');
            
            $item = $auth->createOperation('election_updateVoteStatus', '',
                    'return (isset($params["vote"]) && $params["vote"]->candidate->user_id == $params["userId"]);'
            );
            
            $item = $auth->createOperation('election_deleteVote', '' , 
                    'return (isset($params["vote"]) && $params["vote"]->user_id == $params["userId"]);'
            );
            
            $task = $auth->createTask('election_electing');
            $task->addChild('election_createVote');
            $task->addChild('election_deleteVote');
            
            $role = $auth->createRole('election_elector');
            $role->addChild('election_electing');
            
            $role = $auth->createRole('election_candidate');
            $role->addChild('election_electing');
            $role->addChild('election_updateVoteStatus');
	}

	public function down()
	{
            $this->dropTable('vote');
            $this->dropTable('candidate');
            
            $auth = Yii::app()->authManager;
            
            $authItems = array(
                'election_selfDeletion','election_selfAppointment','election_manageCandidates',
                'election_deleteCandidate','election_updateCandidateStatus', 'election_updateCandidateOwnStatus',
                'election_createCandidate', 'election_candidate', 'election_elector',
                'election_electing', 'election_updateVoteStatus', 'election_createVote',
                'election_deleteVote'
            );
            
            foreach ($authItems as $item)
                $auth->removeAuthItem($item);            
	}

}