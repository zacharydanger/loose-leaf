<?php
/* Standard stuff used all over the place. */

/**
 * Checks whether or not a key exists in an array and returns the value.
 */
function exists($key, $array = array(), $default = null) {
	$return_value = $default;
	if(true == is_array($array) && true == array_key_exists($key, $array)) {
		$return_value = $array[$key];
	}
	return $return_value;
}

/**
 * Finds the value from the global $_GET array.
 */
function get_var($var_key, $default_value = null) {
	global $_GET;
	return exists($var_key, $_GET, $default_value);
}

/**
 * Finds the value from the global $_POST array.
 */
function post_var($var_key, $default_value = null) {
	global $_POST;
	return exists($var_key, $_POST, $default_value);
}

/**
 * Finds the value from the global $_REQUEST array.
 */
function request_var($var_key, $default_value = null) {
	global $_REQUEST;
	return exists($var_key, $_REQUEST, $default_value);
}

/**
 * Finds the value from the global $_SESSION array.
 */
function session_var($var_key, $default_value = null) {
	global $_SESSION;
	return exists($var_key, $_SESSION, $default_value);
}