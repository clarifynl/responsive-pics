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

		/**
		 * ResponsivePicsPlugin constructor.
		 */
		public function __construct() {
			add_action('plugins_loaded', array($this, 'init'));
			add_action('admin_bar_menu', array($this, 'admin_bar'), 100);
			add_action('init',           array($this, 'process_handler'));
		}

		/**
		 * Init
		 */
		public function init() {
			require_once(plugin_dir_path( __FILE__ ) . 'classes/ResponsivePics.php');
		}

		/**
		 * Admin bar
		 *
		 * @param WP_Admin_Bar $wp_admin_bar
		 */
		public function admin_bar( $wp_admin_bar ) {
			if (!current_user_can('manage_options')) {
				return;
			}

			$wp_admin_bar->add_menu(array(
				'id'    => 'responsive-pics',
				'title' => __('Responsive Pics', 'responsive-pics'),
				'href'  => '#',
			));
			$wp_admin_bar->add_menu(array(
				'parent' => 'responsive-pics',
				'id'     => 'responsive-pics-queue',
				'title'  => __('View resize queue', 'responsive-pics'),
				'href'   => wp_nonce_url(admin_url( '?process=queue'), 'process'),
			));
		}

		/**
		 * Process handler
		 */
		public function process_handler() {
			if (!isset($_GET['process'] ) || !isset($_GET['_wpnonce'])) {
				return;
			}

			if (!wp_verify_nonce($_GET['_wpnonce'], 'process')) {
				return;
			}

			if ('queue' === $_GET['process']) {
				// $this->show_queue();
			}
		}
	}

	new ResponsivePicsPlugin();
}