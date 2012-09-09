<?php

//  Merge-approach from http://www.yiiframework.com/forum/index.php?/topic/23899-merging-mainphp-and-commandphp-config-files/
$mainConfigArray = require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'main.php';

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'Everturk Console Application',
	'import' => array_merge($mainConfigArray['import'], array(
		'application.commands.components.*',
	    )
	),
	// preloading 'log' component
	'preload' => array('log'),
	// application components
	'components' => array(
		'db' => $mainConfigArray['components']['db'],
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning',
				),
			),
		),
	),
);