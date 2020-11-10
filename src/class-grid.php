<?php

class RP_Grid extends ResponsivePics {

	// calculates column shortcut (e.g. "xs-5") to actual pixels
	public function columns_to_pixels($input) {
		$comp = explode('-', $input, 2);
		$key  = $comp[0];
		$col  = $comp[1];

		// convert 'xs-full' shorthand to 'xs-12-full'
		if ($col === 'full') {
			$col = self::$columns . '-full';
		}

		// check breakpoint
		$breakpoint = ResponsivePics()->process->process_breakpoint($key);
		if (is_numeric($breakpoint)) {
			// strip of ratio
			if (ResponsivePics()->helpers->contains($col, '/')) {
				$comp = explode('/', $col);
				$col  = trim($comp[0]);
			}

			// strip of crop
			if (ResponsivePics()->helpers->contains($col, '|')) {
				$comp = explode('|', $col);
				$col  = trim($comp[0]);
			}

			// check for full-width syntax (eg. md-6-full)
			if (ResponsivePics()->helpers->match($col, '/-full$/')) {
				$next_breakpoint = ResponsivePics()->breakpoints->get_next_breakpoint($key);

				// use max breakpoint if there's no bigger one left
				if (!isset($next_breakpoint)) {
					$next_breakpoint = $key;
				}

				// get full-width columns
				$comp = explode('-', $col, 2);
				$col  = $comp[0];

				// check if col is valid
				if (ResponsivePics()->helpers->match($col, '/(\d+)/')) {
					if ($col < 1 || $col > self::$columns) {
						ResponsivePics()->error->add_error('invalid', sprintf('number of columns %d should be between 1 and %s', $col, self::$columns), $col);
					}
				}

				// get column size from next breakpoint width
				$grid_width = self::$breakpoints[$next_breakpoint];
				if (!isset($grid_width)) {
					ResponsivePics()->error->add_error('missing', sprintf('no breakpoint set for %s', $breakpoint), self::$breakpoints);
				}

			// normal column syntax (eg. md-6)
			} else if (ResponsivePics()->helpers->match($col, '/(\d+)/')) {
				if ($col < 1 || $col > self::$columns) {
					ResponsivePics()->error->add_error('invalid', sprintf('number of columns %d should be between 1 and %s', $col, self::$columns), $col);
				} else {
					// get column size from grid width
					$grid_width = self::$grid_widths[$key];
					if (!isset($grid_width)) {
						ResponsivePics()->error->add_error('missing', sprintf('no width found for breakpoint %s', $breakpoint), self::$grid_widths);
					}
				}
			} else {
				$grid_width = -1;
				ResponsivePics()->error->add_error('invalid', sprintf('invalid columns: %s', $col), $col);
			}

			// calculate column width
			$column_pixels = ($grid_width - (self::$columns * self::$gutter)) / self::$columns;
			$pixels        = floor(($column_pixels * $col) + (self::$gutter * ($col - 1)));

			return $pixels;
		}
	}
}