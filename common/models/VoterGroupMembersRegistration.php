<?php
/* 
 * VoterGroupMembersRegistration is the Transaction Script which perform 
 * members registration of related election's voters groups as electors
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
    protected $_election;

    protected $status = 0;


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
            
        $this->_election = $election;    
    }
    
    public function run()
    {
        $this->setState(self::STATE_RUNNING);
        
        
        
        $this->setState(self::STATE_FINISHED);
    }
}
