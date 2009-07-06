<?php
class Router {
	static $routes = array();

	static function add($url, $route) {
		self::$routes[$url] = $route;
	}

	static function route($url) {
		if(true == array_key_exists($url, self::$routes)) {
			return self::$routes[$url];
		} else {
			return self::_processUrl($url);
		}
	}

	static function _processUrl($url) {
		$url_parts = explode('/', $url);
		$controller_raw = exists(0, $url_parts);
		$action_raw = exists(1, $url_parts);
		$id = exists(2, $url_parts);

		$route['id'] = $id;
		$route['action'] = $action_raw;
		$route['controller'] = self::_findController($controller_raw);
		return $route;
	}

	static function _findController($raw_controller) {
		$controller = ucfirst(strtolower($raw_controller)) . '_Controller';
		
		if(true == class_exists($controller)) {
			return $controller;
		} else {
			return false;
		}
	}
}
?>
