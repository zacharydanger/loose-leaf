<?php
namespace LooseLeaf;

/**
 * Service locator for finding controllers.
 */
class Controller_Locator {
	private static $_instance;
	private $_controllers = array();

	private function __construct() { /* nada */ }

	public static function get() {
		if(false == isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c();
		}
		return self::$_instance;
	}

	public function findController($controller_name) {
		$controller = null;
		if(true == array_key_exists($controller_name, $this->_controllers)) {
			$controller = $this->_controllers[$controller_name];
		} else {
			$controller = new $controller_name();
		}
		return $controller;
	}

	public function setController($controller_name, $controller_substitute) {
		$this->_controllers[$controller_name] = $controller_substitute;
	}
}
?>
