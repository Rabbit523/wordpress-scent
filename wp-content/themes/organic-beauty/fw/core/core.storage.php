<?php
/**
 * Organic Beauty Framework: theme variables storage
 *
 * @package	organic_beauty
 * @since	organic_beauty 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Get theme variable
if (!function_exists('organic_beauty_storage_get')) {
	function organic_beauty_storage_get($var_name, $default='') {
		global $ORGANIC_BEAUTY_STORAGE;
		return isset($ORGANIC_BEAUTY_STORAGE[$var_name]) ? $ORGANIC_BEAUTY_STORAGE[$var_name] : $default;
	}
}

// Set theme variable
if (!function_exists('organic_beauty_storage_set')) {
	function organic_beauty_storage_set($var_name, $value) {
		global $ORGANIC_BEAUTY_STORAGE;
		$ORGANIC_BEAUTY_STORAGE[$var_name] = $value;
	}
}

// Check if theme variable is empty
if (!function_exists('organic_beauty_storage_empty')) {
	function organic_beauty_storage_empty($var_name, $key='', $key2='') {
		global $ORGANIC_BEAUTY_STORAGE;
		if (!empty($key) && !empty($key2))
			return empty($ORGANIC_BEAUTY_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return empty($ORGANIC_BEAUTY_STORAGE[$var_name][$key]);
		else
			return empty($ORGANIC_BEAUTY_STORAGE[$var_name]);
	}
}

// Check if theme variable is set
if (!function_exists('organic_beauty_storage_isset')) {
	function organic_beauty_storage_isset($var_name, $key='', $key2='') {
		global $ORGANIC_BEAUTY_STORAGE;
		if (!empty($key) && !empty($key2))
			return isset($ORGANIC_BEAUTY_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return isset($ORGANIC_BEAUTY_STORAGE[$var_name][$key]);
		else
			return isset($ORGANIC_BEAUTY_STORAGE[$var_name]);
	}
}

// Inc/Dec theme variable with specified value
if (!function_exists('organic_beauty_storage_inc')) {
	function organic_beauty_storage_inc($var_name, $value=1) {
		global $ORGANIC_BEAUTY_STORAGE;
		if (empty($ORGANIC_BEAUTY_STORAGE[$var_name])) $ORGANIC_BEAUTY_STORAGE[$var_name] = 0;
		$ORGANIC_BEAUTY_STORAGE[$var_name] += $value;
	}
}

// Concatenate theme variable with specified value
if (!function_exists('organic_beauty_storage_concat')) {
	function organic_beauty_storage_concat($var_name, $value) {
		global $ORGANIC_BEAUTY_STORAGE;
		if (empty($ORGANIC_BEAUTY_STORAGE[$var_name])) $ORGANIC_BEAUTY_STORAGE[$var_name] = '';
		$ORGANIC_BEAUTY_STORAGE[$var_name] .= $value;
	}
}

// Get array (one or two dim) element
if (!function_exists('organic_beauty_storage_get_array')) {
	function organic_beauty_storage_get_array($var_name, $key, $key2='', $default='') {
		global $ORGANIC_BEAUTY_STORAGE;
		if (empty($key2))
			return !empty($var_name) && !empty($key) && isset($ORGANIC_BEAUTY_STORAGE[$var_name][$key]) ? $ORGANIC_BEAUTY_STORAGE[$var_name][$key] : $default;
		else
			return !empty($var_name) && !empty($key) && isset($ORGANIC_BEAUTY_STORAGE[$var_name][$key][$key2]) ? $ORGANIC_BEAUTY_STORAGE[$var_name][$key][$key2] : $default;
	}
}

// Set array element
if (!function_exists('organic_beauty_storage_set_array')) {
	function organic_beauty_storage_set_array($var_name, $key, $value) {
		global $ORGANIC_BEAUTY_STORAGE;
		if (!isset($ORGANIC_BEAUTY_STORAGE[$var_name])) $ORGANIC_BEAUTY_STORAGE[$var_name] = array();
		if ($key==='')
			$ORGANIC_BEAUTY_STORAGE[$var_name][] = $value;
		else
			$ORGANIC_BEAUTY_STORAGE[$var_name][$key] = $value;
	}
}

// Set two-dim array element
if (!function_exists('organic_beauty_storage_set_array2')) {
	function organic_beauty_storage_set_array2($var_name, $key, $key2, $value) {
		global $ORGANIC_BEAUTY_STORAGE;
		if (!isset($ORGANIC_BEAUTY_STORAGE[$var_name])) $ORGANIC_BEAUTY_STORAGE[$var_name] = array();
		if (!isset($ORGANIC_BEAUTY_STORAGE[$var_name][$key])) $ORGANIC_BEAUTY_STORAGE[$var_name][$key] = array();
		if ($key2==='')
			$ORGANIC_BEAUTY_STORAGE[$var_name][$key][] = $value;
		else
			$ORGANIC_BEAUTY_STORAGE[$var_name][$key][$key2] = $value;
	}
}

// Add array element after the key
if (!function_exists('organic_beauty_storage_set_array_after')) {
	function organic_beauty_storage_set_array_after($var_name, $after, $key, $value='') {
		global $ORGANIC_BEAUTY_STORAGE;
		if (!isset($ORGANIC_BEAUTY_STORAGE[$var_name])) $ORGANIC_BEAUTY_STORAGE[$var_name] = array();
		if (is_array($key))
			organic_beauty_array_insert_after($ORGANIC_BEAUTY_STORAGE[$var_name], $after, $key);
		else
			organic_beauty_array_insert_after($ORGANIC_BEAUTY_STORAGE[$var_name], $after, array($key=>$value));
	}
}

// Add array element before the key
if (!function_exists('organic_beauty_storage_set_array_before')) {
	function organic_beauty_storage_set_array_before($var_name, $before, $key, $value='') {
		global $ORGANIC_BEAUTY_STORAGE;
		if (!isset($ORGANIC_BEAUTY_STORAGE[$var_name])) $ORGANIC_BEAUTY_STORAGE[$var_name] = array();
		if (is_array($key))
			organic_beauty_array_insert_before($ORGANIC_BEAUTY_STORAGE[$var_name], $before, $key);
		else
			organic_beauty_array_insert_before($ORGANIC_BEAUTY_STORAGE[$var_name], $before, array($key=>$value));
	}
}

// Push element into array
if (!function_exists('organic_beauty_storage_push_array')) {
	function organic_beauty_storage_push_array($var_name, $key, $value) {
		global $ORGANIC_BEAUTY_STORAGE;
		if (!isset($ORGANIC_BEAUTY_STORAGE[$var_name])) $ORGANIC_BEAUTY_STORAGE[$var_name] = array();
		if ($key==='')
			array_push($ORGANIC_BEAUTY_STORAGE[$var_name], $value);
		else {
			if (!isset($ORGANIC_BEAUTY_STORAGE[$var_name][$key])) $ORGANIC_BEAUTY_STORAGE[$var_name][$key] = array();
			array_push($ORGANIC_BEAUTY_STORAGE[$var_name][$key], $value);
		}
	}
}

// Pop element from array
if (!function_exists('organic_beauty_storage_pop_array')) {
	function organic_beauty_storage_pop_array($var_name, $key='', $defa='') {
		global $ORGANIC_BEAUTY_STORAGE;
		$rez = $defa;
		if ($key==='') {
			if (isset($ORGANIC_BEAUTY_STORAGE[$var_name]) && is_array($ORGANIC_BEAUTY_STORAGE[$var_name]) && count($ORGANIC_BEAUTY_STORAGE[$var_name]) > 0) 
				$rez = array_pop($ORGANIC_BEAUTY_STORAGE[$var_name]);
		} else {
			if (isset($ORGANIC_BEAUTY_STORAGE[$var_name][$key]) && is_array($ORGANIC_BEAUTY_STORAGE[$var_name][$key]) && count($ORGANIC_BEAUTY_STORAGE[$var_name][$key]) > 0) 
				$rez = array_pop($ORGANIC_BEAUTY_STORAGE[$var_name][$key]);
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if (!function_exists('organic_beauty_storage_inc_array')) {
	function organic_beauty_storage_inc_array($var_name, $key, $value=1) {
		global $ORGANIC_BEAUTY_STORAGE;
		if (!isset($ORGANIC_BEAUTY_STORAGE[$var_name])) $ORGANIC_BEAUTY_STORAGE[$var_name] = array();
		if (empty($ORGANIC_BEAUTY_STORAGE[$var_name][$key])) $ORGANIC_BEAUTY_STORAGE[$var_name][$key] = 0;
		$ORGANIC_BEAUTY_STORAGE[$var_name][$key] += $value;
	}
}

// Concatenate array element with specified value
if (!function_exists('organic_beauty_storage_concat_array')) {
	function organic_beauty_storage_concat_array($var_name, $key, $value) {
		global $ORGANIC_BEAUTY_STORAGE;
		if (!isset($ORGANIC_BEAUTY_STORAGE[$var_name])) $ORGANIC_BEAUTY_STORAGE[$var_name] = array();
		if (empty($ORGANIC_BEAUTY_STORAGE[$var_name][$key])) $ORGANIC_BEAUTY_STORAGE[$var_name][$key] = '';
		$ORGANIC_BEAUTY_STORAGE[$var_name][$key] .= $value;
	}
}

// Call object's method
if (!function_exists('organic_beauty_storage_call_obj_method')) {
	function organic_beauty_storage_call_obj_method($var_name, $method, $param=null) {
		global $ORGANIC_BEAUTY_STORAGE;
		if ($param===null)
			return !empty($var_name) && !empty($method) && isset($ORGANIC_BEAUTY_STORAGE[$var_name]) ? $ORGANIC_BEAUTY_STORAGE[$var_name]->$method(): '';
		else
			return !empty($var_name) && !empty($method) && isset($ORGANIC_BEAUTY_STORAGE[$var_name]) ? $ORGANIC_BEAUTY_STORAGE[$var_name]->$method($param): '';
	}
}

// Get object's property
if (!function_exists('organic_beauty_storage_get_obj_property')) {
	function organic_beauty_storage_get_obj_property($var_name, $prop, $default='') {
		global $ORGANIC_BEAUTY_STORAGE;
		return !empty($var_name) && !empty($prop) && isset($ORGANIC_BEAUTY_STORAGE[$var_name]->$prop) ? $ORGANIC_BEAUTY_STORAGE[$var_name]->$prop : $default;
	}
}
?>