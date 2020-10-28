<?php

class RP_Rules extends ResponsivePics {

	// this processes our resizing syntax with art direction support and returns a normalized array with resizing rules
	public function get_image_rules($input, $order = 'desc', $art_direction = true, $img_crop = null) {
		$variants = ResponsivePics()->breakpoints->add_missing_breakpoints(explode(',', $input));
		$result   = [];

		// get image crop & ratio
		$global_crop = null;
		if (!$art_direction && $img_crop) {
			$global_crop = ResponsivePics()->process->process_ratio_crop($img_crop);
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
				ResponsivePics()->error->add_error('invalid', sprintf('size %s has neither breakpoint:width nor breakpoint-column syntax', (string) $variant), $variant);
				break;
			}

			if (is_array($dimensions)) {
				$width      = $dimensions['width'];
				$height     = $dimensions['height'];
				$ratio      = $dimensions['ratio'];
				$crop       = $dimensions['crop'];
			}

			if (!$art_direction && $global_crop) {
				$ratio = $global_crop['ratio'];
				$crop  = $global_crop['crop'];
			}

			$result[] = [
				'breakpoint' => $breakpoint,
				'width'      => $width,
				'height'     => $height,
				'ratio'      => $ratio,
				'crop'       => $crop
			];
		}

		usort($result, ['RP_Breakpoints', ($order === 'asc') ? 'sort_by_breakpoint_reverse' : 'sort_by_breakpoint']);

		return $result;
	}
}