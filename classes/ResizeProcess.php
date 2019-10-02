<?php

// exit if accessed directly
if (!defined('ABSPATH')) exit;

// load wp-background-processing
require_once(plugin_dir_path( __FILE__ ) . '../includes/wp-background-processing/wp-background-processing.php');


class WP_Resize_Process extends WP_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'resize_process';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task($request) {
		$editor = wp_get_image_editor($request['file_path']);

		// Check if image exists
		if (!file_exists($request['resize_path'])) {
			if (!is_wp_error($editor)) {
				$editor->set_quality($request['quality']);
				$editor->resize($request['width'] * $request['ratio'], $request['height'] * $request['ratio'], $request['crop']);
				$editor->save($request['resize_path']);

				// remove from queue
				return false;

			} else {
				$message = sprintf('error resizing image "%s"', $request['resize_path']);
				$error   = sprintf('<pre>%s error: %s</pre>', get_class(), $message);

				echo $error;
			}
		} else {
			// remove from queue
			return false;
		}
	}

	/**
	 * Show Process Queue
	 *
	 * This will return the current cron batch in the database, not the full data object.
	 *
	 * @return array
	 */
	public function show_queue() {
		global $wpdb;

		$identifier = (string)$this->identifier;
		$table      = $wpdb->options;
		$column     = 'option_name';

		if (is_multisite()) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		// Get current cron batch
		$key  = $wpdb->esc_like($identifier . '_batch_') . '%';
		$data = $wpdb->get_results($wpdb->prepare("
			SELECT option_value
			FROM {$table}
			WHERE {$column} LIKE %s",
		$key), ARRAY_A);

		return $data;
	}

	/**
	 * Cancel Process
	 *
	 * This will cancel the current cron batch in the database, not the full data object.
	 *
	 */
	public function cancel_process() {
		parent::unlock_process();
		parent::cancel_process();

		add_action('admin_notices', array('ResponsivePicsPlugin', 'admin_notices'));
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();
	}
}