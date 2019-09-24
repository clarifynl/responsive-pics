<?php

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
	 * Cancel Process
	 *
	 * Stop processing queue items, clear cronjob and delete batch.
	 *
	 */
	public function cancel_process() {
		parent::unlock_process();
		parent::cancel_process();
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