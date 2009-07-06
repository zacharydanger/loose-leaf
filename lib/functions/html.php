<?php
/* These are a bunch of helper functions for drawing repetitive HTML stuff. */

/**
 * Draws a string of parameters for a URL.
 */
function draw_get_param_string($params = array()) {
	$param_array = array();
	foreach($params as $key => $value) {
		$value = trim($value);
		if(false == empty($value)) {
			$param_array[] = $key.'='.$value;
		}
	}
	return '?' . implode('&amp;', $param_array);
}

/**
 * Draws a select input field with given options / params / default value.
 */
function draw_select($name = "select", $options = array(), $default = null, $params = null) {
	$select_string = '<select name="' . $name . '" ' . $params . ' >';
	foreach($options as $value => $option) {
		$select_string .= '<option value="' . $value . '" ';
		if($value == $default) {
			$select_string .= 'selected="selected"';
		}
		$select_string .= '>' . $option . '</option>';
	}
	$select_string .= '</select>';
	return $select_string;
}

function draw_hidden($name, $value, $params = null) {
	$input_string = '<input type="hidden" name="' . sanitize_string($name) . '" value="' . sanitize_string($value) . '" ' . $params . ' />';
	return $input_string;
}

function draw_checkbox($name, $value, $checked = false, $params = null) {
	$input_string = '<input type="checkbox" name="' . sanitize_string($name) . '" value="' . sanitize_string($value) . '" ' . $params;

	if(true == $checked) {
		$input_string .= ' checked="checked"';
	}
	$input_string .= ' />';
	return $input_string;
}

function draw_radio($name, $value, $checked = false, $params = null) {
	$input_string = '<input type="radio" name="' . sanitize_string($name) . '" value="' . sanitize_string($value) . '" ' . $params;

	if(true == $checked) {
		$input_string .= ' checked="checked"';
	}
	$input_string .= ' />';
	return $input_string;
}
?>