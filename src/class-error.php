<?php

class RP_Error extends ResponsivePics {

	// print error message
	public function show_error($message) {
		$error = sprintf('<pre>%s error: %s</pre>', get_class(), $message);

		echo $error;
	}
}
