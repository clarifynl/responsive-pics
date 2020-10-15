<?php

class RP_Process extends ResponsivePics {

	// validates and returns image id
	public function process_image_id($id = null) {
		if (!$id) {
			return ResponsivePics()->error->add_error('invalid', 'image id is undefined');
		} elseif (is_array($id)) {
			return $id[0];
		} elseif (!is_int($id)) {
			return ResponsivePics()->error->add_error('invalid', sprintf('image id %s is not an integer', $id), $id);
		}

		return $id;
	}

	// validates and returns classes as an array
	public function process_classes($classes = null) {
		if (!is_array($classes) && !is_string($classes)) {
			return ResponsivePics()->error->add_error('invalid', 'classes parameter is neither a string nor an array', $classes);
		} elseif (!is_array($classes) && is_string($classes)) {
			if (!empty($classes)) {
				$classes = preg_split('/[\s,]+/', $classes);
			} else {
				$classes = [];
			}

			return $classes;
		}
	}

	// breakpoint can be shortcut (e.g. "xs") or number
	public function process_breakpoint($input) {
		$input = trim($input);

		if (isset(self::$breakpoints[$input])) {
			return self::$breakpoints[$input];
		} else {
			return $input;
		}

		return false;
	}

	// dimensions can be shortcut (e.g. "xs-5"), width (e.g. "400") or width and height (e.g. "400 300");
	public function process_dimensions($input) {
		$dimensions = trim($input);
		$width      = -1;
		$height     = -1;
		$crop       = false;
		$crop_ratio = null;

		if (ResponsivePics()->helpers->contains($dimensions, '-')) {
			if (ResponsivePics()->helpers->contains($dimensions, ' ')) {
				// width and height supplied
				$wh        = explode(' ', $dimensions);
				$dimension = trim($wh[0]);
				$height    = trim($wh[1]);
				$width     = ResponsivePics()->helpers->columns_to_pixels($dimension);
			} else {
				$width = ResponsivePics()->helpers->columns_to_pixels($dimensions);
			}
		} else {
			if (ResponsivePics()->helpers->contains($dimensions, ' ')) {
				// width and height supplied
				$wh     = explode(' ', $dimensions);
				$width  = trim($wh[0]);
				$height = trim($wh[1]);
			} else {
				// height will be calculated based on width
				$width = ResponsivePics()->helpers->match($dimensions, '/(\d+)/');

				if (!isset($width)) {
					return ResponsivePics()->error->add_error('invalid', sprintf('width is undefined in %s', $dimensions), $dimensions);
				}
			}
		}

		if (ResponsivePics()->helpers->contains($dimensions, '/')) {
			// height is a specified factor of weight
			$wh         = explode('/', $dimensions);
			$crop_ratio = trim($wh[1]);

			if ($this->process_ratio($crop_ratio)) {
				$height = $width * $crop_ratio;
			} else {
				return ResponsivePics()->error->add_error('invalid', sprintf('the crop ratio %s needs to be higher then 0 and equal or lower then 2', (string) $ratio), $ratio);
			}
		}

		return [
			'input'      => $input,
			'width'      => (int) $width,
			'height'     => (int) $height,
			'crop_ratio' => $crop_ratio
		];
	}

	// returns true if ratio is a number and between reasonable values 0-2
	public function process_ratio($ratio) {
		if (is_numeric($ratio) && (0 < $ratio) && ($ratio <= 2)) {
			return true;
		} else {
			return false;
		}
	}

	// crop values can be single shortcut values (e.g. "c") or two dimensional values (e.g. "l t");
	public function process_crop($input) {
		if ($input === false) {
			return false;
		}

		$shortcuts = explode(' ', trim($input));

		if (sizeof($shortcuts) === 1) {
			$shortcuts = self::$crop_shortcuts[$shortcuts[0]];
			$shortcuts = explode(' ', trim($shortcuts));
		}

		$result = [];

		foreach($shortcuts as $key => $value) {
			$result[] = self::$crop_map[$value];
		}

		return $result;
	}

	// process the scheduled resize action
	public static function process_resize_request($id, $quality, $width, $height, $crop, $ratio) {
		$file_path   = get_attached_file($id);
		$path_parts  = pathinfo($file_path);
		$suffix      = ResponsivePics()->helpers->get_resized_suffix($width, $height, $ratio, $crop);
		$resize_path = join(DIRECTORY_SEPARATOR, [$path_parts['dirname'], $path_parts['filename'] . '-' . $suffix . '.' . $path_parts['extension']]);
		$wp_editor   = wp_get_image_editor($file_path);

		// Check if image exists
		if (!file_exists($resize_path)) {
			if (!is_wp_error($wp_editor)) {
				$wp_editor->set_quality($quality);
				$wp_editor->resize($width * $ratio, $height * $ratio, $crop);
				$wp_editor->save($resize_path);
			} else {
				syslog(LOG_ERR, sprintf('error resizing image "%s"', $resize_path));
			}
		}
	}
}
