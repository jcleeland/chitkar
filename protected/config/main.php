<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Chitkar',
	//'theme'=>'classic',
	'theme'=>'cpsu',

    'behaviors'=>array(
        'onBeginRequest' => array(
            'class' => 'application.components.RequireLogin'
        )
    ),
    
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes       //Removed by jason when installing PHPMailer direct 26 Mar 2023
/*	'import'=>array(
		'application.models.*',
		'application.components.*',
        'ext.YiiMailer.YiiMailer',
	), */
    'import' => array(
        'application.models.*',
        'application.components.*',    
    ),

	'modules'=> array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'chitkar',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1','192.9.200.*'),
		),
		
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
            'class' => 'WebUser',
		),
       
		// uncomment the following to enable URLs in path-format
		
		/*'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),*/
		
		/* 'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),*/
		// uncomment the following to use a MySQL database
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=chitkar',
			'emulatePrepare' => true,
			'username' => 'username',
			'password' => 'password',
			'charset' => 'utf8',
            'initSQLs'=>array("SET sql_mode=(SELECT REPLACE(@@sql_mode,'NO_ZERO_DATE',''));"),
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=> YII_DEBUG ? null : array('site/error'),
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
        'dbConfig'=>array(
            'class'=>'dbConfig',
        ),
        // This forces Yii to disable caching for JS and CSS files. Normally a bad idea for production sites but OK for small user sites like Chitkar
        'components' => array(
            'assetManager' => array(
                'linkAssets' => true,
            )
        ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',

	),
);
