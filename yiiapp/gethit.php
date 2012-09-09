<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require_once('protected/extensions/turk50/Turk50.php');
require_once('protected/config/secrets.php');
$mturk = new Turk50(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, array("sandbox" => FALSE));

$Request = array(
	"PageSize" => "10"
);
$response = $mturk->GetReviewableHITs($Request);
echo "<pre>";
$array = $response->GetReviewableHITsResult->HIT;
foreach ($array as $hit)
{

	echo "<br>hit " . $hit->HITId . "<br>";
	$Request2 = array(
		"HITId" => $hit->HITId
	);

	$response2 = $mturk->GetAssignmentsForHIT($Request2);
	$response3 = $mturk->GetHIT($Request2);
	echo "<br>Q:";
	echo $response3->HIT->Question;
	echo "<br>A:";
	echo $response2->GetAssignmentsForHITResult->Assignment->Answer;
//echo "<br>r2";
//print_r($response2); 	
//echo "<br>r3";
//print_r($response3); 
}
echo "test";
echo "</pre>";
?>