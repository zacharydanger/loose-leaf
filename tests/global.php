<?php
require_once '../public_html/inc/config.php';

require_once 'config.php';

set_include_path(get_include_path() . PATH_SEPARATOR . DIR_ROOT . 'inc');

require_once 'defines.php';
require_once 'classes/DB.php';

try {
	$DB = DB::DB(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS, TEST_DB_NAME); //new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
} catch(Exception $e) {
	echo "\nERROR: Failed to connect to test database...\n" . mysqli_connect_error() . "\n\n";
	exit(1);
}

require_once 'functions/db.php';
require_once 'functions.php';

require_once 'classes/DB_Jedi.php';

$db_start = time();
echo 'Setting up database...';
echo "\n\t* dropping tables";
$query = db_query("SHOW TABLES");
$table_list = array();
while($query->num_rows > 0 && $t = $query->fetch_array()) {
	$table_list[] = "`" . $t[0] . "`";
	echo ".";
}
if(count($table_list) > 0) {
	$sql = "DROP TABLE " . implode(',', $table_list);
	db_query($sql);
}

echo "done!\n";
$DBJ = new DB_Jedi('../data/');
$DBJ_data = new DB_Jedi('testdata/sql/');
$db_finish = time() - $db_start;
echo "\t* Database built in " . $db_finish . " seconds\n";

$class_dir = DIR_ROOT . 'inc/classes';
if(true == is_dir($class_dir)) {
	if($dh = opendir($class_dir)) {
		while(($file = readdir($dh)) !== false) {
			$file_path = $class_dir . '/' . $file;
			$file_path_info = pathinfo($file);
			if("php" == $file_path_info['extension']) {
				if(false == is_dir($file_path) && true == file_exists($file_path)) {
					require_once $file_path;
				}
			}
		}
	}
}

$class_dir = DIR_ROOT . 'admin/inc/classes';
if(true == is_dir($class_dir)) {
	if($dh = opendir($class_dir)) {
		while(($file = readdir($dh)) !== false) {
			$file_path = $class_dir . '/' . $file;
			if(false == is_dir($file_path) && true == file_exists($file_path)) {
				require_once $file_path;
			}
		}
	}
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';
?>