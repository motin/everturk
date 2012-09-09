<?php

class ValidateException extends Exception
{

	public $model;

	public function __construct(&$model, $message = "", $code = null, $previous = null)
	{

		//U::rollbackCurrentTransaction();

		// Informative exception message
		$this->model = $model;
		$errors = $model->getErrors();
		if (empty($errors))
		{
			$this->model->validate();
			$errors = $this->model->getErrors();
			$message = "Errors (after extra validation): " . print_r($errors, true);
		} else
		{
			$message = "Errors: " . print_r($errors, true);
		}
		parent::__construct($message, $code, $previous);
	}

}