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

		var_dump(__LINE__, $structure);

		// List notes
	}

}
