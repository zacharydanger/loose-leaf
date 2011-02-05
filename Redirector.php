<?php
namespace LooseLeaf;

class Redirector {
	private static $_instance;
	private static $_override;

	private function __construct() {
		/* do nothing */
	}

	public static function get() {
		if(false == isset(self::$_instance)) {
			$class = __CLASS__;
			self::$_instance = new $class();
		}
		$instance = self::$_instance;
		if(true == isset(self::$_override)) {
			$instance = self::$_override;
		}

		return $instance;
	}

	public static function useRedirector($object) {
		self::$_override = $object;
	}

	public static function unsetOverride() {
		self::$_override = null;
	}

	public function redirect($url, $params = null) {
		//try to send it via the header() function...
		if(false == headers_sent()) {
			if(true == is_array($params)) {
				$param_strings = array();
				foreach($params as $key => $value) {
					$param_strings[] = $key . '=' . $value;
				}
				$param_string = implode('&', $param_strings);
				if(false == empty($param_string)) {
					$url .= '?' . $param_string;
				}
			}
			header('Location: ' . $url);
			exit;
		} else {
			echo "could not redirect, headers already sent.";
		}
	}
}

/**
 * Attempts to redirect to a URL.
 */
function redirect($url, $params = null) {
	Redirector::get()->redirect($url, $params);
}
