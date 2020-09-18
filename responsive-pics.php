<?php
/*
	Plugin Name: Responsive Pics
	Plugin URI: https://responsive.pics
	Description: Responsive Pics is a Wordpress tool for resizing images on the fly.
	Author: Booreiland
	Version: 1.0.1
	Author URI: https://booreiland.amsterdam
*/

add_action('plugins_loaded', function() {
	if (!class_exists('ResponsivePics')) {
		require_once(plugin_dir_path( __FILE__ ) . 'libraries/action-scheduler/action-scheduler.php');
		require_once(plugin_dir_path( __FILE__ ) . 'classes/ResponsivePics.php');
	}
});