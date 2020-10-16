<?php

class RP_Helpers extends ResponsivePics {

	// calculates column shortcut (e.g. "xs-5") to actual pixels
	public function columns_to_pixels($input) {
		$components = explode('-', $input);

		$key = $components[0];
		$col = $components[1];

		// check breakpoint
		$breakpoint = ResponsivePics()->process->process_breakpoint($key);
		if (is_wp_error($breakpoint)) {
			return $breakpoint;
		}

		if ($this->contains($col, '/')) {
			$col = explode('/', $col)[0];
		}

		if ($col === 'full') {
			$next_breakpoint = ResponsivePics()->breakpoints->get_next_breakpoint($key);

			// use max breakpoint if there's no bigger one left
			if (!isset($next_breakpoint)) {
				$next_breakpoint = $key;
			}

			$next_width = self::$breakpoints[$next_breakpoint];

			if (!isset($next_width)) {
				return ResponsivePics()->error->add_error('missing', sprintf('no breakpoint set for %s', $key), $key);
			}

			return $next_width;

		} else if ($this->match($col, '/(\d+)/')) {
			if ($col < 1 || $col > self::$columns) {
				return ResponsivePics()->error->add_error('invalid', sprintf('number of columns should be between 1 and %s', self::$columns), self::$columns);
			}
		} else {
			return ResponsivePics()->error->add_error('invalid', sprintf('invalid columns: %s', $col), $col);
		}

		$grid_width = self::$grid_widths[$key];
		if (!isset($grid_width)) {
			return ResponsivePics()->error->add_error('missing', sprintf('no width found for breakpoint %s', $key), $key);
		}

		$column_pixels = ($grid_width - (self::$columns) * self::$gutter) / self::$columns;
		$pixels = floor($column_pixels * $col + self::$gutter * ($col - 1));

		return $pixels;
	}

	// get suffix for resized image
	public function get_resized_suffix($width, $height, $ratio, $crop) {
		if ($ratio === 1) {
			$ratio_indicator = '';
		} else {
			$ratio_indicator = '@' . $ratio . 'x';
		}

		if ($crop === false) {
			$crop_indicator = '';
		} else {
			$crop_indicator = '-' . implode('-', $crop);
		}

		// note: actual dimensions can be different from the dimensions appended to the filename, but we don't know those before we actually resize
		$suffix = sprintf('%sx%s%s%s', (int)$width, (int)$height, $crop_indicator, $ratio_indicator);

		return $suffix;
	}

	// get a css rule for targeting high dpi screens
	public function get_media_query_2x($breakpoint) {
		// apparently this targets high dpi screens cross-browser
		return sprintf('@media only screen and (-webkit-min-device-pixel-ratio: 2) and (min-width: %spx), only screen and (min-resolution: 192dpi) and (min-width: %spx)', $breakpoint, $breakpoint);
	}

	// check if resize request is not a pending scheduled action
	public function is_scheduled_action($request, $id) {
		$scheduled = as_get_scheduled_actions([
			'group'    => 'process_resize_request_' . $id,
			'status'   => \ActionScheduler_Store::STATUS_PENDING,
			'per_page' => -1
		]);

		foreach ($scheduled as $action) {
			// Get protected value from object
			$reflection = new \ReflectionClass($action);
			$property   = $reflection->getProperty('args');
			$property->setAccessible(true);
			$action_args = $property->getValue($action);

			if ($action_args === $request) {
				return true;
			}
		}

		return false;
	}

	// check if request comes from rest api
	public function is_rest_api_request() {
		if (empty($_SERVER['REQUEST_URI'])) {
			// Probably a CLI request
			return false;
		}

		// check if request contains rest url prefix (/wp-json)
		$rest_prefix = trailingslashit(rest_get_url_prefix());
		$is_rest_api_request = (false !== strpos($_SERVER['REQUEST_URI'], $rest_prefix));

		return apply_filters('is_rest_api_request', $is_rest_api_request);
	}

	// check if png file has transparent background
	public function is_alpha_png($fn) {
		return (ord(@file_get_contents($fn, NULL, NULL, 25, 1)) === 6);
	}

	// check if gif file is animated
	public function is_gif_ani($fn) {
		if (!($fh = @fopen($fn, 'rb')))
			return false;

		$count = 0;
		while (!feof($fh) && $count < 2) {
			$chunk = fread($fh, 1024 * 100);
			$count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00[\x2C\x21]#s', $chunk, $matches);
		}

		fclose($fh);
		return $count > 1;
	}

	// returns true if a string contains a substring
	public function contains($haystack, $needle) {
		return strpos($haystack, $needle) !== false;
	}

	// returns regex match of string
	public function match($string, $regex) {
		$found = preg_match($regex, $string, $matches);

		return $found ? $matches[0] : null;
	}
}
