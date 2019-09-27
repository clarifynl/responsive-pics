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
			add_action('init',           array($this, 'process_handler'));
			add_action('plugins_loaded', array($this, 'init'));
			add_action('admin_menu',     array($this, 'admin_menu'));
			add_action('admin_bar_menu', array($this, 'admin_bar_menu'), 100);
		}

		/**
		 * Init
		 */
		public function init() {
			require_once(plugin_dir_path( __FILE__ ) . 'classes/ResponsivePics.php');
		}

		/**
		 * Process handler
		 */
		public function process_handler() {
			if (!isset($_GET['resize_process'] ) || !isset($_GET['_wpnonce'])) {
				return;
			}

			if (!wp_verify_nonce($_GET['_wpnonce'], 'process')) {
				return;
			}

			if ('clear_queue' === $_GET['resize_process']) {
				ResponsivePics::clearResizeProcessQueue();
			}
		}

		/**
		 * Admin menu
		 */
		public function admin_menu() {
			add_menu_page(
				__('Responsive Pics', 'responsive-pics'),
				'Responsive Pics',
				'manage_options',
				'responsive-pics',
				array($this, 'show_process_queue'),
				'dashicons-images-alt2',
				80
			);
		}

		public function show_process_queue() {
			echo '<div class="wrap">'.
				 '<h1 class="wp-heading-inline">' . __('Responsive Pics', 'responsive-pics') . '</h1>' .
				 '</div>';

			var_dump(ResponsivePics::getResizeProcessQueue());
		}

		/**
		 * Admin bar menu
		 *
		 * @param WP_Admin_Bar $wp_admin_bar
		 */
		public function admin_bar_menu($wp_admin_bar) {
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
				'id'     => 'responsive-pics-clear',
				'title'  => __('Clear resize queue', 'responsive-pics'),
				'href'   => wp_nonce_url(admin_url('?resize_process=clear_queue'), 'process')
			));

			$wp_admin_bar->add_menu(array(
				'parent' => 'responsive-pics',
				'id'     => 'responsive-pics-show',
				'title'  => __('View resize queue', 'responsive-pics'),
				'href'   => wp_nonce_url(admin_url('?page=responsive-pics'), 'process')
			));
		}

		/**
		 * Admin notices
		 */
		public static function admin_notices() {
			// On Clear
			if ('clear_queue' === $_GET['resize_process']) {
				echo '<div class="notice notice-success is-dismissible">'.
					'<p>'. __('The responsive pics resize background process queue has been cleared succesfully.', 'responsive-pics') .'</p>'.
				'</div>';
			}
		}
	}

	new ResponsivePicsPlugin();
}