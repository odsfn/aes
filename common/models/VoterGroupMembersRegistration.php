<?php
/** 
 * VoterGroupMembersRegistration is the Transaction Script which perform 
 * members registration of related election's voters groups as electors
 * 
 * @TODO: Perform access control before run
 */
class VoterGroupMembersRegistration extends CComponent 
{
    const STATE_NOT_STARTED = 0;
    const STATE_RUNNING = 1;
    const STATE_FINISHED = 2;
    
    /**
     * Election 
     * @var Election
     */
    protected $election;

    protected $_state = self::STATE_NOT_STARTED;


    public function __construct($election)
    {
        $this->setElection($election);
    }
    
    public function setElection($election)
    {
        if (is_numeric($election))
            $election = Election::model()->findByPk($election);
        
        if (!$election)
            throw new CException('Election was not found');
            
        $this->election = $election;    
    }
    
    public function getElection()
    {
        return $this->election;
    }

    public function setState($state)
    {
        $this->_state = $state;
    }

    public function getState()
    {
        return $this->_state;
    }

    public function run()
    {
        $this->setState(self::STATE_RUNNING);
        
        $election_id = $this->getElection()->id; 
        
        $command = Yii::app()->db->createCommand(
            "INSERT INTO elector (election_id, user_id, status) "
                . "SELECT evg.election_id, vgm.user_id, 0 AS status "
                . "FROM election_voter_group AS evg "
                . "INNER JOIN voter_group_member AS vgm "
                    . "ON evg.voter_group_id = vgm.voter_group_id "
                        . "AND evg.election_id = $election_id "
                . "WHERE vgm.user_id NOT IN ("
                    . "SELECT user_id FROM elector WHERE election_id = $election_id"
                . ")"
                . "GROUP BY vgm.user_id"
        );
        
        $command->execute();
        
        $this->setState(self::STATE_FINISHED);
    }
}
