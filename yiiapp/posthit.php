<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require_once('protected/extensions/turk50/Turk50.php');
require_once('protected/config/secrets.php');
$mturk = new Turk50(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, array("sandbox" => FALSE));

if ($_GET['q'])
{
	$q = $_GET['q'];
} else
{
	echo "no question";
	exit;
}

// prepare ExternalQuestion
$Question = ' <QuestionForm xmlns="http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2005-10-01/QuestionForm.xsd"><Overview><Title></Title><Text>Please anwser the followingtask:</Text></Overview><Question><QuestionIdentifier>1</QuestionIdentifier><DisplayName></DisplayName><IsRequired>true</IsRequired><QuestionContent><Text>' . $q . '</Text></QuestionContent><AnswerSpecification><FreeTextAnswer><Constraints><Length minLength="2" maxLength="20000" /></Constraints><DefaultText></DefaultText></FreeTextAnswer></AnswerSpecification></Question></QuestionForm>';

// prepare Request
$Request = array(
	"Title" => $q,
	"Description" => "Bar",
	"Question" => $Question,
	"Reward" => array("Amount" => "0.01", "CurrencyCode" => "USD"),
	"AssignmentDurationInSeconds" => "30",
	"LifetimeInSeconds" => "30"
);

// invoke CreateHIT
$CreateHITResponse = $mturk->CreateHIT($Request);
?>