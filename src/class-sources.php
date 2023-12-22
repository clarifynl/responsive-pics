<?php

class RP_Sources extends ResponsivePics
{
	/**
	 * Returns a normalized array of available sources
	 *
	 * @param int     $id           The wordpress attachment id
	 * @param array   $rules        The requested image resize rules
	 * @param string  $order        The order of the image sources
	 * @param uri     $rest_route   The original WP REST API request
	 *
	 * @return array  The image sources to process
	 */
	public function get_resize_sources($id, $rules = null, $order = 'desc', $rest_route = null) {
		$image_url       = wp_get_attachment_url($id);
		$image_path      = get_attached_file($id);
		$meta_data       = wp_get_attachment_metadata($id);
		$original_width  = $meta_data['width'];
		$original_height = $meta_data['height'];
		$sources         = [];
		$addedSource     = false;
		$min_breakpoint  = null;

		foreach ($rules as $rule) {
			$width  = $rule['width'];
			$height = $rule['height'];
			$factor = $rule['factor'];
			$crop   = $rule['crop'];

			// calculate height based on original aspect ratio
			if ($height === -1) {
				$height = floor($original_height / $original_width * $width);
			}

			// preserve aspect ratio when cropping
			if ($crop && $width > $original_width && $height > $original_height) {
				$org_ratio   = $original_width / $original_height;
				$rule_factor = $height / $width;

				// apply ratio on original width
				$height = $original_width * ($factor ? $factor : $rule_factor);
				$width  = $original_width;

				// check if original height is large enough for new factor height
				if ($height > $original_height) {
					$height = $original_height;
					$width  = $original_height / ($factor ? $factor : $rule_factor);
				}
			}

			// get resized url
			$resized_url = $this->get_resized_url($id, $image_path, $image_url, $width, $height, $crop, 1, $rest_route);
			if ($resized_url) {
				$size_2x_available = ($width * 2) < $original_width && ($height * 2) < $original_height;

				// check if retina url is possible
				$source1x = $resized_url;
				$source2x = $size_2x_available
					? $this->get_resized_url($id, $image_path, $image_url, $width, $height, $crop, 2, $rest_route)
					: null;

				// use the maximum possible image url when the retina width is too large for the current source being generated
				if (!$size_2x_available && $width < $original_width) {
					$ratio_max = round(($original_width / ($width * 2)), 1);

					// Only use retina version if ratio is at least 1,5 times as big as non-retina
					if ($ratio_max > 1.5) {
						$source2x  = $this->get_resized_url($id, $image_path, $image_url, $width, $height, $crop, $ratio_max, $rest_route);
					}
				}

				$breakpoint = $rule['breakpoint'];
				if ($breakpoint < $min_breakpoint || !isset($min_breakpoint)) {
					$min_breakpoint = $breakpoint;
				}

				$sources[] = [
					'breakpoint' => $breakpoint,
					'source1x'   => $source1x,
					'source2x'   => $source2x,
					'width'      => $width,
					'height'     => $height,
					'ratio'      => $width / $height
				];

				$addedSource = true;
			}
		}

		// add original source if no sources have been found so far
		if (!$addedSource) {
			$sources[] = [
				'source1x' => $image_url,
				'width'    => $original_width,
				'height'   => $original_height,
				'ratio'    => $original_width / $original_height
			];
		// add minimum breakpoint if it doesn't exist (otherwise there will be no image)
		} else if ($min_breakpoint != 0) {
			$minimum_breakpoint = [
				'breakpoint' => 0,
				'source1x'   => $image_url,
				'width'      => $original_width,
				'height'     => $original_height,
				'ratio'      => $original_width / $original_height
			];

			// set sources order
			if ($order === 'asc') {
				array_unshift($sources, $minimum_breakpoint);
			} elseif ($order === 'desc') {
				array_push($sources, $minimum_breakpoint);
			}
		}

		return $sources;
	}

	/**
	 * Creates a resized file if it doesn't exist yet
	 *
	 * @param int     $id           The wordpress attachment id
	 * @param string  $file_path    The wordpress attachment file path
	 * @param uri     $original_url The wordpress attachment original url
	 * @param int     $width        The requested image width
	 * @param int     $height       The requested image height
	 * @param array   $crop         The requested image crop positions
	 * @param float   $ratio        The requested image ratio
	 * @param uri     $rest_route   The original WP REST API request
	 *
	 * @return uri final resized image url
	 */
	public function get_resized_url($id, $file_path, $original_url, $width, $height, $crop, $ratio = 1, $rest_route = null) {
		$path_parts        = pathinfo($file_path);
		$suffix            = ResponsivePics()->helpers->get_resized_suffix($width, $height, $ratio, $crop);
		$resized_file_name = $path_parts['filename'] . '-' . $suffix . '.' . $path_parts['extension'];
		$resized_file_path = join(DIRECTORY_SEPARATOR, [$path_parts['dirname'], $resized_file_name]);
		$resized_url       = join(DIRECTORY_SEPARATOR, [dirname($original_url), basename($resized_file_path)]);
		$resize_request    = [
			'id'         => (int) $id,
			'quality'    => (int) self::$image_quality,
			'width'      => (int) $width,
			'height'     => (int) $height,
			'crop'       => $crop,
			'ratio'      => (int) $ratio,
			'path'       => (string) $resized_file_path,
			'rest_route' => (string) $rest_route
		];

		// Get legacy file path
		$suffix_legacy            = ResponsivePics()->helpers->get_resized_suffix_legacy($width, $height, $ratio, $crop);
		$resized_file_name_legacy = $path_parts['filename'] . '-' . $suffix_legacy . '.' . $path_parts['extension'];
		$resized_file_path_legacy = join(DIRECTORY_SEPARATOR, [$path_parts['dirname'], $path_parts['filename'] . '-' . $suffix_legacy . '.' . $path_parts['extension']]);
		$resized_url_legacy       = join(DIRECTORY_SEPARATOR, [dirname($original_url), basename($resized_file_path_legacy)]);

		// if image size does not exist yet as filename (or as legacy filename)
		$resized_file_exists = apply_filters('responsive_pics_file_exists', $id, [
			'path'   => $resized_file_path,
			'file'   => $resized_file_name,
			'width'  => $resize_request['width'],
			'height' => $resize_request['height'],
			'ratio'  => $resize_request['ratio']
		]);

		$resized_file_exists_legacy = apply_filters('responsive_pics_file_exists', $id, [
			'path'   => $resized_file_path_legacy,
			'file'   => $resized_file_name_legacy,
			'width'  => $resize_request['width'],
			'height' => $resize_request['height'],
			'ratio'  => $resize_request['ratio']
		]);

		if (!$resized_file_exists && !$resized_file_exists_legacy) {
			$is_pending = ResponsivePics()->helpers->is_scheduled_action($resize_request, $id);

			// if image size is not a pending request
			if (!$is_pending) {
				as_schedule_single_action(time(), 'process_resize_request', $resize_request, 'process_resize_request_' . $id);
				do_action('responsive_pics_request_scheduled', $id, $resize_request);
			}

			return;

		// new crop suffix
		} elseif ($resized_file_exists) {
			return esc_url($resized_url);

		// legacy crop suffix
		} elseif ($resized_file_exists_legacy) {
			return esc_url($resized_url_legacy);
		}
	}

	/**
	 * Check if a file exists in the filesystem.
	 *
	 * @param int    $id    The attachment ID.
	 * @param array  $file  The file array to check.
	 *
	 * @return bool
	 */
	public static function file_exists($id, $file) {
		return isset($file['path']) && file_exists($file['path']);
	}
}
