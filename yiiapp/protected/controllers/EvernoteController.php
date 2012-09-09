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

						// If we have a proper authentication, we store it
						if ($return)
						{
							$user = User::model()->findOrCreateByPk($_SESSION['userId']);

							$toSave = new EvernoteAuthorization;
							$toSave->requestToken = $_SESSION['requestToken'];
							$toSave->requestTokenSecret = $_SESSION['requestTokenSecret'];
							$toSave->oauthVerifier = $_SESSION['oauthVerifier'];
							$toSave->accessToken = $_SESSION['accessToken'];
							$toSave->noteStoreUrl = $_SESSION['noteStoreUrl'];
							$toSave->webApiUrlPrefix = $_SESSION['webApiUrlPrefix'];
							$toSave->tokenExpires = $_SESSION['tokenExpires'];
							$toSave->user_id = $user->id;
							$toSave->created = date("Y-m-d H:i:s", time());

							if (!$toSave->save())
								throw new SaveException($toSave);
						}

						// Create initial notebooks and stacks that the service expects
						$user->updateStructure($return);

						// Submit notes (not yet submitted)
						$user->submitNotes();
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