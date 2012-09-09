<?php
// Include our OAuth functions
require_once('protected/extensions/evernote-sdk-php/sample/oauth/functions.php');

// Use a session to keep track of temporary credentials, etc
session_start();

// Status variables
global $lastError, $currentStatus;
$lastError = null;
$currentStatus = null;
$return = null;

//var_dump($_SESSION);
// Request dispatching. If a function fails, $lastError will be updated.
if (isset($_GET['action']))
{
	$action = $_GET['action'];
	if ($action == 'callback')
	{
		if (handleCallback())
		{
			if (getTokenCredentials())
			{
				$return = listNotebooks();
			} else
			{
				//var_dump(__LINE__);
			}
		}
	} else if ($action == 'authorize')
	{
		if (getTemporaryCredentials('/evernote/connect'))
		{
			// We obtained temporary credentials, now redirect the user to evernote.com to authorize access
			header('Location: ' . getAuthorizationUrl('/evernote/connect'));
			exit();
		}
	} else if ($action == 'reset')
	{
		resetSession();
	}
} else
{
	// Verify access by attempting to list the user's notebooks. If it doesn't work, we need to authenticate...
	try {
		$return = listNotebooks();
	} catch (Exception $e) {
		unset($lastError);
	}
}

//var_dump(__LINE__, $return, $lastError, $GLOBALS['lastError'], $currentStatus);
?>

<?php
if (isset($lastError))
{
	?>

	<p style="color:red">An error occurred: <?php echo $lastError; ?></p>

	<?php
} else if (isset($return) && $return)
{
	?>

	<p style="color:green">
		Congratulations, you have successfully authorized this application to access your Evernote account!
	</p>

	<p>
		Your account contains the following notebooks:
	</p>

	<?php
	if (isset($_SESSION['notebooks']))
	{
		?>
		<ul>
			<?php
			foreach ($_SESSION['notebooks'] as $notebook)
			{
				?>
				<li><?php print $notebook; ?></li>
			<?php } ?>
		</ul>

		<hr/>

		<p>
			<a href="?action=reset">Click here</a> to log out.
		</p>

	<?php } // if (isset($_SESSION['notebooks']))       ?>

	<?php
} else
{
	?>

	<h1>Connect with Evernote</h1>

	<h2>Step 1 - Evernote Authentication</h2>

	<p>
		<a href="?action=authorize">Click here</a> to authorize this application to access your Evernote account. You will be directed to evernote.com to authorize access, then returned to this application after authorization is complete.
	</p>

	<hr/>

	<p>
		<a href="?action=reset">Click here</a> if you are experiencing problems and wish to start over.
	</p>

	<?php
} // if (isset($lastError))
