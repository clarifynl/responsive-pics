<?php

class RP_Error extends ResponsivePics {
	// format error
	public function get_error($code = 'error', $message = '', $data = null) {
		if (ResponsivePics()->helpers->is_rest_api_request()) {
			return $this->return_error($code, $message, $data);
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
	public function return_error($code, $message, $data) {
		return new WP_Error($code, sprintf('%s error: %s', get_parent_class(), $message), $data);
	}
}
