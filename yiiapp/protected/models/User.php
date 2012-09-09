<?php

// auto-loading fix
Yii::setPathOfAlias('User', dirname(__FILE__));
Yii::import('User.*');

class User extends BaseUser
{

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

}
