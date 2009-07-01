<?php
/* Standard stuff used all over the place. */

/**
 * Converts a string for a URL, stripping out any bad stuff.
 */
function convert_for_url($string) {
	$string = trim($string);
	$search = array(" ", "#", "'", '"', "/", ".", '&');
	$replace = array("-", "", "", "", "", "", '&amp;');
	return str_replace($search, $replace, $string);
}

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
 * Hashes out a password.
 */
function passwordify($password, $salt = null) {
	$hash = sha1($salt . $password . $salt);
	return $hash;
}

/**
 * Calls print_r() inside of <pre> brackets.
 */
function pprint_r($thing) {
	echo '<pre>' . print_r($thing, true) . '</pre>';
}

/**
 * Returns a random value of a given length.
 */
function random_value($length = 8) {
	$length = abs(intval($length));
	$corpus = array(range(0, 9), range('a', 'z'), range('A', 'Z'));
	$value = '';
	while(strlen($value) < $length) {
		shuffle($corpus);
		shuffle($corpus[0]);
		$value .= $corpus[0][0];
	}
	return $value;
}

/**
 * Attempts to redirect to a URL.
 */
function redirect($url, $params = null) {
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

/**
 * Sanitize a string? Yes.
 */
function sanitize_string($string) {
	if(true == is_string($string)) {
		$string = strip_tags($string);
		$bad_stuff = array("\\", '"', '"', '>', '<');
		$string = str_replace($bad_stuff, null, $string);
		$string = str_replace('&', '&amp;', $string);
		$string = strip_tags($string);
		$string = trim($string);
	}
	return $string;
}

/**
 * By the power of greyskull / regex this function validates email addresses.
 */
function validate_email($email_address) {
	$email_address = trim($email_address);
	$email_address = filter_var($email_address, FILTER_SANITIZE_EMAIL);
	$valid = false;
	if(false !== filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
		$valid = true;
	}
	return $valid;
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

/**
 * Finds the value from the global $_FILES array.
 */
function file_var($var_key, $default_value = null) {
	global $_FILES;
	return exists($var_key, $_FILES, $default_value);
}
?>