<?php
/*
	Plugin Name: Responsive Pics
	Plugin URI: https://responsive.pics
	Description: Responsive Pics is a Wordpress tool for resizing images on the fly.
	Author: Clarify (previously Booreiland)
	Version: 1.8.3
	Author URI: https://clarify.nl
	Copyright: Wimer Hazenberg, Toine Kamps
*/

defined('ABSPATH') or exit;

class ResponsivePicsWP
{
	private static $instance;
	public static $enqueue;
	public $api;
	public $helpers;
	public $error;
	public $process;
	public $rules;
	public $breakpoints;
	public $grid;
	public $sources;
	public $focalpoint;
	public $s3offload;

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
		define('RESPONSIVE_PICS_DIR', plugin_dir_path(__FILE__));
		define('RESPONSIVE_PICS_VERSION', '1.8.3');
		define('RESPONSIVE_PICS_TEXTDOMAIN', 'responsive-pics');

		// Init
		if (!class_exists('ResponsivePics')) {
			require_once(RESPONSIVE_PICS_DIR . '/lib/action-scheduler/action-scheduler.php');
			require_once(RESPONSIVE_PICS_DIR . '/lib/wpackio/enqueue/inc/Enqueue.php');
			require_once(RESPONSIVE_PICS_DIR . '/src/class-responsive-pics.php');

			self::$enqueue = new \WPackio\Enqueue('responsivePics', 'dist', RESPONSIVE_PICS_VERSION, 'plugin', __FILE__);
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
