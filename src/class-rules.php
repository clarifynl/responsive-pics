<?php

class RP_Rules extends ResponsivePics {

	// this processes our resizing syntax with art direction support and returns a normalized array with resizing rules
	public function get_art_image_rules($input, $order = 'asc') {
		$variants = ResponsivePics()->breakpoints->add_missing_breakpoints(explode(',', $input));
		$result   = [];

		foreach ($variants as $variant) {
			$variant    = trim($variant);
			$crop       = false;
			$breakpoint = -1;
			$dimensions = [
				'width'  => -1,
				'height' => -1,
				'crop'   => false
			];

			// get dimensions
			if (ResponsivePics()->helpers->contains($variant, ':')) {
				$comp = explode(':', $variant);
				$bp   = trim($comp[0]); // xs
				$dm   = trim($comp[1]); // 400(/0.75|c)

				$breakpoint = ResponsivePics()->process->process_breakpoint($bp);
				if ($breakpoint !== false) {
					$dimensions = ResponsivePics()->process->process_dimensions($dm);
				} else {
					break;
				}
			// shorthand xs-12
			} elseif (ResponsivePics()->helpers->contains($variant, '-')) {
				$comp = explode('-', $variant);
				$bp   = trim($comp[0]);

				$breakpoint = ResponsivePics()->process->process_breakpoint($bp);
				if ($breakpoint !== false) {
					$dimensions = ResponsivePics()->process->process_dimensions($variant);
				} else {
					break;
				}

			} else {
				ResponsivePics()->error->add_error('invalid', 'size has neither breakpoint:width nor breakpoint-column syntax', $variant);
				break;
			}

			if (is_array($dimensions)) {
				$width      = $dimensions['width'];
				$height     = $dimensions['height'];
				$ratio      = $dimensions['ratio'];
				$crop       = $dimensions['crop'];
			}

			$result[] = [
				'breakpoint' => $breakpoint,
				'width'      => $width,
				'height'     => $height,
				'ratio'      => $ratio,
				'crop'       => $crop
			];
		}

		usort($result, ['RP_Breakpoints', ($order === 'desc') ? 'sort_by_breakpoint_reverse' : 'sort_by_breakpoint']);

		return $result;
	}

	// this processes our resizing syntax and returns a normalized array with resizing rules
	public function get_image_rules($input, $order = 'asc', $img_crop = null) {
		$variants = ResponsivePics()->breakpoints->add_missing_breakpoints(explode(',', $input));
		$result   = [];

		foreach ($variants as $variant) {
			$variant    = trim($variant);
			$ratio      = null;
			$crop       = false;
			$breakpoint = -1;
			$dimensions = [
				'width'  => -1,
				'height' => -1,
				'crop'   => false
			];

			// check for height and/or crops syntax
			if (ResponsivePics()->helpers->contains($variant, ' ') || ResponsivePics()->helpers->contains($variant, '|') || ResponsivePics()->helpers->contains($variant, '/')) {
				ResponsivePics()->error->add_error('invalid', sprintf('art directed parameters (height, factor, crop_x, crop_y) are not supported on image sizes: %s', $variant), $variant);
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

			if (is_array($dimensions)) {
				$width  = $dimensions['width'];
				$height = $dimensions['height'];
				$ratio  = $dimensions['ratio'];
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
				'ratio'      => $ratio,
				'crop'       => $crop
			];
		}

		usort($result, ['RP_Breakpoints', ($order === 'desc') ? 'sort_by_breakpoint_reverse' : 'sort_by_breakpoint']);

		return $result;
	}
}