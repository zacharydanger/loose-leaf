<?php
session_start();
require_once 'config.php';
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

define('DIR_CACHE', dirname(__FILE__) . '/cache/', false);

if(false == defined('ENABLE_FIREPHP')) {
	define('ENABLE_FIREPHP', false, false);
}

require_once 'classes/DB.php';

try {
	$DB = DB::DB(DB_HOST, DB_USER, DB_PASS, DB_NAME); //instantiate just so we can test below
} catch(Exception $e) {
	//handle database connection errors...
	//TODO: email admin on error, produce a styled error page.
	?>
	<center><h1>whoops!</h1><p><strong>Could not connect to database:</strong></p><p><?php echo mysqli_connect_error(); ?></p>
	<?php exit;
}
require_once 'defines.php';
require_once 'functions.php';
require_once 'routes.php';
require_once 'classes/DB_Jedi.php';
$DBJ = new DB_Jedi(DIR_SCHEMA);

$session_token = exists('session_id', $_COOKIE, 0);
$USER = User_Session::tokenFactory($session_token);
?>