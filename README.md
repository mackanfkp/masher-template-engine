Masher Template Engine
================================

* Plugin Name: Masher Template Engine
* Plugin URI: https://github.com/mackanfkp/masher-template-engine
* Description: A simple template engine for wordpress developers. Use it to separate logic from markup in your plugins, widgets, classes and methods.
* Version: 1.0
* Author: mackanfkp <mackanfkp@gmail.com>
* Author URI: https://github.com/mackanfkp/
* License: GPLv2

Why use this plugin?
--------------------------------
This plugin was made to separate logic from markup when building plugins and widgets for Wordpress. Without a template engine like this you have to write your markup in your classes and methods which makes the code, not only ugly, but also very hard to read.


Basic usage:
--------------------------------

	// Create a new instance of the Masher_Template_Engine

	// ...using templates located here in Masher_Template_Engine plugin directory.
	$o = Masher_Template_Engine::instance();

	// ...using templates located in your custom directory.
	$o = Masher_Template_Engine::instance('/path/to/your/templates/');

	// ...using templates located here in YOUR plugin directory.
	$o = Masher_Template_Engine::instance(plugin_dir_path( __FILE__ ) . '/templates/');

	// Set some variables
	$o->set('key1', 'val1');
	$o->set('key2', 'val2');

	// ...and/or set an array or variables
	$o->setArray(array('key3' => 'val3', 'key4' => 'val4', 'key1' => 'new_val'), $merge = true);

	// Output the template named example.phtml
	$o->show('example');

	// ...or save output to a variable
	$html = $o->load('example');


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/mackanfkp/masher-template-engine/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

