<?php

class SaveException extends ValidateException
{

	public $model;

	public function __construct(&$model, $message = "", $code = null, $previous = null)
	{

		//U::rollbackCurrentTransaction();
		parent::__construct($model, $message, $code, $previous);

	}

}