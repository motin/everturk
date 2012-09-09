<?php

class EvernoteController extends Controller
{

	public function actionConnect()
	{

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

		$this->render('connect', compact("return", "lastError", "currentStatus"));
	}

}