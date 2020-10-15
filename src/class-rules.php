<?php

class RP_Rules extends ResponsivePics {

	// this processes our resizing syntax with art direction support and returns a normalized array with resizing rules
	public function get_art_image_rules($input, $reverse = false) {
		$variants = ResponsivePics()->breakpoints->add_missing_breakpoints(explode(',', $input));
		$result   = [];

		foreach ($variants as $variant) {
			$crop       = false;
			$breakpoint = -1;
			$dimensions = [
				'width'  => -1,
				'height' => -1,
				'crop'   => false
			];

			// get crop positions
			if (ResponsivePics()->helpers->contains($variant, '|')) {
				$components = explode('|', $variant);
				$variant    = trim($components[0]);
				$crop       = ResponsivePics()->process->process_crop($components[1]);
			}

			// get dimensions
			if (ResponsivePics()->helpers->contains($variant, ':')) {
				$components = explode(':', $variant);
				$breakpoint = ResponsivePics()->process->process_breakpoint($components[0]);
				$dimensions = ResponsivePics()->process->process_dimensions($components[1]);
			} else {
				$dimensions = ResponsivePics()->process->process_dimensions($variant);
			}

			// check for errors
			if (is_wp_error($dimensions)) {
				return $dimensions;
			} elseif (is_array($dimensions)) {
				$width      = $dimensions['width'];
				$height     = $dimensions['height'];
				$crop_ratio = $dimensions['crop_ratio'];
			}

			if ($breakpoint === -1) {
				if (ResponsivePics()->helpers->contains($dimensions['input'], '-')) {
					// use breakpoint based on defined column size
					$components = explode('-', $dimensions['input']);
					$bp         = trim($components[0]);
					$breakpoint = self::$breakpoints[$bp];
				} else {
					// use breakpoint based on width
					$breakpoint = $width;
				}
			}

			$result[] = [
				'breakpoint' => $breakpoint,
				'width'      => $width,
				'height'     => $height,
				'crop_ratio' => $crop_ratio,
				'crop'       => $crop
			];
		}

		usort($result, ['RP_Breakpoints', $reverse ? 'sort_by_breakpoint_reverse' : 'sort_by_breakpoint']);

		return $result;
	}

	// this processes our resizing syntax and returns a normalized array with resizing rules
	public function get_image_rules($input, $reverse = false, $img_crop = null) {
		$variants = ResponsivePics()->breakpoints->add_missing_breakpoints(explode(',', $input));
		$result   = [];

		foreach ($variants as $variant) {
			$crop       = false;
			$crop_ratio = null;
			$breakpoint = -1;
			$dimensions = [
				'width'  => -1,
				'height' => -1,
				'crop'   => false
			];

			// check for height and/or crops syntax
			$variant = trim($variant);
			if (ResponsivePics()->helpers->contains($variant, ' ') || ResponsivePics()->helpers->contains($variant, '|') || ResponsivePics()->helpers->contains($variant, '/')) {
				return ResponsivePics()->error->add_error('invalid', sprintf('art directed parameters (height, factor, crop_x, crop_y) are not supported on image sizes: %s', $variant), $variant);
			}

			// get global img crop positions
			if ($img_crop) {
				// check if crop position is set
				if (ResponsivePics()->helpers->contains($img_crop, '|')) {
					$components = explode('|', $img_crop);
					$ratio      = trim($components[0]);
					$crop_pos   = $components[1];

					// check if ratio is within range
					if (ResponsivePics()->process->process_ratio($ratio)) {
						$crop     = ResponsivePics()->process->process_crop($crop_pos);
						$variant .= '/'. $ratio;
					}
				// add default crop position
				} else {
					$ratio        = $img_crop;
					$default_crop = 'c';

					// check if ratio is within range
					if (ResponsivePics()->process->process_ratio($ratio)) {
						$crop     = ResponsivePics()->process->process_crop($default_crop);
						$variant .= '/'. $ratio;
					}
				}
			}

			// get dimensions
			if (ResponsivePics()->helpers->contains($variant, ':')) {
				$components = explode(':', $variant);
				$breakpoint = ResponsivePics()->process->process_breakpoint($components[0]);
				$dimensions = ResponsivePics()->process->process_dimensions($components[1]);
			} else {
				$dimensions = ResponsivePics()->process->process_dimensions($variant);
			}

			// check for errors
			if (is_wp_error($dimensions)) {
				return $dimensions;
			} elseif (is_array($dimensions)) {
				$width      = $dimensions['width'];
				$height     = $dimensions['height'];
				$crop_ratio = $dimensions['crop_ratio'];
			}

			if ($breakpoint === -1) {
				if (ResponsivePics()->helpers->contains($dimensions['input'], '-')) {
					// use breakpoint based on defined column size
					$components = explode('-', $dimensions['input']);
					$bp         = trim($components[0]);
					$breakpoint = self::$breakpoints[$bp];
				} else {
					// use breakpoint based on width
					$breakpoint = $width;
				}
			}

			$result[] = [
				'breakpoint' => $breakpoint,
				'width'      => $width,
				'height'     => $height,
				'crop_ratio' => $crop_ratio,
				'crop'       => $crop
			];
		}

		usort($result, ['RP_Breakpoints', $reverse ? 'sort_by_breakpoint_reverse' : 'sort_by_breakpoint']);

		return $result;
	}
}
