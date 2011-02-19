<?php
namespace LooseLeaf;

/**
 * Service/Resource locator singleton. Basic key/val store.
 */
class Locator {
	private static $instance;

	private $key_store = array();

	private $defaults = array();

	private function __construct() {
		/* singleton */
	}

	public static function get() {
		if(!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}

	public static function find($key) {
		$val = null;
		if(array_key_exists($key, self::get()->key_store)) {
			$val = self::get()->key_store[$key];
		} elseif(array_key_exists($key, self::get()->defaults)) {
			$val = self::get()->defaults[$key]();
			self::set($key, $val);
		}

		return $val;
	}

	public static function set($key, $value) {
		self::get()->key_store[$key] = $value;
	}

	public static function reset() {
		self::get()->key_store = array();
	}

	public static function setDefault($key, \Closure $function) {
		self::get()->defaults[$key] = $function;
	}

	public static function dump() {
		return self::get()->key_store;
	}
}