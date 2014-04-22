<?php
if(!function_exists('generateExpDate')) { 
    function generateExpDate($validity) {
        $curDate = new DateTime;
        $curDate->add(new DateInterval('P' . $validity . 'M'));
        return $curDate->format('Y-m-d H:i:s');
    }
}

return array(
    array('id' => '1','election_id' => '1','candidate_id' => '3','name' => 'Mandate of Election 1','submiting_ts' => date('Y-m-d H:i:s'), 'expiration_ts' => generateExpDate(6), 'validity' => '6','votes_count' => '2','status' => '0'),
    'superman' => array('id' => '2','election_id' => '2','candidate_id' => '5','name' => 'Superman','submiting_ts' => '2014-02-11 00:00:00','expiration_ts' => '2015-02-11 00:00:00','validity' => '12','votes_count' => '2','status' => '2'),
);
