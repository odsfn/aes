<?php
$submittingTs1 = new DateTime;
$submittingTs1->sub(new DateInterval('P6M1D'));

$expirationTs1 = new DateTime;
$expirationTs1->add(new DateInterval('P1M'));

$expTs2 = new DateTime();
$expTs2->add(new DateInterval('P1Y'));

return array(
    array('id' => '1','election_id' => '1','candidate_id' => '3','name' => 'Mandate of Election 1',
        'submiting_ts' => $submittingTs1->format('Y-m-d H:i:s'),
        'expiration_ts' => $expirationTs1->format('Y-m-d H:i:s'),
        'validity' => '6','votes_count' => '2','status' => '0'
    ),
    'superman' => array(
        'id' => '2','election_id' => '2','candidate_id' => '5','name' => 'Superman',
        'submiting_ts' => '2014-02-11 00:00:00',
        'expiration_ts' => $expTs2->format('Y-m-d H:i:s'),
        'validity' => '12','votes_count' => '2','status' => '2'
    ),
);