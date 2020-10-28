<?php

class RP_Process extends ResponsivePics {

	// validates and returns image id
	public function process_image($id = null) {
		if (!$id) {
			ResponsivePics()->error->add_error('invalid', 'image id is undefined');
			return false;

		} elseif (is_array($id)) {
			$id = $id[0];

		} elseif (!is_int($id)) {
			ResponsivePics()->error->add_error('invalid', sprintf('image id %s is not an integer', $id), $id);
			return false;
		}

		// check for image url
		$url = wp_get_attachment_url($id);
		if (!$url) {
			ResponsivePics()->error->add_error('missing', sprintf('url does not exist for id %s', $id), $id);
			return false;
		}

		// check for image path
		$file_path = get_attached_file($id);
		if (!$file_path) {
			ResponsivePics()->error->add_error('missing', sprintf('file does not exist for id %s', $id), $id);
			return false;
		}

		// check for image dimensions
		$meta_data       = wp_get_attachment_metadata($id);
		$original_width  = $meta_data['width'];
		$original_height = $meta_data['height'];

		if (!$original_width || !$original_height) {
			ResponsivePics()->error->add_error('missing', sprintf('no dimensions for file id %s', $id), $meta_data);
			return false;
		}

		return $id;
	}

	// validates sizes
	public function process_sizes($id, $sizes, $order = 'desc', $art_direction = true, $img_crop = null) {
		$url       = wp_get_attachment_url($id);
		$mime_type = get_post_mime_type($id);
		$alt       = get_post_meta($id, '_wp_attachment_image_alt', true);
		$alpha     = false;
		$animated  = false;

		// check if png has alpha channel
		if ($mime_type === 'image/png') {
			$alpha = ResponsivePics()->helpers->is_alpha_png($file_path);
		}

		// check if gif is animated
		if ($mime_type === 'image/gif') {
			$animated = ResponsivePics()->helpers->is_gif_ani($file_path);
		}

		// unsupported mime-type, return original source without breakpoints
		if (!in_array($mime_type, self::$supported_mime_types) || $animated) {
			return [
				'sources' => [[
					'source1x' => $url,
					'ratio'    => 1
				]],
				'mimetype' => $mime_type,
				'alt'      => $alt,
				'alpha'    => $alpha
			];
		}

		// get resize rules
		if ($art_direction) {
			$rules = ResponsivePics()->rules->get_art_image_rules($sizes, $order);
		} else {
			$rules = ResponsivePics()->rules->get_image_rules($sizes, $order, $img_crop);
		}

		// get resize sources
		$sources = [];
		if ($rules) {
			$sources = ResponsivePics()->sources->get_resize_sources($id, $rules, $order);
		}

		return [
			'sources'  => $sources,
			'alt'      => $alt,
			'mimetype' => $mime_type,
			'alpha'    => $alpha
		];
	}

	// validates and returns classes as an array
	public function process_classes($classes = null) {
		if (!is_array($classes) && !is_string($classes)) {
			ResponsivePics()->error->add_error('invalid', 'classes parameter is neither a (comma separated) string nor an array', $classes);
		} elseif (!is_array($classes) && is_string($classes)) {
			if (!empty($classes)) {
				$classes = preg_split('/[\s,]+/', $classes);
			} else {
				$classes = [];
			}

			return $classes;
		}

		return $classes;
	}

	// validates boolean value
	public function process_boolean($boolean = false, $type = 'boolean') {
		if (is_bool($boolean)) {
			return $boolean;
		} elseif (is_string($boolean)) {
			return $boolean === 'true';
		} else {
			ResponsivePics()->error->add_error('invalid', sprintf('%s parameter %s is not a valid boolean', $type, (string) $boolean), $boolean);
		}

		return false;
	}

	// breakpoint can be shortcut (e.g. "xs") or number
	public function process_breakpoint($input) {
		$input = trim($input);

		if (isset(self::$breakpoints[$input])) {
			return self::$breakpoints[$input];
		} elseif (is_numeric($input)) {
			return $input;
		} else {
			ResponsivePics()->error->add_error('invalid', sprintf('breakpoint %s is neither defined nor a number', $input), self::$breakpoints);
			return false;
		}

		return $input;
	}

	// dimensions can be shortcut (e.g. "xs-5"), width (e.g. "400") or width and height (e.g. "400 300");
	public function process_dimensions($input) {
		$dimensions = trim($input);
		$width      = -1;
		$height     = -1;
		$ratio      = null;
		$crop       = false;

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
					ResponsivePics()->error->add_error('invalid', sprintf('width is undefined in %s', $dimensions), $dimensions);
				}
			}
		}

		// get height ratio
		if (ResponsivePics()->helpers->contains($dimensions, '/')) {
			$wh    = explode('/', $dimensions);
			$ratio = trim($wh[1]);

			// strip off crop positions
			if (ResponsivePics()->helpers->contains($ratio, '|')) {
				$comp  = explode('|', $ratio);
				$ratio = trim($comp[0]);
			}

			if ($this->process_ratio($ratio)) {
				$height = $width * $ratio;
			} else {
				ResponsivePics()->error->add_error('invalid', sprintf('the crop ratio %s in size %s needs to be higher then 0 and equal or lower then 2', (string) $ratio, (string) $dimensions), $ratio);
			}
		}

		// get crop positions
		if (ResponsivePics()->helpers->contains($dimensions, '|')) {
			$comp = explode('|', $dimensions);
			$dm   = trim($comp[0]);
			$cr   = trim($comp[1]);
			$crop = ResponsivePics()->process->process_crop($cr);
		}

		return [
			'input'  => $input,
			'width'  => (int) $width,
			'height' => (int) $height,
			'ratio'  => $ratio,
			'crop'   => $crop
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
			if (isset(self::$crop_shortcuts[$shortcuts[0]])) {
				$shortcuts = self::$crop_shortcuts[$shortcuts[0]];
				$shortcuts = explode(' ', trim($shortcuts));
			} else {
				ResponsivePics()->error->add_error('invalid', sprintf('crop shortcut %s is not defined', $shortcuts[0]), self::$crop_shortcuts);
				$shortcuts = [];
			}
		}

		$result = [];
		foreach($shortcuts as $key => $value) {
			if (isset(self::$crop_map[$value])) {
				$result[] = self::$crop_map[$value];
			} else {
				$direction = ($key === 0) ? 'x' : 'y';
				ResponsivePics()->error->add_error('invalid', sprintf('crop_%s position %s is not defined', $direction, $value), self::$crop_map);
			}
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