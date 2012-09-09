<?php
// Include our OAuth functions
require_once('protected/extensions/evernote-sdk-php/sample/oauth/functions.php');

// Use a session to keep track of temporary credentials, etc
session_start();

// Status variables
$lastError = null;
$currentStatus = null;

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
				listNotebooks();
			}
		}
	} else if ($action == 'authorize')
	{
		if (getTemporaryCredentials('/evernote/connect'))
		{
			// We obtained temporary credentials, now redirect the user to evernote.com to authorize access
			header('Location: ' . getAuthorizationUrl('/evernote/connect'));
		}
	} else if ($action == 'reset')
	{
		resetSession();
	}
}
?>

<?php
if (isset($lastError))
{
	?>
	<h1>Connect with Evernote</h1>

	<p style="color:red">An error occurred: <?php echo $lastError; ?></p>

	<?php
} else if ($action != 'callback')
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
} else
{
	?>

	<p style="color:green">
		Congratulations, you have successfully authorized this application to access your Evernote account!
	</p>

	<p>
		You account contains the following notebooks:
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

	<?php } // if (isset($_SESSION['notebooks']))    ?>
<?php } // if (isset($lastError))    ?>

