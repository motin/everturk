<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require_once('protected/extensions/turk50/Turk50.php');
require_once('protected/config/secrets.php');
$db = mysql_connect(YII_DB_HOST, YII_DB_USER, YII_DB_PASSWORD);
mysql_select_db(YII_DB_NAME, $db);
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
	if (isset($response2->GetAssignmentsForHITResult->Assignment->Answer) && $response2->GetAssignmentsForHITResult->Assignment->Answer != "")
	{
		$sql = sprintf("SELECT 1 FROM `finished_assignment`  WHERE `hitid` = '%s'", mysql_real_escape_string($hit->HITId));
		if ($result = mysql_query($sql))
		{
			if (mysql_num_rows($result) == 0)
			{
				echo "not yet processed";
				$sql2 = sprintf("INSERT INTO `finished_assignment`  (`hitid`) values ('%s')", mysql_real_escape_string($hit->HITId));
				if (mysql_query($sql2))
				{
					echo "success";
				} else
				{
					echo "error $sql2 " . mysql_error();
				}
				$response3 = $mturk->GetHIT($Request2);
				
				//Create Evernote note here with Q: $response3->HIT->Question and A: $response2->GetAssignmentsForHITResult->Assignment->Answer

				echo "Q:" . $response3->HIT->Question;
				echo "A:" . $response2->GetAssignmentsForHITResult->Assignment->Answer;
			} else
			{
				echo "already processed";
			}
		} else
		{
			echo "error $sql" . mysql_error();
		}
	} else
	{
		echo "no anwser";
	}
}
echo "test";
echo "</pre>";
?>