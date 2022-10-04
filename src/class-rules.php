<?php

class RP_Rules extends ResponsivePics
{

	// this processes our resizing syntax with art direction support and returns a normalized array with resizing rules
	public function get_image_rules($input, $order = 'desc', $art_direction = true, $img_crop = null, $focal_point = ['x' => 50, 'y' => 50]) {
		$variants = ResponsivePics()->breakpoints->add_missing_breakpoints(explode(',', $input));
		$result   = [];

		// get image crop & ratio
		$global_crop = null;
		if (!$art_direction && $img_crop) {
			$global_crop = ResponsivePics()->process->process_factor_crop($img_crop, $focal_point);
		}

		foreach ($variants as $variant) {
			$variant    = trim($variant);
			$crop       = false;
			$breakpoint = -1;
			$dimensions = [
				'width'  => -1,
				'height' => -1,
				'crop'   => false
			];

			// check for factor and/or crops syntax in non-art directed elements
			if (!$art_direction && ResponsivePics()->helpers->is_art_directed($variant)) {
				ResponsivePics()->error->add_error('invalid', sprintf('art directed parameters (height, factor, crop_x, crop_y) are not supported on image sizes: %s', $variant), $variant);
				break;
			}

			// get dimensions
			if (ResponsivePics()->helpers->contains($variant, ':')) {
				$comp = explode(':', $variant);
				$bp   = trim($comp[0]);
				$dm   = trim($comp[1]);

				$breakpoint = ResponsivePics()->process->process_breakpoint($bp);
				if ($breakpoint !== false) {
					$dimensions = ResponsivePics()->process->process_dimensions($dm, $focal_point);
				} else {
					break;
				}
			// shorthand xs-12 / xs-12-full
			} elseif (ResponsivePics()->helpers->contains($variant, '-')) {
				$comp = explode('-', $variant, 2);
				$bp   = trim($comp[0]);

				$breakpoint = ResponsivePics()->process->process_breakpoint($bp);
				if ($breakpoint !== false) {
					$dimensions = ResponsivePics()->process->process_dimensions($variant, $focal_point);
				} else {
					break;
				}

			} else {
				ResponsivePics()->error->add_error('invalid', sprintf('size %s has neither breakpoint:width nor breakpoint-column syntax', (string) $variant), $variant);
				break;
			}

			if (is_array($dimensions)) {
				$width  = $dimensions['width'];
				$height = $dimensions['height'];
				$factor = $dimensions['factor'];
				$crop   = $dimensions['crop'];
			}

			if (!$art_direction && $global_crop) {
				$factor = $global_crop['factor'];
				$crop   = $global_crop['crop'];
				$height = (int) round($width * $factor);
			}

			$result[] = [
				'breakpoint' => $breakpoint,
				'width'      => $width,
				'height'     => $height,
				'factor'     => $factor,
				'crop'       => $crop
			];
		}

		usort($result, ['RP_Breakpoints', ($order === 'asc') ? 'sort_by_breakpoint_reverse' : 'sort_by_breakpoint']);

		return $result;
	}
}