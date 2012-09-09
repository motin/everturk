<?php

// auto-loading fix
Yii::setPathOfAlias('User', dirname(__FILE__));
Yii::import('User.*');

class User extends BaseUser
{

	// Array that holds meta-information about the structure of notebooks and stacks that Everturk utilizes
	public $structure = array(
		'notebooks' => array(
			'input' => array(),
			'pending' => array(),
			'results' => array(),
		),
		'template_notebook' => null,
		'templates' => array(),
	);

	// Add your model-specific methods here. This file will not be overriden by gtc except you force it.
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function init()
	{
		return parent::init();
	}

	public function __toString()
	{
		return (string) $this->created;
	}

	public function behaviors()
	{
		return array_merge(parent::behaviors(), array(
		    ));
	}

	public function rules()
	{
		return array_merge(
			/* array('column1, column2', 'rule'), */
			parent::rules()
		);
	}

	public function findOrCreateByPk($id)
	{
		$user = $this->findByPk($id);
		if (empty($user))
		{
			$user = new User;
			$user->id = $id;
			$user->created = date("Y-m-d H:i:s", time());
			if (!$user->save())
				throw new SaveException($user);
		}
		return $user;
	}

	public function getAuthToken()
	{

		// Get from session if available
		if (!empty($_SESSION['authToken']))
			return $_SESSION['authToken'];

		// Otherwise, find the user's most recently stored authToken
		throw new Exception("TODO");
	}

	public function getStructure($existingNotebooks = null)
	{
		if (empty($this->structure))
		{
			$this->updateStructure($existingNotebooks = null);
		}
		return $this->structure;
	}

	public function updateStructure($existingNotebooks = null)
	{

		// Get existing notebooks
		if (is_null($existingNotebooks))
		{
			$_SESSION['authToken'] = $this->getAuthToken();
			$existingNotebooks = listNotebooks();
		}

		// Create template notebook if not exists
		$name = 'Everturk Task Templates';
		$stack = '';
		$exists = false;
		$notebook = null;
		foreach ($existingNotebooks as $en)
		{
			if ($en->name == $name && $en->stack == $stack)
			{
				$exists = true;
				$notebook = $en;
				break;
			}
		}
		if (!$exists)
		{
			$notebook = createNotebookByNameAndStack($name, $stack);
		}

		$this->structure['template_notebook'] = $notebook;

		// Get template notes
		$notes = findNotesByNotebookGuidOrderedByCreated($notebook->guid, $offset = 0, $maxNotes = 100);
		$this->structure['templates'] = $notes->notes;

		// Create default template note if no templates exists
		if ($notes->totalNotes == 0)
		{

			// Create default template note
			// TODO
			//
			// Refresh list of existing notebooks
			// TODO

			throw new Exception("TODO");
		}

		// Create input, pending and result notebooks based on the template notes
		$input_notebooks = array();
		$pending_notebooks = array();
		$result_notebooks = array();

		foreach ($this->structure['templates'] as $template)
		{
			$input_notebooks[] = $template->title;
			$pending_notebooks[] = $template->title . " (Pending)";
			$result_notebooks[] = $template->title . " (Results)";
		}

		$notebookStructure = array(
			'input' => $input_notebooks,
			'pending' => $pending_notebooks,
			'results' => $result_notebooks,
		);

		foreach ($notebookStructure as $stack => $names)
		{

			// Friendly names
			if ($stack == 'input')
				$stackName = 'Everturk Input';
			if ($stack == 'pending')
				$stackName = 'Everturk Pending';
			if ($stack == 'results')
				$stackName = 'Everturk Results';

			foreach ($names as $name)
			{
				$notebook = null;
				$exists = false;

				foreach ($existingNotebooks as $en)
				{
					if ($en->name == $name && $en->stack == $stackName)
					{
						$exists = true;
						$notebook = $en;
						break;
					}
				}
				if (!$exists)
				{
					$notebook = createNotebookByNameAndStack($name, $stackName);
				}
				$this->structure['notebooks'][$stack][] = $en;
			}
		}
	}

	public function submitNotes()
	{

		$structure = $this->getStructure();
		//var_dump(__LINE__, $structure);
		//
		// List notes in input notebooks
		foreach ($structure['notebooks']['input'] as $k => $notebook)
		{
			$notes = findNotesByNotebookGuidOrderedByCreated($notebook->guid, $offset = 0, $maxNotes = 100);

			foreach ($notes->notes as $input_note)
			{
				//var_dump(__LINE__, $note);
				//
				// Find the template note - matching simply by name atm
				$template_note = null;
				foreach ($structure['templates'] as $template)
				{
					if ($template->title == $notebook->name)
					{
						$template_note = $template;
						break;
					}
					//var_dump(__LINE__, $template->title, $notebook->name);
				}

				if (empty($template_note))
				{
					var_dump("The template note for the input note was not found", $template_note);die();
					throw new Exception("The template note for the input note was not found");
				}

				$input_note_full = getNote($input_note->guid, $withContent = true);
				//var_dump(__LINE__, $input_note, $input_note_full);
				// Extract note variables
				$variables = array();

				// {note-title}
				$variables['{node-title}'] = htmlspecialchars($input_note->title);
				//$variables['i'] = 'b';

				// {note-audio-length-seconds}
				// {node-wordcount}

				$template_note_full = getNote($template_note->guid, $withContent = true);
				//var_dump(__LINE__, $template_note, $template_note_full);
				//
				// Replace variables in template content
				//var_dump(__LINE__, $variables, array_keys($variables), array_values($variables));
				$template_note_title = $template_note->title;
				$template_note_title = str_replace(array_keys($variables), array_values($variables), $template_note_title);
				//var_dump($template_note_title);

				$template_note_title = str_replace('{node-title}', htmlspecialchars($input_note->title), $template_note_title);
				$template_note_title = str_replace('{note-title}', htmlspecialchars($input_note->title), $template_note_title);

				// Extract default values
				// Start with values that will result in an invalid submission
				$defaults = array();
				$defaults['MaxAssignments'] = -1;
				$defaults['Reward_USD'] = -0.01;
				$defaults['LifetimeInSeconds'] = -1;
				$defaults['AssignmentDurationInSeconds'] = -1;

				//$include_content = true;
				$link_to_content = true;
				$template_contents = strip_tags($template_note_full->content);
				$template_rows = explode("\n", $template_contents);
				//var_dump(__LINE__, $template_contents, $template_rows);

				foreach ($template_rows as $template_row)
				{
					$template_row = trim($template_row);

					// Ignore sneaky evernote-character
					$template_row = str_replace('Â', '', $template_row);

					if (empty($template_row))
						continue;

					/*
					  if ($template_row == 'do not include content')
					  {
					  $include_content = false;
					  continue;
					  }

					  if ($template_row == 'include content')
					  {
					  $include_content = true;
					  continue;
					  }

					  if ($template_row == 'do not link to content')
					  {
					  $link_to_content = false;
					  continue;
					  }

					  if ($template_row == 'link to content')
					  {
					  $link_to_content = true;
					  continue;
					  }
					 */

					// .:: Extract some general information
					// MaxAssignments
					//X worker(s)
					preg_match($p = '/(\d+)\s+worker(s)?/', $template_row, $m);
					//var_dump($p, $template_row, $m);
					if (!empty($m[1]))
					{
						$defaults['MaxAssignments'] = (int) $m[1];
						continue;
					}

					// Reward_USD
					//X$ per worker
					preg_match($p = '/([\.\d]+)(\s)?\$\s+per\s+worker(s)?/', $template_row, $m);
					//var_dump($p, $template_row, $m);
					if (!empty($m[1]))
					{
						$defaults['Reward_USD'] = (float) $m[1];
						continue;
					}

					// LifetimeInSeconds
					//expires in 2 hours
					preg_match($p = '/expires\s+in\s+([\.\d]+)\s+hour(s)?/', $template_row, $m);
					//var_dump($p, $template_row, $m);
					if (!empty($m[1]))
					{
						$defaults['LifetimeInSeconds'] = (int) $m[1] * 60 * 60;
						continue;
					}
					preg_match($p = '/expires\s+in\s+([\.\d]+)\s+minute(s)?/', $template_row, $m);
					//var_dump($p, $template_row, $m);
					if (!empty($m[1]))
					{
						$defaults['LifetimeInSeconds'] = (int) $m[1] * 60;
						continue;
					}
					preg_match($p = '/expires\s+in\s+([\.\d]+)\s+second(s)?/', $template_row, $m);
					//var_dump($p, $template_row, $m);
					if (!empty($m[1]))
					{
						$defaults['LifetimeInSeconds'] = (int) $m[1];
						continue;
					}

					// AssignmentDurationInSeconds
					//max 30 minutes to complete
					preg_match($p = '/max\s+([\.\d]+)\s+minute(s)?\s+to\s+complete/', $template_row, $m);
					//var_dump($p, $template_row, $m);
					if (!empty($m[1]))
					{
						$defaults['AssignmentDurationInSeconds'] = (int) $m[1] * 60;
						continue;
					}
				}

				// If link to content
				if ($link_to_content)
				{
					$sharedNoteUrl = getSharedNoteUrl($input_note->guid);
					//var_dump(__LINE__, $sharedNoteUrl);
				}

				// Extract overrides
				// TODO later
				// 
				// Create HIT
				$title = $template_note_title;

				// Prepare Question
				$Question = '
					<QuestionForm xmlns="http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2005-10-01/QuestionForm.xsd">
						<Overview>
							<Title>Important:</Title>
							<Text>Any texts or other media referred to in the task (if any) is available here: ' . $sharedNoteUrl . '</Text>
						</Overview>
						<Question>
							<QuestionIdentifier>1</QuestionIdentifier>
							<DisplayName>Your task:</DisplayName>
							<IsRequired>true</IsRequired>
							<QuestionContent>
								<Text>' . htmlspecialchars($title) . '</Text>
							</QuestionContent>
							<AnswerSpecification>
								<FreeTextAnswer>
									<Constraints>
										<Length minLength="2" maxLength="20000" />
									</Constraints>
									<DefaultText></DefaultText>
								</FreeTextAnswer>
							</AnswerSpecification>
						</Question>
					</QuestionForm>';

				// Replace variables in HIT content
				$Question = str_replace(array_keys($variables), array_values($variables), $Question);

				// Prepare Request
				$Request = array(
					"Title" => $title,
					"Description" => $title,
					"Question" => str_replace(array("\n", "\t", "\r"), "", $Question),
					"Reward" => array("Amount" => $defaults['Reward_USD'], "CurrencyCode" => "USD"),
					"AssignmentDurationInSeconds" => $defaults['AssignmentDurationInSeconds'],
					"LifetimeInSeconds" => $defaults['LifetimeInSeconds'],
					"MaxAssignments" => $defaults['MaxAssignments'],
				);

				// While developing
				//var_dump(__LINE__, $Request);continue;
				//
				// Submit HIT
				Yii::import('ext.turk50.Turk50');
				$mturk = new Turk50(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, array("sandbox" => FALSE));
				$CreateHITResponse = $mturk->CreateHIT($Request);

				//var_dump(__LINE__, $Request, $CreateHITResponse, $CreateHITResponse->HIT->Request);

				if ($CreateHITResponse->HIT->Request->IsValid == 'True')
				{
					// Move note from input to pending notebook
					$toNotebookGuid = $structure['notebooks']['pending'][$k]->guid;
					$result = moveNote($input_note, $toNotebookGuid);
					//var_dump(__LINE__, $result);
				} else
				{
					var_dump(compact("problem_with_submission"), $CreateHITResponse->HIT->Request);
				}
			}
		}
	}

}
