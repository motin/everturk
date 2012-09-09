<?php

class AppConsoleCommand extends CConsoleCommand
{

	public function status($msg)
	{
		echo round(Yii::getLogger()->getExecutionTime(), 2) . "s - $msg\n";
	}

	protected function loadControllerByModelRef($modelRef)
	{
		$controllerRef = $modelRef . "Controller";
		Yii::import('application.controllers.' . $controllerRef);
		$controllerRefLcase1st = strtolower(substr($controllerRef, 0, 1)) . substr($controllerRef, 1, strlen($controllerRef) - 1);
		return new $controllerRef($controllerRefLcase1st);
	}

	protected function getCriteria($modelRef, $pageSize, $currentPage)
	{

		$criteria = new CDbCriteria;

		if ($currentPage > 1)
		{
			$pages = new CPagination($modelRef::model()->count($criteria));
			$pages->pageSize = $pageSize;
			$pages->setCurrentPage($currentPage - 1); // -1 so that the argument can be the same as in webview
			$pages->applyLimit($criteria);
		} else
		{
			$criteria->limit = $pageSize;
		}

		return $criteria;
	}

	protected function exceptionStatus(Exception $e, $throw = false)
	{
		$this->status("Exception: " . $e->getMessage()
		    . "\n File: " . $e->getFile()
		    . "\n Line: " . $e->getLine());
		if ($throw) throw $e;
	}

}