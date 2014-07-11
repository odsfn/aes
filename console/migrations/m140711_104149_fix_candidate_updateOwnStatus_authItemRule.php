<?php

class m140711_104149_fix_candidate_updateOwnStatus_authItemRule extends EDbMigration
{
    public function up()
    {
        $this->update('AuthItem',array('bizrule'=>
            'return ( 
                isset($params["candidate"]) 
                && $params["candidate"]->user_id == $params["userId"] 
                && ( 
                    $params["election"]->status == Election::STATUS_REGISTRATION 
                    && ($params["candidate"]->status == Candidate::STATUS_INVITED 
                        && $params["status"] == Candidate::STATUS_REGISTERED
                        ) 
                    || ($params["candidate"]->status == Candidate::STATUS_REGISTERED 
                        || $params["candidate"]->status == Candidate::STATUS_INVITED
                        || $params["candidate"]->status == Candidate::STATUS_AWAITING_CONFIRMATION
                        && $params["status"] == Candidate::STATUS_REFUSED
                        )
                ) 
            );'),
            'name = "election_updateCandidateOwnStatus"'
        );            
    }

    public function down()
    {
        $this->update('AuthItem',array('bizrule'=>
            'return ( isset($params["candidate"]) && $params["candidate"]->user_id == $params["userId"] 
                && ( $params["election"]->status == Election::STATUS_REGISTRATION 
                    && ($params["candidate"]->status == Candidate::STATUS_INVITED && $params["status"] == Candidate::STATUS_REGISTERED) 
                    || ($params["candidate"]->status == Candidate::STATUS_REGISTERED || $params["candidate"]->status == Candidate::STATUS_INVITED && $params["status"] == Candidate::STATUS_REFUSED)
                   ) 
            );'),
            'name = "election_updateCandidateOwnStatus"'
        );
    }
}