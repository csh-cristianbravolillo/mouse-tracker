<?php
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Basic definitions.
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	define('APP_NAME',				'MouseTracking v0.1');
	define('APP_URL',				'http://localhost/~cbravo/mt/player/');
	define('APP_ROOT',				'/home/cbravo/public_html/mt/');
	define('APP_PATH',				APP_ROOT . 'player/');
	define('APP_CLASSPATH',			APP_ROOT . 'classes/');
	define('SMARTY_DIR',			APP_ROOT . 'smarty/libs/');

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Needed classes.
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	require_once(APP_CLASSPATH .	'functions.php');
	require_once(SMARTY_DIR .		'Smarty.class.php');
	require_once(APP_CLASSPATH .	'CompactDB.php');
	require_once(APP_CLASSPATH .	'Template.php');
	require_once(APP_CLASSPATH .	'Element.php');
	require_once(APP_CLASSPATH .	'GenException.php');
	require_once(APP_CLASSPATH .	'Canvas.php');
	require_once(APP_CLASSPATH .	'Toolbar.php');
	require_once(APP_CLASSPATH .	'Query.php');
	require_once(APP_CLASSPATH .	'Table.php');
	require_once(APP_CLASSPATH .	'Screen.php');
?>
