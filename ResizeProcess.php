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
		// Resize the image
		file_put_contents(get_stylesheet_directory() . '/logs/debug.log', '['. date(DATE_RFC2822) .'] ' . join('\n', $request['editor']), FILE_APPEND | LOCK_EX);
		return false;

		// if (!is_wp_error($editor)) {
		// 	$editor->set_quality($request['quality']);
		// 	$editor->resize($request['width'] * $request['ratio'], $request['height'] * $request['ratio'], $request['crop']);
		// 	$editor->save($request['resize_path']);

		// 	file_put_contents(get_stylesheet_directory() . '/logs/debug.log', '['. date(DATE_RFC2822) .'] ' . $request['file_path'], FILE_APPEND | LOCK_EX);

		// 	// Remove from queue
		// 	return false;
		// } else {
		// 	file_put_contents(get_stylesheet_directory() . '/logs/error.log', '['. date(DATE_RFC2822) .'] ' . $request['file_path'], FILE_APPEND | LOCK_EX);
		// }
	}

	/**
	 * Debug function
	 *
	 * @return object
	 */
	public function show_queue() {
		$total = count((array)$this->data);
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

?>