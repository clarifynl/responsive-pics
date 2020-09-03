<?php
/*
	Plugin Name: Responsive Pics
	Plugin URI: https://responsive.pics
	Description: Responsive Pics is a Wordpress tool for resizing images on the fly.
	Author: Booreiland
	Version: 1.0.0
	Author URI: https://booreiland.amsterdam
*/


// exit if accessed directly
if (!defined('ABSPATH') ) exit;

// check if class already exists
if (!class_exists('ResponsivePicsPlugin')) {

	class ResponsivePicsPlugin {
		/**
		 * ResponsivePicsPlugin constructor.
		 */
		public function __construct() {
			add_action('plugins_loaded', array($this, 'init'));
			add_filter('big_image_size_threshold', '__return_false');
		}

		/**
		 * Init
		 */
		public function init() {
			require_once(plugin_dir_path( __FILE__ ) . 'libraries/action-scheduler/action-scheduler.php');
			require_once(plugin_dir_path( __FILE__ ) . 'classes/ResponsivePics.php');
		}
	}

	new ResponsivePicsPlugin();
}