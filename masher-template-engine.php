<?php
/**
 * @package Masher Template Engine
 */

/**
 * Plugin Name: Masher Template Engine
 * Plugin URI: https://github.com/mackanfkp/wordpress/plugins/masher-template-engine/
 * Description: A simple template class to separate logic from markup in wordpress plugins and methods.
 * Version: 0.0.1
 * Author: mackanfkp
 * Author URI: https://github.com/mackanfkp/wordpress/
 * License:
 */

// Deny direct access to plugin!
if (! function_exists('add_action')) {
	header('HTTP/1.0 404 Not Found');
	exit;
}

// Some defines
define('MASHER_TEMPLATE_ENGINE_NAME',    'Masher Template Engine');
define('MASHER_TEMPLATE_ENGINE_VERSION', '0.0.1');
define('MASHER_TEMPLATE_ENGINE_AUTHOR',  'mackanfkp');

/**
 * Basic usage:

	// Create a new instance of the Masher_Template_Engine

	// …using templates located here in Masher_Template_Engine plugin directory.
	$o = Masher_Template_Engine::instance();

	// …using templates located in your custom directory.
	$o = Masher_Template_Engine::instance('/path/to/your/templates/');

	// …using templates located here in YOUR plugin directory.
	$o = Masher_Template_Engine::instance(plugin_dir_path( __FILE__ ) . '/templates/');

	// Set some variables
	$o->set('key1', 'val1');
	$o->set('key2', 'val2');

	// Output the example.phtml template
	$o->show('example');

	// …or save value in a variable
	$html = $o->load('example');

 */

class Masher_Template_Engine {
	protected $path;
	protected $data = array();

	public function __construct ($path = null) {
		$this->path = $path ? $path : '';
	}

	public function __get ($key) {
		return $this->get($key);
	}

	public function __set ($key, $val) {
		return $this->set($key);
	}

	public function show ($file, $flush_after = false) {
		echo $this->load($file, $flush_after);
	}

	public function load ($file, $flush_after = false) {
		$retval = '';

		if (! ($file = $this->loadTemplateFile($file))) {
			$retval = '<!-- Template does not exist -->';
		} else {
			if ($this->data) {
				extract($this->data);
			}
			ob_start();

			include $file;

			$retval = ob_get_clean();
		}

		if ($flush_after) {
			$this->flush();
		}

		return $retval;
	}

	public function flush () {
		$this->data = array();
		return $this;
	}

	public function set ($key, $val, $overwrite = true) {
		if ($overwrite || ! array_key_exists($key, $this->data)) {
			$this->data[$key] = $val;
		}
		return $this;
	}

	public function get ($key, $default = '') {
		return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
	}

	public function setArray (array $data, $merge = true) {
		$this->data = $merge ? array_merge($this->data, $data) : $data;
		return $this;
	}

	public function getArray () {
		return $this->data;
	}

	public function setPath ($path) {
		$this->path = rtrim($path, ' /') . '/';
		$this->path = str_replace('../', '', $this->path);
		return $this;
	}

	public function getPath () {
		return $this->path;
	}

	protected function loadTemplateFile ($file) {
		$file = $this->path . $file . '.phtml';

		if (! file_exists($file) || ! is_readable($file)) {
			if (WP_DEBUG) {
				throw new Exception(__CLASS__ .': Template named "'. $file .'" does not exist.');
			}

			return false;
		}

		return $file;
	}

	/**
	 * Global instance of this template engine
	 *
	 * If no path is entered the default location is template-dir in this plugin directory
	 */
	static public function instance ($path = null) {
		static $instances = array();

		if (! $path) {
			$path = plugin_dir_path( __FILE__ ) . '/templates/';
		}

		$path = rtrim($path, ' /') . '/';
		$path = str_replace('../', '', $path);

		if (! isset($instances[$path])) {
			$instances[$path] = new self($path);
		}

		return $instances[$path]->flush();
	}
}

/**
 * Debug/Test using this…
 */

/*
if (is_admin()) {
	// New instance of the global Masher_Template_Engine
	$o = Masher_Template_Engine::instance();

	// Set some variables (used in the example-template)
	$o->set('name', MASHER_TEMPLATE_ENGINE_NAME);
	$o->set('version', MASHER_TEMPLATE_ENGINE_VERSION);
	$o->set('author', MASHER_TEMPLATE_ENGINE_AUTHOR);

	// Output the example.phtml template
	$o->show('example');
}
*/
