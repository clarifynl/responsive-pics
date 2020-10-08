<?php

class RP_Error extends ResponsivePics {

	// format error
	public function get_error($message) {
		if (ResponsivePics()->helpers->is_rest_api_request()) {
			$this->return_error($message);
		} else {
			$this->show_error($message);
		}
	}

	// echo error message
	public function show_error($message) {
		$error = sprintf('<pre>%s error: %s</pre>', get_parent_class(), $message);
		echo $error;
	}

	// return WP_Error
	public function return_error($message) {
		return new WP_Error('401', sprintf('%s error: %s', get_parent_class(), $message));
	}
}
