<?php

class RP_Error extends ResponsivePics {
	// add to error
	public function add_error($code = 'error', $message = '', $data = null) {
		self::$wp_error->add('responsive_pics_' . $code, $message, $data);
		return self::$wp_error;
	}

	// get errors
	public function get_error($error = null) {
		if (is_wp_error($error)) {
			if (ResponsivePics()->helpers->is_rest_api_request()) {
				return $error;
			} else {
				$this->show_error($error);
			}
		} else {
			return $error;
		}
	}

	// display error messages
	public function show_error($error) {
		if (is_wp_error($error)) {
			$error_codes = $error->get_error_codes();

			if (!empty($error_codes)) {
				$output = '<pre class="responsive-pics-error"><h6>' . get_parent_class() . ' errors</h6>';

				foreach ($error_codes as $code) {
					$error_messages = $error->get_error_messages($code);

					if (!empty($error_messages)) {
						$output .= '<section><strong>'. $code . ':</strong><ul>';
						foreach ($error_messages as $message) {
							$output .= '<li>' . $message . '</li>';
						}
						$output .= '</ul></section>';
					}
				}

				$output .= '</pre>';
				echo $output;
			}
		}

		return;
	}
}
