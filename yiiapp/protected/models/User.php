<?php

// auto-loading fix
Yii::setPathOfAlias('User', dirname(__FILE__));
Yii::import('User.*');

class User extends BaseUser
{

	// Array that holds meta-information about the structure of notebooks and stacks that Everturk utilizes
	public $structure = null;

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

		// Hard-coded template
		$templates = array(
			'Do as I say',
			'Summarize this web clip',
			'Translate this into french',
			'Translate this into german',
		);

		$pending_notebooks = array();
		foreach ($templates as $template)
			$pending_notebooks[] = $template . " (Pending)";

		$result_notebooks = array();
		foreach ($templates as $template)
			$result_notebooks[] = $template . " (Results)";

		$notebookStructure = array(
			'Everturk Input' => $templates,
			'Everturk Pending' => $pending_notebooks,
			'Everturk Results' => $result_notebooks,
		);

		//var_dump($existingNotebooks);

		foreach ($notebookStructure as $stack => $names)
		{

			foreach ($names as $name)
			{
				$exists = false;

				foreach ($existingNotebooks as $en)
				{
					if ($en->name == $name && $en->stack == $stack)
					{
						$exists = true;
						break;
					}
				}
				if (!$exists)
				{
					$notebook = createNotebookByNameAndStack($name, $stack);
					//var_dump($notebook);
				}
			}
		}

		$this->structure = array(1, 2, 4, 5, 6, 7, 8);
	}

	public function submitNotes()
	{

		$structure = $this->getStructure();

		var_dump(__LINE__, $structure);

		// List notes
	}

}
