<?php
require_once __DIR__ . '/standard_lib.php';

class Router {
	static $routes = array();
	static $controller_alias_list = array();
	static $action_alias_list = array();

	static function add($url, $route) {
		$hash = self::_hashUrl($url);
		self::$routes[$hash] = $route;
	}

	static function _hashUrl($url) {
		$hashed_url = null;
		$parts = explode('/', $url);
		foreach($parts as $i => $key) {
			if(strlen($key) > 0) {
				$hashed_url .= sha1($key);
			}
		}
		$url = sha1($hashed_url);
		return $url;
	}

	static function route($url) {
		$hash = self::_hashUrl($url);
		if(true == array_key_exists($hash, self::$routes)) {
			return self::$routes[$hash];
		} else {
			return self::_processUrl($url);
		}
	}

	static function aliasController($alias, $controller) {
		self::$controller_alias_list[$alias] = $controller;
	}

	static function aliasAction($controller, $action_alias, $action) {
		self::$action_alias_list[$controller][$action_alias] = $action;
	}

	static function _processUrl($url) {
		$url_parts = explode('/', $url);
		$controller_raw = exists(0, $url_parts);
		$action_raw = exists(1, $url_parts);
		$id = exists(2, $url_parts);

		$route['id'] = $id;
		$route['controller'] = self::_findController($controller_raw);
		$route['action'] = self::_findAction($route['controller'], $action_raw);
		return $route;
	}

	static function _findAction($controller, $raw_action) {
		return exists($raw_action, self::$action_alias_list[$controller], $raw_action);
	}

	static function _findController($raw_controller) {
		if(false == array_key_exists($raw_controller, self::$controller_alias_list)) {
			$controller = ucfirst(strtolower($raw_controller)) . '_Controller';
			
			if(true == class_exists($controller)) {
				return $controller;
			} else {
				return false;
			}
		} else {
			return self::$controller_alias_list[$raw_controller];
		}
	}
}
?>
