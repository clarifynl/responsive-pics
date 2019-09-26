<?php
/*
	Plugin Name: Responsive Pics
	Plugin URI: https://responsive.pics
	Description: Responsive Pics is a Wordpress tool for resizing images on the fly.
	Author: Booreiland
	Version: 0.8
	Author URI: https://booreiland.amsterdam
*/


// exit if accessed directly
if (!defined('ABSPATH') ) exit;


// check if class already exists
if (!class_exists('ResponsivePicsPlugin')) {

	class ResponsivePicsPlugin {

		public function __construct() {
			add_action('plugins_loaded', array('ResponsivePicsPlugin', 'init'));
		}

		public static function init() {
			require_once(plugin_dir_path( __FILE__ ) . '/includes/wp-background-processing/wp-background-processing.php');
			require_once(plugin_dir_path( __FILE__ ) . '/classes/ResponsivePics.php');
			require_once(plugin_dir_path( __FILE__ ) . '/classes/ResizeProcess.php');
		}
	}

	new ResponsivePicsPlugin();
}