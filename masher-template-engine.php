<?php
/*
Plugin Name: Masher Template Engine
Plugin URI: https://github.com/mackanfkp/masher-template-engine
Description: A simple template engine for wordpress developers. Use it to separate logic from markup in your plugins, widgets, classes and methods.
Version: 1.0
Author: mackanfkp <mackanfkp@gmail.com>
Author URI: https://github.com/mackanfkp/
License: GPLv2
*/

/**
 * Masher Template Engine - Wordpress Template Plugin
 * Copyright (C) 2013 mackanfkp ( mackanfkp@gmail.com )

 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Deny access if no ABSPATH is set, i.e. direct access to plugin from browser
if (! defined('ABSPATH')) {
    header('HTTP/1.0 404 Not Found');
	exit;
}

// Skip this plugin if wp is currently installing
if (defined('WP_INSTALLING') && WP_INSTALLING)
    return;

// Some defines
define('MASHER_TEMPLATE_ENGINE_NAME',    'Masher Template Engine');
define('MASHER_TEMPLATE_ENGINE_VERSION', '1.0');
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
			ob_start();

			//include $file;

			if (false === eval('?>'. file_get_contents($file))) {
				trigger_error("Parse error in template '$file'");
			}

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
