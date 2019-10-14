<?php
/*
	Plugin Name: Responsive Pics
	Plugin URI: https://responsive.pics
	Description: Responsive Pics is a Wordpress tool for resizing images on the fly.
	Author: Booreiland
	Version: 1.0.0-beta
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
		}

		/**
		 * Init
		 */
		public function init() {
			require_once(plugin_dir_path( __FILE__ ) . 'classes/ResponsivePics.php');
		}

		/**
		 * Admin menu
		 */
		public function admin_menu() {
			add_management_page(
				__('Responsive Pics', 'responsive-pics'),
				__('Responsive Pics', 'responsive-pics'),
				'manage_options',
				'responsive-pics',
				array($this, 'show_process_queue')
			);
		}

		/**
		 * Construct markup for current resize queue
		 */
		public function show_process_queue() {
			echo '<div class="wrap">'.
				 '<h1 class="wp-heading-inline">' . __('Responsive Pics', 'responsive-pics') . '</h1>' .
				 '</div>';

			$output  = '<h2>' . __('Resize Queue', 'responsive-pics') . '</h2>';
			$queue   = ResponsivePics::getResizeProcessQueue();

			$columns = [
				'id'     => __('Image', 'responsive-pics'),
				'width'  => __('Resize Width', 'responsive-pics'),
				'height' => __('Resize Height', 'responsive-pics'),
				'crop'   => __('Crop Position', 'responsive-pics'),
				'ratio'  => __('Density', 'responsive-pics')
			];

			if (!empty($queue)) {
				// Get table head values
				$output .= '<form method="post" action="tools.php?page=responsive-pics">';
				$output .= '<table class="widefat striped"><thead><tr>';
				$output .= '<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">'. __('Select All', 'responsive-pics') .'</label><input id="cb-select-all-1" type="checkbox"></td>';
				foreach ($columns as $key => $name) {
					$output .= '<th scope="col" id="'. $key .'" class="manage-column column-cb check-column'. $key .'">'. $name .'</th>';
				}
				$output .= '</tr></thead><tbody>';

				// Loop through batches
				foreach ($queue as $key => $item) {
					$id     = $item['option_id'];
					$name   = $item['option_name'];
					$values = unserialize($item['option_value']);

					$output .= '<tr>';
					$output .= '<td scope="row">'. sprintf('<input type="checkbox" name="delete[%1$s][%2$s]" value="%1$s">', esc_attr($id), esc_attr(rawurlencode($name))) .'</td>';
					$output .= '<td scope="row" colspan="'. count($columns) .'"><table class="widefat striped">';

					// Loop through batch items
					foreach ($values as $value) {
						$output .= '<tr>';

						// Loop through admin columns
						foreach ($columns as $key => $name) {
							$output .= '<td scope="col" data-colname="'. $key .'">';

							// Construct column value
							if (!empty($value[$key]) && is_array($value[$key])) {
								$col = implode(' ', $value[$key]);
							} elseif ($key === 'id') {
								$col = wp_get_attachment_image($value[$key], [50, 50]);
							} else {
								$col = $value[$key];
							}

							$output .= $col;
							$output .= '</td>';
						}

						$output .= '</tr>';
					}

					$output .= '</table></td>';
					$output .= '</tr>';
				}

				$output .= '</tbody></table>';

				// Submit form
				$output .= wp_nonce_field('bulk-delete-resize-batch', '_wpnonce', true, false);
				$output .= get_submit_button(__('Delete Selected Items', 'responsive-pics'), 'primary large', 'delete_resize_batches');
				$output .= '</form>';

			} else {
				$output .=  '<div class="wrap"><p>' . __('No items left in resize queue', 'responsive-pics') . '</p></div>';
			}

			echo $output;
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

			if (isset($_POST['delete_resize_batches'])) {
				if (!current_user_can('manage_options')) {
					wp_die(esc_html__('You are not allowed to delete resize batches.', 'responsive-pics'), 401);
				}
				check_admin_referer('bulk-delete-resize-batch');

				if (empty($_POST['delete'])) {
					return;
				}

				$delete  = wp_unslash($_POST['delete']);
				$deleted = 0;

				// foreach ($delete as $next_run => $batch) {
				// 	foreach ($events as $id => $sig ) {
				// 		if ( 'crontrol_cron_job' === $id && ! current_user_can( 'edit_files' ) ) {
				// 			continue;
				// 		}
				// 		if ( $this->delete_cron( urldecode( $id ), $sig, $next_run ) ) {
				// 			$deleted++;
				// 		}
				// 	}
				// }

				$redirect = array(
					'page'    => 'responsive-pics',
					'deleted' => $deleted
				);
				wp_safe_redirect(add_query_arg($redirect, admin_url('tools.php')));
				exit;
			}

			if ('clear_queue' === $_GET['resize_process']) {
				ResponsivePics::clearResizeProcessQueue();
			}
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