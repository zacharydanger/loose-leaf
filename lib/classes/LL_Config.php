<?php
class LL_Config {
	static $config = array();

	static function configure($ini_file) {
		self::$config = parse_ini_file($ini_file, true);
	}
}
?>
