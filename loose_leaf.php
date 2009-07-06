<?php
//getting allll loooooosey goooosey.
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));
require_once 'defines.php';
require_once 'lib/functions.php';
require_once 'lib/classes/LL_Config.php';

LL_Config::configure('ll_config.ini');

require_once 'lib/classes/DB.php';

try {
	$db_config = LL_Config::$config['db'];
	$DB = DB::DB($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['name']);
} catch(Exception $e) {
	echo $e->getMessage();
	exit(1);
}

require_once 'lib/classes/Router.php';
require_once 'lib/classes/Dispatcher.php';
require_once 'lib/classes/Controller.php';
require_once 'lib/classes/Object.php';
require_once 'lib/classes/Template.php';
require_once 'lib/classes/View.php';

if(true == LL_Config::$config['db_jedi']['enabled']) {
	require_once 'lib/classes/DB_Jedi.php';
	$DBJ = new DB_Jedi(LL_Config::$config['db_jedi']['dir']);
}
?>
