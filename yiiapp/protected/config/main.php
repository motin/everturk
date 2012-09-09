<?php

// Always use UTC
date_default_timezone_set('UTC');

require('secrets.php');

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'Everturk',
	// preloading 'log' component
	'preload' => array('log'),
	// autoloading model and component classes
	'import' => array(
		'application.models.*',
		'application.components.*',
		'application.exceptions.*',
		'ext.gtc.components.*'
	),
	'modules' => array(
		// uncomment the following to enable the Gii tool
		'gii' => array(
			'class' => 'system.gii.GiiModule',
			'password' => 'Enter Your Password Here',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters' => array('127.0.0.1', '::1'),
			'generatorPaths' => array(
				'ext.gtc', // Gii Template Collection
			),
		),
	),
	// application components
	'components' => array(
		'user' => array(
			// enable cookie-based authentication
			'allowAutoLogin' => true,
		),
		// uncomment the following to enable URLs in path-format
		// uncomment the following to enable URLs in path-format
		'urlManager' => array(
			'urlFormat' => 'path',
			'rules' => array(
				'/' => 'site/index',
				// Hackingly support plural forms of controller in urls
				'<controller:\w+>s/<action:\w+>' => '<controller>/<action>',
			/*
			  '<controller:\w+>/<id:\d+>'=>'<controller>/view',
			  '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
			  '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			 */
			),
			'showScriptName' => false
		),
		'db' => array(
			'connectionString' => 'mysql:host=' . YII_DB_HOST . (defined('YII_DB_PORT') ? ';port=' . YII_DB_HOST : '') . ';dbname=' . YII_DB_NAME,
			'emulatePrepare' => true,
			'username' => YII_DB_USER,
			'password' => YII_DB_PASSWORD,
			'charset' => 'utf8',
		//'schemaCachingDuration'=>3600*24,
		),
		'errorHandler' => array(
			// use 'site/error' action to display errors
			'errorAction' => 'site/error',
		),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning',
				),
			// uncomment the following to show log messages on web pages
			/*
			  array(
			  'class'=>'CWebLogRoute',
			  ),
			 */
			),
		),
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => array(
		// this is used in contact page
		'adminEmail' => 'webmaster@example.com',
	),
);