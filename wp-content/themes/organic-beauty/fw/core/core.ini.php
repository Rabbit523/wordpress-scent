<?php
/**
 * Organic Beauty Framework: ini-files manipulations
 *
 * @package	organic_beauty
 * @since	organic_beauty 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


//  Get value by name from .ini-file
if (!function_exists('organic_beauty_ini_get_value')) {
	function organic_beauty_ini_get_value($file, $name, $defa='') {
		if (!is_array($file)) {
			if (file_exists($file)) {
				$file = organic_beauty_fga($file);
			} else
				return $defa;
		}
		$name = organic_beauty_strtolower($name);
		$rez = $defa;
		for ($i=0; $i<count($file); $i++) {
			$file[$i] = trim($file[$i]);
			if (($pos = organic_beauty_strpos($file[$i], ';'))!==false)
				$file[$i] = trim(organic_beauty_substr($file[$i], 0, $pos));
			$parts = explode('=', $file[$i]);
			if (count($parts)!=2) continue;
			if (organic_beauty_strtolower(trim(chop($parts[0])))==$name) {
				$rez = trim(chop($parts[1]));
				if (organic_beauty_substr($rez, 0, 1)=='"')
					$rez = organic_beauty_substr($rez, 1, organic_beauty_strlen($rez)-2);
				else
					$rez *= 1;
				break;
			}
		}
		return $rez;
	}
}

//  Retrieve all values from .ini-file as assoc array
if (!function_exists('organic_beauty_ini_get_values')) {
	function organic_beauty_ini_get_values($file) {
		$rez = array();
		if (!is_array($file)) {
			if (file_exists($file)) {
				$file = organic_beauty_fga($file);
			} else
				return $rez;
		}
		for ($i=0; $i<count($file); $i++) {
			$file[$i] = trim(chop($file[$i]));
			if (($pos = organic_beauty_strpos($file[$i], ';'))!==false)
				$file[$i] = trim(organic_beauty_substr($file[$i], 0, $pos));
			$parts = explode('=', $file[$i]);
			if (count($parts)!=2) continue;
			$key = trim(chop($parts[0]));
			$rez[$key] = trim($parts[1]);
			if (organic_beauty_substr($rez[$key], 0, 1)=='"')
				$rez[$key] = organic_beauty_substr($rez[$key], 1, organic_beauty_strlen($rez[$key])-2);
			else
				$rez[$key] *= 1;
		}
		return $rez;
	}
}
?>