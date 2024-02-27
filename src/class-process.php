<?php

class RP_Process extends ResponsivePics
{
	/**
	 * Validates and returns the image id
	 */
	public function process_image($id = null) {
		if (!$id || $id === 0) {
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

		return $id;
	}

	/**
	 * Validate image sizes
	 */
	public function process_sizes($id, $sizes, $order = 'desc', $art_direction = true, $img_crop = null, $rest_route = null) {
		$file_path   = get_attached_file($id);
		$url         = wp_get_attachment_url($id);
		$meta_data   = wp_get_attachment_metadata($id);
		$mime_type   = get_post_mime_type($id);
		$alt         = get_post_meta($id, '_wp_attachment_image_alt', true);
		$focal_point = get_post_meta($id, 'responsive_pics_focal_point', true);
		$alpha       = false;
		$animated    = false;

		// check if png has alpha channel
		if ($mime_type === 'image/png') {
			$alpha = ResponsivePics()->helpers->is_alpha_png($file_path);
		}

		// check if gif is animated
		if ($mime_type === 'image/gif') {
			$animated = ResponsivePics()->helpers->is_gif_ani($file_path);
		}

		// check if mime-type is supported
		if (!in_array($mime_type, self::$supported_mime_types) ||
			$animated)
		{
			return [
				'alt'      => $alt,
				'alpha'    => $alpha,
				'animated' => $animated,
				'mimetype' => $mime_type,
				'sources'  => [[
					'source1x' => $url
				]]
			];
		}

		// check for image dimensions
		$original_width  = isset($meta_data['width']) ? $meta_data['width'] : null;
		$original_height = isset($meta_data['height']) ? $meta_data['height'] : null;

		if (!$original_width || !$original_height) {
			ResponsivePics()->error->add_error('missing', sprintf('no dimensions found in metadata for image %s', $id), $meta_data);
		}

		// default focal point
		if (!$focal_point) {
			$focal_point = [
				'x' => 50,
				'y' => 50
			];
		}

		// get resize rules
		$rules = ResponsivePics()->rules->get_image_rules($sizes, $order, $art_direction, $img_crop, $focal_point);

		// get resize sources
		$sources = [];
		if ($rules) {
			$sources = ResponsivePics()->sources->get_resize_sources($id, $rules, $order, $rest_route);
		}

		return [
			'alpha'    => $alpha,
			'alt'      => $alt,
			'animated' => $animated,
			'mimetype' => $mime_type,
			'sources'  => $sources
		];
	}

	/**
	 * Validates and returns classes as an array
	 */
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

	/**
	 * Validates boolean values
	 */
	public function process_boolean($boolean = false, $type = 'boolean') {
		if (is_bool($boolean)) {
			return $boolean;
		} elseif (is_string($boolean)) {
			return $boolean === 'true';
		} else {
			ResponsivePics()->error->add_error('invalid', sprintf('%s parameter is not a valid boolean', $type), $boolean);
		}

		return false;
	}

	/**
	 * Validates lazyload value, this can be a boolean or string 'native'
	 */
	public function process_lazyload($lazyload = false, $type = 'lazyload') {
		if (is_bool($lazyload)) {
			return $lazyload;
		} elseif ($lazyload === 'native') {
			return 'native';
		} elseif (is_string($lazyload)) {
			return $lazyload === 'true';
		} else {
			ResponsivePics()->error->add_error('invalid', sprintf('%s parameter is not a valid lazyload value', $type), $lazyload);
		}

		return false;
	}

	/**
	 * Validates breakpoints, these can be shortcut (e.g. "xs") or number
	 */
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

	/**
	 * Validates dimensions, these can be:
	 *
	 * - shortcut (e.g. "xs-5")
	 * - shortcut with height (e.g. "xs-5 400")
	 * - shortcut with ratio (e.g. "xs-5/0.75")
	 * - shortcut with height and crop (shorthand) (e.g. "xs-5 400|c")
	 * - shortcut with ratio and crop (shorthand) (e.g. "xs-5/0.75|c")
	 * - width (e.g. "400")
	 * - width and height (e.g. "400 300")
	 * - width with ratio (e.g. "400/0.75")
	 * - width and height and crop (shorthand) (e.g. "400 300|c")
	 * - width with ratio and crop (shorthand) (e.g. "400/0.75|c")
	 */
	public function process_dimensions($input, $focal_point = null) {
		$dimensions = trim($input);
		$width      = -1;
		$height     = -1;
		$factor     = null;
		$crop       = false;

		// get crop positions first to prevent double spaces (600 400|c t)
		if (ResponsivePics()->helpers->contains($dimensions, '|')) {
			$comp = explode('|', $dimensions);
			$dm   = trim($comp[0]);
			$cr   = trim($comp[1]);

			$dimensions = $dm;
			$crop       = ResponsivePics()->process->process_crop($cr, $focal_point);
		}

		// get breakpoint and dimensions
		if (ResponsivePics()->helpers->contains($dimensions, '-')) {
			if (ResponsivePics()->helpers->contains($dimensions, ' ')) {
				// width and height supplied
				$wh        = explode(' ', $dimensions);
				$dimension = trim($wh[0]);
				$height    = trim($wh[1]);
				$width     = ResponsivePics()->grid->columns_to_pixels($dimension);
			} else {
				$width = ResponsivePics()->grid->columns_to_pixels($dimensions);
			}
		} else {
			if (ResponsivePics()->helpers->contains($dimensions, ' ')) {
				// width and height supplied
				$wh     = explode(' ', $dimensions);
				$width  = trim($wh[0]);
				$hg     = trim($wh[1]);

				// if height does not start with /
				if (substr($hg, 0, 1) !== '/') {
					$height = $hg;
				}
			} else {
				// height will be calculated based on width
				$width = ResponsivePics()->helpers->match($dimensions, '/(\d+)/');

				if (!isset($width)) {
					ResponsivePics()->error->add_error('invalid', sprintf('width is undefined in %s', $dimensions), $dimensions);
				}
			}
		}

		// get height factor
		if (ResponsivePics()->helpers->contains($dimensions, '/')) {
			$wh     = explode('/', $dimensions);
			$factor = trim(end($wh));

			// set height based upon factor if height is not set yet
			if ($this->process_factor($factor)) {
				if ($height === -1) {
					$height = $width * $factor;
				}
			} else {
				ResponsivePics()->error->add_error('invalid', sprintf('the crop factor %s in size %s needs to be higher then 0 and equal or lower then 2', (string) $factor, (string) $dimensions), $factor);
			}
		}

		return [
			'input'  => $input,
			'width'  => (int) $width,
			'height' => (int) $height,
			'factor' => (float) $factor,
			'crop'   => $crop
		];
	}

	/**
	 * Validates crop factor
	 *
	 * @return factor & crop array if has valid /factor|crop syntax
	 */
	public function process_factor_crop($factor_crop = null, $focal_point = null) {
		$factor_crop = preg_replace('/\//', '', $factor_crop); // remove any leading /
		$factor      = null;
		$crop        = false;

		// Check for crop positions
		if (ResponsivePics()->helpers->contains($factor_crop, '|')) {
			$comp = explode('|', $factor_crop);
			$ft   = trim($comp[0]);
			$cr   = trim($comp[1]);

			if ($this->process_factor($ft)) {
				$factor = $this->process_factor($ft);
				$crop   = $this->process_crop($cr, $focal_point);
			} else {
				ResponsivePics()->error->add_error('invalid', sprintf('the crop factor %s needs to be a floating number between 0 and %d', (string) $ft, (float) self::$max_width_factor), $ft);
				return false;
			}
		// add default crop positions
		} else {
			if ($this->process_factor($factor_crop)) {
				$factor = $this->process_factor($factor_crop);
				$crop   = $this->process_crop('c');
			} else {
				ResponsivePics()->error->add_error('invalid', sprintf('the crop factor %s needs to be a floating number between 0 and %d', (string) $factor_crop, (float) self::$max_width_factor), $factor_crop);
				return false;
			}
		}

		return [
			'factor' => (float) $factor,
			'crop'   => $crop
		];
	}

	/**
	 * Validates factor
	 *
	 * @return true if factor is a number and between reasonable values 0-2
	 */
	public function process_factor($factor) {
		// replace comma's with dots
		$factor = str_replace(',', '.', $factor);

		if (is_numeric($factor) && (0 < $factor) && ($factor <= self::$max_width_factor)) {
			return $factor;
		} else {
			return false;
		}
	}

	/**
	 * Validates crop, these can be:
	 *
	 * - single shortcut string value (e.g. "c")
	 * - two dimensional string values (e.g. "l t")
	 * - two dimensional numeric values (e.g. 75 25)
	 *
	 * @return array
	 */
	public function process_crop($input, $focal_point = null) {
		if ($input === false) {
			return false;
		}

		$crop_positions = explode(' ', trim($input));
		if (empty($crop_positions)) {
			return false;
		}

		// Single shorthand value
		if (sizeof($crop_positions) === 1) {
			if (isset(self::$crop_shortcuts[$crop_positions[0]])) {
				if ($crop_positions[0] === 'f') {
					if (self::process_focal_point($focal_point)) {
						$focal_point = self::process_focal_point($focal_point);
						return $focal_point;
					} else {
						ResponsivePics()->error->add_error('invalid', sprintf('the focal point %s needs to be an array containing an x & y percentage', json_encode($focal_point)), $focal_point);
						return false;
					}
				}

				$crop_positions = self::$crop_shortcuts[$crop_positions[0]];
				$crop_positions = explode(' ', trim($crop_positions));
			} else {
				ResponsivePics()->error->add_error('invalid', sprintf('crop shortcut %s is not defined', $crop_positions[0]), self::$crop_shortcuts);
				$crop_positions = [];
			}
		}

		// Two numeric values
		if (!empty($crop_positions) &&
			is_numeric($crop_positions[0]) &&
			is_numeric($crop_positions[1])) {
			$x_perc = intval($crop_positions[0]);
			$y_perc = intval($crop_positions[1]);

			if (self::process_focal_percentage($x_perc) &&
				self::process_focal_percentage($y_perc)) {
				$crop_percentages = [
					'x' => $x_perc,
					'y' => $y_perc
				];

				return $crop_percentages;
			} else {
				$direction = self::process_focal_percentage($x_perc) ? 'y' : 'x';
				$value     = self::process_focal_percentage($x_perc) ? $y_perc : $x_perc;
				ResponsivePics()->error->add_error('invalid', sprintf('crop_%s position %s is not a percentage', $direction, $value), $crop_positions);
				return false;
			}
		}

		// Two string values
		$result = [];
		foreach ($crop_positions as $key => $value) {
			if (isset(self::$crop_map[$value]) && $key < 2) {
				$result[$key === 0 ? 'x' : 'y'] = self::$crop_map[$value];
			} else {
				$direction = ($key === 0) ? 'x' : 'y';
				ResponsivePics()->error->add_error('invalid', sprintf('crop_%s position %s is not defined', $direction, $value), self::$crop_map);
			}
		}

		$crop = self::process_crop_positions($result);

		return $crop;
	}

	// focal point must be an array containing an 'x' & 'y' key with float values
	public static function process_focal_point($focal_point = null) {
		if (is_array($focal_point) &&
			array_key_exists('x', $focal_point) &&
			array_key_exists('y', $focal_point)) {
			return [
				'x' => round($focal_point['x'], 0),
				'y' => round($focal_point['y'], 0)
			];
		} else {
			return false;
		}
	}

	// focal point must be an integer between 0 and 100
	public static function process_focal_percentage($focal_position = null) {
		$is_percentage = filter_var(
			$focal_position,
			FILTER_VALIDATE_INT,
			[
				'options' => [
					'min_range' => 0,
					'max_range' => 100
				]
			]
		);

		return is_int($is_percentage);
	}

	// convert crop positions array 'top left' to coordinates '0 0'
	public static function process_crop_positions($crop = []) {
		if (isset($crop['x']) &&
			is_string($crop['x']) &&
			isset(self::$crop_percentages[$crop['x']])) {
			$crop['x'] = self::$crop_percentages[$crop['x']];
		}

		if (isset($crop['y']) &&
			is_string($crop['y']) &&
			isset(self::$crop_percentages[$crop['y']])) {
			$crop['y'] = self::$crop_percentages[$crop['y']];
		}

		return $crop;
	}

	// calculate crop parameters based upon focal percentages
	public static function process_focal_crop($meta_data = null, $dest_w = null, $dest_h = null, $crop = []) {
		$src_w = $meta_data['width'];
		$src_h = $meta_data['height'];

		// scale factor of scaling dest rect inside source rect
		$scale_to_fill = min(($src_w / $dest_w), ($src_h / $dest_h));

		// dest rect inside source rect
		$scaled = [
			'w' => ($dest_w * $scale_to_fill),
			'h' => ($dest_h * $scale_to_fill)
		];

		// source crop x, y, rounded
		$src_x = round($crop['x'] / 100 * $src_w - ($scaled['w'] * 0.5));
		$src_y = round($crop['y'] / 100 * $src_h - ($scaled['h'] * 0.5));

		// after that, now round source width, height too
		$scaled['w'] = round($scaled['w']);
		$scaled['h'] = round($scaled['h']);

		// keep rect inside source dimensions
		if ($src_x < 0) {
			$src_x = 0;
		}

		if (($src_x + $scaled['w']) > $src_w) {
			$src_x = $src_w - $scaled['w'];
		}

		if ($src_y < 0) {
			$src_y = 0;
		}

		if ($src_y + $scaled['h'] > $src_h) {
			$src_y = $src_h - $scaled['h'];
		}

		return [
			'src_x'  => $src_x,
			'src_y'  => $src_y,
			'src_w'  => $scaled['w'],
			'src_h'  => $scaled['h'],
			'dest_w' => $dest_w,
			'dest_h' => $dest_h
		];
	}

	/**
	 * Process the scheduled resize action
	 *
	 * @param int        $id           The attachment ID
	 * @param int        $quality      The requested image quality
	 * @param int        $width.       The requested image width
	 * @param int        $height       The requested image height
	 * @param bool|array $crop         The requested image crop
	 * @param string     $resize_path  The requested image file path
	 * @param uri        $rest_route   The original WP REST API request
	 *
	 * @return void
	 */
	public static function process_resize_request($id, $quality, $width, $height, $crop, $ratio, $resize_path, $rest_route = '') {
		$file_path = get_attached_file($id);
		$meta_data = wp_get_attachment_metadata($id);
		$wp_editor = wp_get_image_editor($file_path);

		do_action('responsive_pics_request_processed', $id, $quality, $width, $height, $crop, $ratio, $resize_path, $rest_route);

		// Check if image exists
		$resized_file_exists = apply_filters('responsive_pics_file_exists', $id, [
			'path'   => $resize_path,
			'file'   => basename($resize_path),
			'width'  => $width,
			'height' => $height,
			'ratio'  => $ratio,
		]);

		if (!$resized_file_exists) {
			if (!is_wp_error($wp_editor)) {
				$wp_editor->set_quality($quality);

				// get crop parameters
				if (!empty($crop)) {
					$crop_parameters = self::process_focal_crop($meta_data, ($width * $ratio), ($height * $ratio), $crop);
					$wp_editor->crop(
						$crop_parameters['src_x'],
						$crop_parameters['src_y'],
						$crop_parameters['src_w'],
						$crop_parameters['src_h'],
						$crop_parameters['dest_w'],
						$crop_parameters['dest_h'],
						false
					);
				} else {
					/**
					 * Sometimes the width in the result doesn't match the ($width * $ratio)
					 * causing images to be re-scheduled on every load since they're not in the S3 storage object
					 */
					$wp_editor->resize($width * $ratio, $height * $ratio, false);
				}

				// Save & offload resized/cropped file
				$saved_file = $wp_editor->save($resize_path);

				if (!is_wp_error($saved_file)) {
					do_action('responsive_pics_file_saved_local', $id, $saved_file);

					if (class_exists('Amazon_S3_And_CloudFront')) {
						ResponsivePics()->s3offload->upload_image($id, $saved_file, round($width * $ratio), round($height * $ratio));
					}
				}
			} else {
				syslog(LOG_ERR, sprintf('error resizing image "%s"', $resize_path));
			}
		}
	}

	/**
	 * Process deleting all resized images
	 *
	 * @param int         $post_id     The attachment post ID
	 * @param object      $post        The attachment post object
	 *
	 * @return void
	 */
	public static function process_delete_attachment($post_id, $post) {
		$file            = get_attached_file($post_id);
		$meta            = wp_get_attachment_metadata($post_id);
		$meta_sizes      = isset($meta['sizes']) ? $meta['sizes'] : [];
		$meta_files      = array_column($meta_sizes, 'file');
		$upload_path     = wp_get_upload_dir();
		$upload_dir      = path_join($upload_path['basedir'], dirname($file));
		$file_parts      = pathinfo($file);
		$file_dir        = $file_parts['dirname'];
		$file_ext        = $file_parts['extension'];
		$file_name       = $file_parts['filename'];
		$resized_files   = glob($file_dir .'/'. $file_name .'-*.'. $file_ext);
		$files_to_delete = [];

		// Check if file has resize syntax and is not a wp image size
		if ($resized_files && is_array($resized_files)) {
			foreach ($resized_files as $resized_file) {
				// Test: https://regex101.com/r/DDSVXC/1
				$pattern = '/-([0-9]{1,}x[0-9]{1,})(-((left|center|right)-(top|center|bottom)|(crop-([0-9]{1,3})-([0-9]{1,3}))))?(@[0-9\.]{1,3}x)?.(jpe?g|png|gif|webp)$/i';

				// Matches syntax
				if (preg_match($pattern, $resized_file)) {
					$resized_file_parts = pathinfo($resized_file);
					$resized_file_name  = $resized_file_parts['basename'];

					// Not an wp image size file
					if (!in_array($resized_file_name, $meta_files)) {
						$files_to_delete[] = $resized_file;
						$deleted = wp_delete_file_from_directory($resized_file, $upload_dir);

						if (!$deleted) {
							syslog(LOG_DEBUG, 'not deleted?');
						} else {
							do_action('responsive_pics_file_deleted_local', $post_id, $resized_file);
						}
					}
				}
			}

			// Delete offloaded images
			if (class_exists('Amazon_S3_And_CloudFront')) {
				ResponsivePics()->s3offload->delete_image($post_id, $files_to_delete);
			}
		}
	}
}
