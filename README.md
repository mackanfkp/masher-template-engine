Masher Template Engine
================================

* Description: A simple template engine for plugins and/or methods in Wordpress.
* Author: mackanfkp
* Licence: 


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

	// Output the template named example.phtml
	$o->show('example');

	// ...or save output to a variable
	$html = $o->load('example');
