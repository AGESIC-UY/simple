<?php
// system/application/plugins/doctrine_pi.php

// load Doctrine library
require_once APPPATH.'/helpers/doctrine/Doctrine.php';

// load database configuration from CodeIgniter
require_once APPPATH.'/config/database.php';

// this will allow Doctrine to load Model classes automatically
spl_autoload_register(array('Doctrine', 'autoload'));

// we load our database connections into Doctrine_Manager
// this loop allows us to use multiple connections later on
foreach ($db as $connection_name => $db_values) {

	// first we must convert to dsn format
	$dsn = $db[$connection_name]['dbdriver'] .
		'://' . $db[$connection_name]['username'] .
		':' . $db[$connection_name]['password'].
		'@' . $db[$connection_name]['hostname'] .
		'/' . $db[$connection_name]['database'];

	Doctrine_Manager::connection($dsn,$connection_name)->setCharset('UTF8');
}

// CodeIgniter's Model class needs to be loaded
require_once BASEPATH.'/core/Model.php';

// telling Doctrine where our models are located
Doctrine::loadModels(APPPATH.'/models');

// (OPTIONAL) CONFIGURATION BELOW


// this will allow us to use "mutators"
Doctrine_Manager::getInstance()->setAttribute(
	Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);


Doctrine_Manager::getInstance()->setAttribute(Doctrine_Core::ATTR_USE_NATIVE_ENUM, true);


// Esto permite validar los datos antes de almacenarlos.
//Doctrine_Manager::getInstance()->setAttribute(
//        Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);


/*
// this sets all table columns to notnull and unsigned (for ints) by default
Doctrine_Manager::getInstance()->setAttribute(
	Doctrine::ATTR_DEFAULT_COLUMN_OPTIONS,
	array('notnull' => true, 'unsigned' => true));


/*
// set the default primary key to be named 'id', integer, 4 bytes
Doctrine_Manager::getInstance()->setAttribute(
	Doctrine::ATTR_DEFAULT_IDENTIFIER_OPTIONS,
	array('name' => 'id', 'type' => 'integer', 'length' => 4));
 * 
 */