<?php

class RP_Sources extends ResponsivePics {

	// returns a normalized array of available sources
	public function get_resize_sources($id, $rules = null, $order = 'desc') {
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
			$resized_url = $this->get_resized_url($id, $image_path, $image_url, $width, $height, $crop);
			if ($resized_url) {
				$size_2x_available = $width * 2 < $original_width && $height * 2 < $original_height;

				$source1x = $resized_url;
				$source2x = $size_2x_available
					? $this->get_resized_url($id, $image_path, $image_url, $width, $height, $crop, 2)
					: null;

				// Use the original image url when it is larger than the current source being generated
				if (!$source2x && $width < $original_width) {
					$source2x = $image_url;
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

	// creates a resized file if it doesn't exist and returns the final image url
	public function get_resized_url($id, $file_path, $original_url, $width, $height, $crop, $ratio = 1) {
		$path_parts        = pathinfo($file_path);
		$suffix            = ResponsivePics()->helpers->get_resized_suffix($width, $height, $ratio, $crop);
		$resized_file_path = join(DIRECTORY_SEPARATOR, [$path_parts['dirname'], $path_parts['filename'] . '-' . $suffix . '.' . $path_parts['extension']]);
		$resized_url       = join(DIRECTORY_SEPARATOR, [dirname($original_url), basename($resized_file_path)]);
		$resize_request    = [
			'id'      => (int) $id,
			'quality' => (int) self::$image_quality,
			'width'   => (int) $width,
			'height'  => (int) $height,
			'crop'    => $crop,
			'ratio'   => (int) $ratio,
			'path'    => (string) $resized_file_path
		];

		// Get legacy file path
		$suffix_legacy            = ResponsivePics()->helpers->get_resized_suffix_legacy($width, $height, $ratio, $crop);
		$resized_file_path_legacy = join(DIRECTORY_SEPARATOR, [$path_parts['dirname'], $path_parts['filename'] . '-' . $suffix_legacy . '.' . $path_parts['extension']]);
		$resized_url_legacy       = join(DIRECTORY_SEPARATOR, [dirname($original_url), basename($resized_file_path_legacy)]);

		// if image size does not exist yet as filename (or as legacy filename)
		if (!file_exists($resized_file_path) &&
			!file_exists($resized_file_path_legacy)) {
			$is_pending = ResponsivePics()->helpers->is_scheduled_action($resize_request, $id);

			// if image size is not a pending request
			if (!$is_pending) {
				as_schedule_single_action(time(), 'process_resize_request', $resize_request, 'process_resize_request_' . $id);
			}

			return;
		// new crop suffix
		} elseif (file_exists($resized_file_path)) {
			return $resized_url;
		// legacy crop suffix
		} elseif (file_exists($resized_file_path_legacy)) {
			return $resized_url_legacy;
		}
	}
}
