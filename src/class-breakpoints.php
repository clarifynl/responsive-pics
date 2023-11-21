<?php

class RP_Breakpoints extends ResponsivePics
{

	// add missing breakpoints
	public function add_missing_breakpoints($variants) {
		$result = [];
		$defined_breakpoints = [];
		$found_custom_breakpoints = false;

		// collect defined breakpoints into array
		foreach ($variants as $variant) {
			$breakpoint = $this->get_breakpoint_from_rule($variant);
			$breakpoint_name = null;

			if (isset($breakpoint)) {
				$breakpoint_name = $breakpoint['breakpoint'];
			}

			if (isset($breakpoint) && isset(self::$breakpoints[$breakpoint_name])) {
				$defined_breakpoints[$breakpoint_name] = $breakpoint;
			} else {
				$found_custom_breakpoints = true;
			}
		}

		// we've also found custom breakpoints, so cancel adding missing breakpoints
		if ($found_custom_breakpoints) {
			return $variants;
		}

		uasort($defined_breakpoints, ['RP_Breakpoints', 'sort_by_breakpoint_index']);

		// add missing smaller breakpoints with all columns
		foreach (self::$breakpoints as $breakpoint_key => $breakpoint_value) {
			if (!isset($defined_breakpoints[$breakpoint_key])) {
				// get first breakpoint and check if it should be fullwidth
				$first     = reset($defined_breakpoints);
				$columns   = ($first['columns'] === 'full') ? 'full' : self::$columns;
				$fullwidth = isset($first['fullwidth']) ? $first['fullwidth'] : false;
				$result[]  = $breakpoint_key . '-' . $columns . ($fullwidth ? '-full' : '');
			} else {
				break;
			}
		}

		// now loop again through original rules
		foreach ($variants as $variant) {
			$variant    = trim($variant);
			$breakpoint = $this->get_breakpoint_from_rule($variant);

			if (isset($breakpoint)) {
				$next_breakpoint = $this->get_next_breakpoint($breakpoint['breakpoint']);

				// add in-between breakpoints if we haven't defined them explicitly yet
				while ($next_breakpoint && !isset($defined_breakpoints[$next_breakpoint])) {
					if (isset(self::$grid_widths[$next_breakpoint])) {
						$result[] = str_replace($breakpoint['breakpoint'], $next_breakpoint, $variant);
					}

					$next_breakpoint = $this->get_next_breakpoint($next_breakpoint);
				}
			}

			$result[] = $variant;
		}

		return $result;
	}

	// get breakpoint name and number of columns
	public function get_breakpoint_from_rule($rule) {
		if (!ResponsivePics()->helpers->contains($rule, ':')) {
			if (ResponsivePics()->helpers->contains($rule, '-')) {
				$components = explode('-', $rule);
				$breakpoint = trim($components[0]);
				$columns    = trim($components[1]);
				$fullwidth  = isset($components[2]) && ResponsivePics()->helpers->contains($components[2], 'full');

				return [
					'breakpoint' => $breakpoint,
					'columns'    => $columns, // can be 'full'
					'fullwidth'  => $fullwidth // true/false
				];
			}
		}

		return null;
	}

	// returns next breakpoint in $breakpoints array
	public function get_next_breakpoint($key) {
		$previous_breakpoint = null;

		foreach (self::$breakpoints as $breakpoint_key => $breakpoint_value) {
			if ($previous_breakpoint === $key) {
				return $breakpoint_key;
			}

			$previous_breakpoint = $breakpoint_key;
		}

		return null;
	}

	// sort breakpoints from small to wide
	public static function sort_by_breakpoint($a, $b) {
		if ($a == $b) {
			return 0;
		}

		return $a['breakpoint'] < $b['breakpoint'] ? 1 : -1;
	}

	// sort breakpoints from wide to small
	public static function sort_by_breakpoint_reverse($a, $b) {
		if ($a == $b) {
			return 0;
		}

		return $b['breakpoint'] < $a['breakpoint'] ? 1 : -1;
	}

	// sort defined breakpoints from small to wide
	public static function sort_by_breakpoint_index($a, $b) {
		$index_a = array_search($a['breakpoint'], array_keys(self::$breakpoints));
		$index_b = array_search($b['breakpoint'], array_keys(self::$breakpoints));

		return $index_a > $index_b ? 1 : -1;
	}
}
