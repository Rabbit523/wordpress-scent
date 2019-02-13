<?php
/**
 * File system manipulations
 *
 * @package WordPress
 * @subpackage ThemeREX Utilities
 * @since v3.0
 */

// Don't load directly
if ( ! defined( 'TRX_UTILS_VERSION' ) ) {
	die( '-1' );
}


/* Enqueue scripts and styles from child or main theme directory and use .min version
------------------------------------------------------------------------------------- */

// Enqueue .min.css (if exists and filetime .min.css > filetime .css) instead .css
if (!function_exists('trx_utils_enqueue_style')) {	
	function trx_utils_enqueue_style($handle, $src=false, $depts=array(), $ver=null, $media='all') {
		global $TRX_UTILS_STORAGE;
		$load = true;
		if (!is_array($src) && $src !== false && $src !== '') {
			$theme_dir = get_template_directory().'/'.$TRX_UTILS_STORAGE['plugin_base'][0].'/';
			$theme_url = get_template_directory_uri().'/'.$TRX_UTILS_STORAGE['plugin_base'][0].'/';
			$child_dir = get_stylesheet_directory().'/'.$TRX_UTILS_STORAGE['plugin_base'][0].'/';
			$child_url = get_stylesheet_directory_uri().'/'.$TRX_UTILS_STORAGE['plugin_base'][0].'/';
			$dir = $url = '';
			if (strpos($src, $child_url)===0) {
				$dir = $child_dir;
				$url = $child_url;
			} else if (strpos($src, $theme_url)===0) {
				$dir = $theme_dir;
				$url = $theme_url;
			} else if (strpos($src, $TRX_UTILS_STORAGE['plugin_url'])===0) {
				$dir = $TRX_UTILS_STORAGE['plugin_dir'];
				$url = $TRX_UTILS_STORAGE['plugin_url'];
			}
			if ($dir != '') {
				if (substr($src, -4)=='.css') {
					if (substr($src, -8)!='.min.css') {
						$src_min = substr($src, 0, strlen($src)-4).'.min.css';
						$file_src = $dir . substr($src, strlen($url));
						$file_min = $dir . substr($src_min, strlen($url));
						if (file_exists($file_min) && filemtime($file_src) <= filemtime($file_min)) $src = $src_min;
					}
				}
				$file_src = $dir . substr($src, strlen($url));
				$load = file_exists($file_src) && filesize($file_src) > 0;
			}
		}
		if ($load) {
			if (is_array($src))
				wp_enqueue_style( $handle, $depts, $ver, $media );
			else if (!empty($src) || $src===false)
				wp_enqueue_style( $handle, $src, $depts, $ver, $media );
		}
	}
}

// Enqueue .min.js (if exists and filetime .min.js > filetime .js) instead .js
if (!function_exists('trx_utils_enqueue_script')) {	
	function trx_utils_enqueue_script($handle, $src=false, $depts=array(), $ver=null, $in_footer=true) {
		global $TRX_UTILS_STORAGE;
		$load = true;
		if (!is_array($src) && $src !== false && $src !== '') {
			$theme_dir = get_template_directory().'/'.$TRX_UTILS_STORAGE['plugin_base'][0].'/';
			$theme_url = get_template_directory_uri().'/'.$TRX_UTILS_STORAGE['plugin_base'][0].'/';
			$child_dir = get_stylesheet_directory().'/'.$TRX_UTILS_STORAGE['plugin_base'][0].'/';
			$child_url = get_stylesheet_directory_uri().'/'.$TRX_UTILS_STORAGE['plugin_base'][0].'/';
			$dir = $url = '';
			if (strpos($src, $child_url)===0) {
				$dir = $child_dir;
				$url = $child_url;
			} else if (strpos($src, $theme_url)===0) {
				$dir = $theme_dir;
				$url = $theme_url;
			} else if (strpos($src, $TRX_UTILS_STORAGE['plugin_url'])===0) {
				$dir = $TRX_UTILS_STORAGE['plugin_dir'];
				$url = $TRX_UTILS_STORAGE['plugin_url'];
			}
			if ($dir != '') {
				if (substr($src, -3)=='.js') {
					if (substr($src, -7)!='.min.js') {
						$src_min  = substr($src, 0, strlen($src)-3).'.min.js';
						$file_src = $dir . substr($src, strlen($url));
						$file_min = $dir . substr($src_min, strlen($url));
						if (file_exists($file_min) && filemtime($file_src) <= filemtime($file_min)) $src = $src_min;
					}
				}
				$file_src = $dir . substr($src, strlen($url));
				$load = file_exists($file_src) && filesize($file_src) > 0;
			}
		}
		if ($load) {
			if (is_array($src)) {
				wp_enqueue_script( $handle, $depts, $ver, $in_footer );
			} else if (!empty($src) || $src===false) {
				wp_enqueue_script( $handle, $src, $depts, $ver, $in_footer );
			}
		}
	}
}


/* Check if file/folder present in the child theme and return path (url) to it. 
   Else - path (url) to file in the main theme dir
------------------------------------------------------------------------------------- */
if (!function_exists('trx_utils_get_file_dir')) {	
	function trx_utils_get_file_dir($file, $return_url=false) {
		global $TRX_UTILS_STORAGE;
		if ($file[0]=='/') $file = substr($file, 1);
		$theme_dir = get_template_directory().'/'.$TRX_UTILS_STORAGE['plugin_base'][0].'/';
		$theme_url = get_template_directory_uri().'/'.$TRX_UTILS_STORAGE['plugin_base'][0].'/';
		$child_dir = get_stylesheet_directory().'/'.$TRX_UTILS_STORAGE['plugin_base'][0].'/';
		$child_url = get_stylesheet_directory_uri().'/'.$TRX_UTILS_STORAGE['plugin_base'][0].'/';
		$dir = '';
		if (file_exists(($child_dir).($file)))
			$dir = ($return_url ? $child_url : $child_dir).($file);
		else if (file_exists(($theme_dir).($file)))
			$dir = ($return_url ? $theme_url : $theme_dir).($file);
		else if (file_exists(($TRX_UTILS_STORAGE['plugin_dir']).($file)))
			$dir = ($return_url ? $TRX_UTILS_STORAGE['plugin_url'] : $TRX_UTILS_STORAGE['plugin_dir']).($file);
		return $dir;
	}
}

if (!function_exists('trx_utils_get_file_url')) {	
	function trx_utils_get_file_url($file) {
		return trx_utils_get_file_dir($file, true);
	}
}

// Return file extension from full name/path
if (!function_exists('trx_utils_get_file_ext')) {	
	function trx_utils_get_file_ext($file) {
		$parts = pathinfo($file);
		return $parts['extension'];
	}
}


// Get domain part from URL
if (!function_exists('trx_utils_get_domain_from_url')) {
	function trx_utils_get_domain_from_url($url) {
		if (($pos=strpos($url, '://'))!==false) $url = substr($url, $pos+3);
		if (($pos=strpos($url, '/'))!==false) $url = substr($url, 0, $pos);
		return $url;
	}
}

// Detect folder location (in the child theme or in the main theme)
if (!function_exists('trx_utils_get_folder_dir')) {	
	function trx_utils_get_folder_dir($folder, $return_url=false) {
		if ($folder[0]=='/') $folder = substr($folder, 1);
		$theme_dir = get_template_directory();
		$theme_url = get_template_directory_uri();
		$child_dir = get_stylesheet_directory();
		$child_url = get_stylesheet_directory_uri();
		$dir = '';
		if (is_dir(($child_dir).'/'.($folder)))
			$dir = ($return_url ? $child_url : $child_dir).'/'.($folder);
		else if (is_dir(($theme_dir).'/'.($folder)))
			$dir = ($return_url ? $theme_url : $theme_dir).'/'.($folder);
		return $dir;
	}
}

if (!function_exists('trx_utils_get_folder_url')) {	
	function trx_utils_get_folder_url($folder) {
		return trx_utils_get_folder_dir($folder, true);
	}
}

// Return list files in the folder
if (!function_exists('trx_utils_get_folder_list')) {	
	function trx_utils_get_folder_list($folder, $ext='', $only_names=false) {
		$dir = trx_utils_get_folder_dir($folder);
		$url = trx_utils_get_folder_url($folder);
		$list = array();
		if ( is_dir($dir) ) {
			$files = @glob(sprintf("%s/%s", $dir, $ext ? "*.{$ext}" : '*.*'));
			if ( is_array($files) ) {
				foreach ($files as $file) {
					if ( substr($file, 0, 1) == '.' || is_dir( $file ) )
						continue;
					$file = basename($file);
					$key = substr($file, 0, strrpos($file, '.'));
					if (substr($key, -4)=='.min') $key = substr($file, 0, strrpos($key, '.'));
					$list[$key] = $only_names ? ucfirst(str_replace('_', ' ', $key)) : ($url) . '/' . ($file);
				}
			}
		}
		return $list;
	}
}



/* CSS & JS minify
-------------------------------------------------------------------------------- */

// Minify CSS string
if (!function_exists('trx_utils_minify_css')) {
	function trx_utils_minify_css($css) {
		$css = preg_replace("/\r*\n*/", "", $css);
		$css = preg_replace("/\s{2,}/", " ", $css);
        //$css = str_ireplace('@CHARSET "UTF-8";', "", $css);
		$css = preg_replace("/\s*>\s*/", ">", $css);
		$css = preg_replace("/\s*:\s*/", ":", $css);
		$css = preg_replace("/\s*{\s*/", "{", $css);
		$css = preg_replace("/\s*;*\s*}\s*/", "}", $css);
        $css = str_replace(', ', ',', $css);
        $css = preg_replace("/(\/\*[\w\'\s\r\n\*\+\,\"\-\.]*\*\/)/", "", $css);
        return $css;
	}
}

// Minify JS string
if (!function_exists('trx_utils_minify_js')) {
	function trx_utils_minify_js($js) {
		// Remove multi-row comments
		//$js = preg_replace('/(\/\*)[^(\*\/)]*(\*\/)/', '', $js);
		$pos = 0;
		while (($pos = strpos($js, '/*', $pos))!==false) {
			if (($pos2 = strpos($js, '*/', $pos))!==false)
				$js = substr($js, 0, $pos) . substr($js, $pos2+2);
			else
				break;
		}
		// Remove single-line comments
		//$js = preg_replace('/\s*\/\/[^\n]*\n/', '', $js);
		$pos = -1;
		while (($pos = strpos($js, '//', $pos+1))!==false) {
			if ($js[$pos-1]!='\\' && $js[$pos-1]!=':') {
				$pos2 = strpos($js, "\n", $pos);
				if ($pos2==false) $pos2 = strlen($js);
				$js = substr($js, 0, $pos) . substr($js, $pos2);
			}
		}
		// Remove spaces before/after {}()
		$js = preg_replace('/\s+/', ' ', $js);
		$js = preg_replace('/([;}{\)\(])\s+/', '$1 ', $js);
		$js = preg_replace('/\s+([;}{\)\(])/', ' $1', $js);
		$js = preg_replace('/(else)\s+/', '$1 ', $js);
//		$js = preg_replace('/([}])\s+(else)/', '$1else', $js);
//		$js = preg_replace('/([}])\s+(var)/', '$1;var', $js);
//		$js = preg_replace('/([{};])\s+(\$)/', '$1\$', $js);
		return $js;
	}
}


/* Init WP Filesystem before the plugins and theme init
------------------------------------------------------------------- */
if (!function_exists('trx_utils_init_filesystem')) {
	add_action( 'after_setup_theme', 'trx_utils_init_filesystem', 0);
	function trx_utils_init_filesystem() {
        if( !function_exists('WP_Filesystem') ) {
            require_once( ABSPATH .'/wp-admin/includes/file.php' );
        }
		if (is_admin()) {
			$url = admin_url();
			$creds = false;
			// First attempt to get credentials.
			if ( function_exists('request_filesystem_credentials') && false === ( $creds = request_filesystem_credentials( $url, '', false, false, array() ) ) ) {
				// If we comes here - we don't have credentials
				// so the request for them is displaying no need for further processing
				return false;
			}
	
			// Now we got some credentials - try to use them.
			if ( !WP_Filesystem( $creds ) ) {
				// Incorrect connection data - ask for credentials again, now with error message.
				if ( function_exists('request_filesystem_credentials') ) request_filesystem_credentials( $url, '', true, false );
				return false;
			}
			
			return true; // Filesystem object successfully initiated.
		} else {
            WP_Filesystem();
		}
		return true;
	}
}


// Put data into specified file
if (!function_exists('trx_utils_fpc')) {	
	function trx_utils_fpc($file, $data, $flag=0) {
		global $wp_filesystem;
		if (!empty($file)) {
			if (isset($wp_filesystem) && is_object($wp_filesystem)) {
				$file = str_replace(ABSPATH, $wp_filesystem->abspath(), $file);
				// Attention! WP_Filesystem can't append the content to the file!
				// That's why we have to read the contents of the file into a string,
				// add new content to this string and re-write it to the file if parameter $flag == FILE_APPEND!
				return $wp_filesystem->put_contents($file, ($flag==FILE_APPEND ? $wp_filesystem->get_contents($file) : '') . $data, false);
			} else {
				if (WP_DEBUG)
					throw new Exception(sprintf(esc_html__('WP Filesystem is not initialized! Put contents to the file "%s" failed', 'trx_utils'), $file));
			}
		}
		return false;
	}
}

// Get text from specified file
if (!function_exists('trx_utils_fgc')) {	
	function trx_utils_fgc($file, $unpack=false) {
		global $wp_filesystem;
		if (!empty($file)) {
			if (isset($wp_filesystem) && is_object($wp_filesystem)) {
				$file = str_replace(ABSPATH, $wp_filesystem->abspath(), $file);
				if ($unpack && trx_utils_get_file_ext($file) == 'zip') {
					$tmp_cont = $wp_filesystem->get_contents($file);
					$tmp_name = 'tmp-'.rand().'.zip';
					$tmp = wp_upload_bits($tmp_name, null, $tmp_cont);
					if ($tmp['error'])
						$tmp_cont = '';
					else {
						unzip_file($tmp['file'], dirname($tmp['file']));
						$file_name = dirname($tmp['file']) . '/' . basename($file, '.zip') . '.txt';
						$tmp_cont = trx_utils_fgc($file_name);
						unlink($tmp['file']);
						unlink($file_name);
					}
					return $tmp_cont;
				} else {
					return $wp_filesystem->get_contents($file);
				}
			} else {
				if (WP_DEBUG)
					throw new Exception(sprintf(esc_html__('WP Filesystem is not initialized! Get contents from the file "%s" failed', 'trx_utils'), $file));
			}
		}
		return '';
	}
}

// Get array with rows from specified file
if (!function_exists('trx_utils_fga')) {	
	function trx_utils_fga($file) {
		global $wp_filesystem;
		if (!empty($file)) {
			if (isset($wp_filesystem) && is_object($wp_filesystem)) {
				$file = str_replace(ABSPATH, $wp_filesystem->abspath(), $file);
				return $wp_filesystem->get_contents_array($file);
			} else {
				if (WP_DEBUG)
					throw new Exception(sprintf(esc_html__('WP Filesystem is not initialized! Get rows from the file "%s" failed', 'trx_utils'), $file));
			}
		}
		return array();
	}
}

// Remove unsafe characters from file/folder path
if (!function_exists('trx_utils_esc')) {	
	function trx_utils_esc($file) {
		return str_replace(array('\\', '~', '$', ':', ';', '+', '>', '<', '|', '"', "'", '`', "\xFF", "\x0A", "\x0D", '*', '?', '^'), '/', trim($file));
	}
}
?>