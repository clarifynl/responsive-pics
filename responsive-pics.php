<?php
/*
	Plugin Name: Responsive Pics
	Plugin URI: https://responsive.pics
	Description: Responsive Pics is a Wordpress tool for resizing images on the fly.
	Author: Booreiland
	Version: 1.2.0
	Author URI: https://booreiland.amsterdam
*/

defined('ABSPATH') or exit;


class ResponsivePicsWP {

	private static $instance;

	/**
	 * Construct
	 */
	function __construct() {
		// php check
		if (version_compare(phpversion(), '5.6', '<')) {
			add_action('admin_notices', array($this, 'upgrade_notice'));
			return;
		}

		// Variables
		define('RESPONSIVE_PICS_DIR', plugin_dir_path( __FILE__ ));

		// Init
		if (!class_exists('ResponsivePics')) {
			include(RESPONSIVE_PICS_DIR . '/lib/action-scheduler/action-scheduler.php');
			include(RESPONSIVE_PICS_DIR . '/src/class-responsive-pics.php');
		}
	}

	/**
	 * Singleton
	 */
	public static function instance() {
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Require PHP 5.6+
	 */
	function upgrade_notice() {
		$message = __('ResponsivePics requires PHP %s or above. Please contact your host and request a PHP upgrade.', 'responsive-pics');
		echo '<div class="error"><p>' . sprintf($message, '5.6') . '</p></div>';
	}
}


function ResponsivePics() {
	return ResponsivePicsWP::instance();
}

ResponsivePics();