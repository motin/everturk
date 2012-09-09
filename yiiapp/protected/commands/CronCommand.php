<?php

class CronCommand extends AppConsoleCommand
{

	public function actionCreateAssignmentsFromNotes($pageSize = 5, $currentPage = 1)
	{

		$modelRef = "EvernoteNotification";

		echo "\n";
		$this->status("Loading controller and model");

		$controller = $this->loadControllerByModelRef($modelRef);

		$this->status("Applying pagination etc and getting the current records");
		$criteria = $this->getCriteria($modelRef, $pageSize, $currentPage);

		// Only include certain input results
		// just for now
		//$criteria->condition = "id = 3";
		//$criteria->addCondition("");

		$model = $modelRef::model();

		$records = $model->getCommandBuilder()
		    ->createFindCommand($model->tableSchema, $criteria)
		    ->queryAll();

		foreach ($records as $k => $record)
		{
			try {
				$this->status("$modelRef id: " . $record["id"]);

				// Load model
				$en = $controller->loadModel($record["id"]);

				$this->status("$modelRef loaded id: " . $en->id);

				// Perform logic
				// TODO
			} catch (ImproperConfigurationException $e) {
				$this->exceptionStatus($e);
			} catch (Exception $e) {
				$this->exceptionStatus($e, true);
			}
		}

		if (isset($pageSize))
		{
			$this->status("Done execution with pageSize $pageSize, current page $currentPage. Memory usage: " . round(memory_get_usage() / 1024 / 1024, 2) . " MiB");
			echo "\n";
		}
	}

	public function actionCreateNotesFromFinishedAssignments($pageSize = 5, $currentPage = 1)
	{

		$modelRef = "FinishedAssignments";

		echo "\n";
		$this->status("Loading controller and model");

		$controller = $this->loadControllerByModelRef($modelRef);

		$this->status("Applying pagination etc and getting the current records");
		$criteria = $this->getCriteria($modelRef, $pageSize, $currentPage);

		// Only include certain input results
		//$criteria->condition = "sumfin IS NULL";
		//$criteria->addCondition("");

		$model = $modelRef::model();

		$records = $model->getCommandBuilder()
		    ->createFindCommand($model->tableSchema, $criteria)
		    ->queryAll();

		foreach ($records as $k => $record)
		{
			try {
				$this->status("$modelRef id: " . $record["id"]);

				// Load model
				$finishedAssignment = $controller->loadModel($record["id"]);

				$this->status("$modelRef loaded id: " . $finishedAssignment->id);

				// Perform logic
				// TODO
			} catch (ImproperConfigurationException $e) {
				$this->exceptionStatus($e);
			} catch (Exception $e) {
				$this->exceptionStatus($e, true);
			}
		}

		if (isset($pageSize))
		{
			$this->status("Done execution with pageSize $pageSize, current page $currentPage. Memory usage: " . round(memory_get_usage() / 1024 / 1024, 2) . " MiB");
			echo "\n";
		}
	}

}