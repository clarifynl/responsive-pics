<?php

	/*
		Responsive Picture v0.4.6
		© 2018 Booreiland

		Responsive Picture is a Wordpress tool for resizing images on the fly.
		It uses a concise syntax for determining the image sizes you need in your template.
		You can define number of columns, aspect ratios and crop settings.
		It handles @2x images and missing breakpoints automatically.

		syntax    : ResponsivePicture::get[_background](id, 'breakpoint:width [/factor|height]|crop_x crop_y, …', 'class-name', lazyload, intrinsic);

		breakpoint: a number or a key in $breakpoints (e.g. "xs")
					if not defined, and width is a number, breakpoint will be the same as the width
					if not defined, and width is a column definition, breakpoint will be the corresponding breakpoint
					(e.g. if width is "xs-8", breakpoint will be "xs")
		width     : a number or a column definition
					a column definition is a key in $grid_widths plus a dash and a column span number (e.g. "xs-8")
					if column span number is "full", the full width of the next matching $breakpoint is used (e.g. "xs-full")
		height    : a number
		factor    : a factor of width
		crop_x    : t(op), r(ight), b(ottom), l(eft) or c(enter),
		crop_y    : t(op), r(ight), b(ottom), l(eft) or c(enter)
					if crop_y is not defined, crop_x will be treated as a shortcut:
					"c" = "center center", "t" = "top center", r = "right center", "b" = "center bottom", "l" = "left center"
		class-name: a class name to add to the html element
		lazyload  : (boolean, default: false) if true:
					- adds a 'lazyload' class to the picture img element
					- swaps the 'src' with 'data-src' attributes on the picture source elements
					- this will enable you to use a lazy loading plugin such as Lazysizes: https://github.com/aFarkas/lazysizes
		intrinsic : (boolean, default: false) if true:
					- adds an 'intrinsic' class to the picture element and a 'intrinsic__item' class to the picture img element
					- adds 'data-aspectratio' attributes on the picture source and img elements
					- this will enable you to pre-occupy the space needed for an image by calculating the height from the image width or the width from the height
					  with an intrinsic plugin such as the lazysizes aspectratio extension

		API

		ResponsivePicture::setColumns(number):    set number of grid columns
		ResponsivePicture::setGutter(pixels):     set grid gutter width
		ResponsivePicture::setGridWidths(array):  set grid widths for various breakpoints, example:
			[
				'xs' => 540,
				'sm' => 720,
				'md' => 960,
				'lg' => 1140,
				'xl' => 1140
			]
		ResponsivePicture::setBreakpoints(array): set breakpoints, example:
			[
				'xs' => 0,
				'sm' => 576,
				'md' => 768,
				'lg' => 992,
				'xl' => 1200
			]

		examples  : ResponsivePicture::get(1, 'xs-12, sm-6, md-4');
					ResponsivePicture::get(1, '400:200 300, 800:400 600', 'my-picture');
					ResponsivePicture::get(1, '400:200 200|c, 800:400 400|l t');
					ResponsivePicture::get(1, 'xs-full|c, sm-12/0.5|c, md-12/0.25|c');

					ResponsivePicture::get_background(1, 'xs:200 200|c, lg:400 400');


		Javascript dependencies:

					A responsive image polyfill such as Picturefill:
					http://scottjehl.github.io/picturefill/

					A lazy loader for images such as Lazysizes:
					https://github.com/aFarkas/lazysizes

					import 'picturefill';
					import 'lazysizes';
					import 'lazysizes/plugins/aspectratio/ls.aspectratio.js';

		TO DO'S:
		* If you want to resize and/or crop with a fixed heigth, but the width is not sufficient, it skips the resize alltogether.
		  Better would be if you can resize with only a fixed width or height and the 2nd dimension is calculated based upon original dimensions
		* Support for multiple background images
	*/



	class ResponsivePicture {

		private static $columns = self::setColumns();
		private static $gutter = self::setGutter();
		private static $grid_widths = self::setGridWidths();
		private static $breakpoints = self::setBreakpoints();

		// map short letters to valid crop values
		private static $crop_map = [
			'c' => 'center',
			't' => 'top',
			'r' => 'right',
			'b' => 'bottom',
			'l' => 'left'
		];

		// some handy shortcuts
		private static $crop_shortcuts = [
			'c' => 'c c',
			't' => 'c t',
			'r' => 'r c',
			'b' => 'c b',
			'l' => 'l c'
		];

		// only resizes images with the following mime types
		private static $supported_mime_types = [
			'image/jpeg',
			'image/png',
			'image/gif'
		];

		// keeps track of added image IDs
		private static $id_map = [];

		// print error message
		private static function show_error($message) {
			$error = sprintf('<pre>%s error: %s</pre>', get_class(), $message);

			echo $error;
		}

		// calculates column shortcut (e.g. "xs-5") to actual pixels
		private static function columns_to_pixels($input) {
			$components = explode('-', $input);

			$key = $components[0];
			$col = $components[1];

			if (self::contains($col, '/')) {
				$col = explode('/', $col)[0];
			}

			if ($col === 'full') {
				$next_breakpoint = self::get_next_breakpoint($key);

				// use max breakpoint if there's no bigger one left
				if (!isset($next_breakpoint)) {
					$next_breakpoint = $key;
				}

				$next_width = self::$breakpoints[$next_breakpoint];

				if (!isset($next_width)) {
					self::show_error(sprintf('no breakpoint set for "%s"', $key));
				}

				return $next_width;
			} else if (self::match($col, '/(\d+)/')) {
				if ($col < 1 || $col > self::$columns) {
					self::show_error(sprintf('number of columns should be between 1 and %s', self::$columns));
				}
			} else {
				self::show_error(sprintf('invalid columns: %s', $col));
			}

			$grid_width = self::$grid_widths[$key];

			if (!isset($grid_width)) {
				self::show_error(sprintf('no width found for breakpoint "%s"', $key));
			}

			$column_pixels = ($grid_width - (self::$columns - 1) * self::$gutter) / self::$columns;

			$pixels = floor($column_pixels * $col + self::$gutter * ($col - 1));

			return $pixels;
		}

		private static function get_next_breakpoint($key) {
			$previous_breakpoint = null;

			foreach (self::$breakpoints as $breakpoint_key => $breakpoint_value) {
				if ($previous_breakpoint == $key) {
					return $breakpoint_key;
				}

				$previous_breakpoint = $breakpoint_key;
			}

			return null;
		}

		// returns true if a string contains a substring
		private static function contains($haystack, $needle) {
			return strpos($haystack, $needle) !== false;
		}

		// returns regex match of string
		private static function match($string, $regex) {
			$found = preg_match($regex, $string, $matches);

			return $found ? $matches[0] : null;
		}

		// breakpoint can be shortcut (e.g. "xs") or number
		private static function process_breakpoint($input) {
			$input = trim($input);

			if (isset(self::$breakpoints[$input])) {
				return self::$breakpoints[$input];
			} else {
				return $input;
			}

			return false;
		}

		// dimensions can be shortcut (e.g. "xs-5"), width (e.g. "400") or width and height (e.g. "400 300");
		private static function process_dimensions($input) {
			$dimensions = trim($input);
			$width = -1;
			$height = -1;

			if (self::contains($dimensions, '-')) {
				$width = self::columns_to_pixels($dimensions);
			} else {
				if (self::contains($dimensions, ' ')) {
					// width and height supplied
					$wh = explode(' ', $dimensions);
					$width = trim($wh[0]);
					$height = trim($wh[1]);
				} else {
					// height will be calculated based on width
					$width = self::match($dimensions, '/(\d+)/');

					if (!isset($width)) {
						self::show_error('width undefined: ' . $dimensions);
					}
				}
			}

			if (self::contains($dimensions, '/')) {
				// height is a specified factor of weight
				$wh = explode('/', $dimensions);
				$height = $width * trim($wh[1]);
			}

			return [
				'input'  => $input,
				'width'  => $width,
				'height' => $height
			];
		}

		// crop values can be single shortcut values (e.g. "c") or two dimensional values (e.g. "l t");
		private static function process_crop($input) {
			$shortcuts = explode(' ', trim($input));

			if (sizeof($shortcuts) === 1) {
				$shortcuts = self::$crop_shortcuts[$shortcuts[0]];
				$shortcuts = explode(' ', trim($shortcuts));
			}

			$result = [];

			foreach($shortcuts as $key => $value) {
				$result[] = self::$crop_map[$value];
			}

			return $result;
		}

		// get breakpoint name and number of columns
		private static function get_breakpoint_from_rule($rule) {
			if (!self::contains($rule, ':')) {
				if (self::contains($rule, '-')) {
					$components = explode('-', $rule);

					return [
						'breakpoint' => trim($components[0]),
						'columns'    => trim($components[1]) // can be 'full'
					];
				}
			}

			return null;
		}

		// sort defined breakpoints from small to wide
		private static function sort_by_breakpoint_index($a, $b) {
			$index_a = array_search($a['breakpoint'], array_keys(self::$breakpoints));
			$index_b = array_search($b['breakpoint'], array_keys(self::$breakpoints));

			return $index_a > $index_b;
		}

		// add missing breakpoints
		private static function add_missing_breakpoints($variants) {
			$result = [];
			$defined_breakpoints = [];
			$found_custom_breakpoints = false;

			// collect defined breakpoints into array
			foreach ($variants as $variant) {
				$breakpoint = self::get_breakpoint_from_rule($variant);
				$breakpoint_name = $breakpoint['breakpoint'];

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

			uasort($defined_breakpoints, ['self', 'sort_by_breakpoint_index']);

			// add missing smaller breakpoints
			foreach (self::$breakpoints as $breakpoint_key => $breakpoint_value) {
				if (!isset($defined_breakpoints[$breakpoint_key])) {
					$result[] = $breakpoint_key . '-12';
				} else {
					break;
				}
			}

			// now loop again through original rules
			foreach ($variants as $variant) {
				$breakpoint = self::get_breakpoint_from_rule($variant);

				if (isset($breakpoint)) {
					$next_breakpoint = self::get_next_breakpoint($breakpoint['breakpoint']);
					$columns = $breakpoint['columns'];

					if (self::contains($columns, '/')) {
						$columns = explode('/', $columns)[0];
					}

					$columns = trim($columns);

					// add in-between breakpoints if we haven't defined them explicitly yet
					while ($next_breakpoint && !isset($defined_breakpoints[$next_breakpoint])) {
						if ($columns == 'full' || ($columns !== 'full' && isset(self::$grid_widths[$next_breakpoint]))) {
							$result[] = sprintf('%s-%s', $next_breakpoint, $breakpoint['columns']);
						}

						$next_breakpoint = self::get_next_breakpoint($next_breakpoint);
					}
				}

				$result[] = $variant;
			}

			return $result;
		}

		// this processes our resizing syntax and returns a normalized array with resizing rules
		private static function get_image_rules($input, $reverse = false) {
			$variants = self::add_missing_breakpoints(explode(',', $input));
			$result = [];

			foreach ($variants as $variant) {

				$crop = false;
				$breakpoint = -1;
				$dimensions = [
					'width'  => -1,
					'height' => -1
				];

				if (self::contains($variant, '|')) {
					$components = explode('|', $variant);
					$variant = trim($components[0]);
					$crop = self::process_crop($components[1]);
				}

				if (self::contains($variant, ':')) {
					$components = explode(':', $variant);
					$breakpoint = self::process_breakpoint($components[0]);
					$dimensions = self::process_dimensions($components[1]);
				} else {
					$dimensions = self::process_dimensions($variant);
				}

				$width = $dimensions['width'];
				$height = $dimensions['height'];

				if ($breakpoint === -1) {
					if (self::contains($dimensions['input'], '-')) {
						// use breakpoint based on defined column size
						$components = explode('-', $dimensions['input']);
						$bp = trim($components[0]);
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
					'crop'       => $crop
				];
			}

			usort($result, ['self', $reverse ? 'sort_by_breakpoint_reverse' : 'sort_by_breakpoint']);

			return $result;
		}

		// sort breakpoints from small to wide
		private static function sort_by_breakpoint($a, $b) {
			return $a['breakpoint'] < $b['breakpoint'];
		}

		// sort breakpoints from wide to small
		private static function sort_by_breakpoint_reverse($a, $b) {
			return $b['breakpoint'] < $a['breakpoint'];
		}

		// creates a resized file if it doesn't exist and returns the final image url
		private static function get_resized_url($file_path, $original_url, $width, $height, $crop, $ratio = 1) {
			if ($ratio === 1) {
				$ratio_indicator = '';
			} else {
				$ratio_indicator = '@' . $ratio . 'x';
			}

			if ($crop === false) {
				$crop_indicator = '';
			} else {
				$crop_indicator = '-' . implode('-', $crop);
			}

			// note: actual dimensions can be different from the dimensions appended to the filename, but we don't know those before we actually resize
			$suffix = sprintf('%sx%s%s%s', round($width), round($height), $crop_indicator, $ratio_indicator);
			$path_parts = pathinfo($file_path);
			$resized_file_path = join(DIRECTORY_SEPARATOR, [$path_parts['dirname'], $path_parts['filename'] . '-' . $suffix . '.' . $path_parts['extension']]);

			if (!file_exists($resized_file_path)) {
				// resize the image
				$editor = wp_get_image_editor($file_path);

				if (!is_wp_error($editor)) {
					$editor->set_quality(95);
					$editor->resize($width * $ratio, $height * $ratio, $crop);
					$editor->save($resized_file_path);
				} else {
					self::show_error(sprintf('error creating picture "%s"', $resized_file_path));
				}
			}

			$resized_url = join(DIRECTORY_SEPARATOR, [dirname($original_url), basename($resized_file_path)]);

			return $resized_url;
		}

		// check if png file has transparent background
		private static function is_alpha_png($fn) {
			return (ord(@file_get_contents($fn, NULL, NULL, 25, 1)) == 6);
		}

		// returns a normalized definition of breakpoints
		private static function get_definition($id, $sizes, $reverse = false) {
			$url       = wp_get_attachment_url($id);
			$file_path = get_attached_file($id);

			if (!$file_path) {
				self::show_error(sprintf('file does not exist for id %s', $id));
			}

			if (!$url) {
				self::show_error(sprintf('url does not exist for id %s', $id));
			}

			$mime_type = get_post_mime_type($id);
			$alt       = get_post_meta($id, '_wp_attachment_image_alt', true);
			$alpha     = false;

			// check if png has alpha channel
			if ($mime_type == 'image/png') {
				$alpha = self::is_alpha_png($file_path);
			}

			// unsupported mime-type, return original source without breakpoints
			if (!in_array($mime_type, self::$supported_mime_types)) {
				return [
					'sources' => [[
						'source1x' => $url,
						'ratio'    => 1
					]],
					'mimetype' => $mime_type,
					'alt'      => $alt
				];
			}

			$meta_data       = wp_get_attachment_metadata($id);
			$original_width  = $meta_data['width'];
			$original_height = $meta_data['height'];
			$rules           = self::get_image_rules($sizes, $reverse);
			$sources         = [];

			$addedSource     = false;
			$min_breakpoint  = null;

			if (!$original_width || !$original_height) {
				self::show_error(sprintf('no dimensions for file id %s', $id));
			}

			foreach ($rules as $rule) {
				$width  = $rule['width'];
				$height = $rule['height'];
				$crop   = $rule['crop'];

				if ($height === -1) {
					// calculate height based on original aspect ratio
					$height = floor($original_height / $original_width * $width);
				}

				if ($width < $original_width && $height < $original_height) {
					// we can safely resize
					$resized_url = self::get_resized_url($file_path, $url, $width, $height, $crop);
					$source1x = $resized_url;
					$source2x = null;

					if ($width * 2 < $original_width && $height * 2 < $original_height) {
						// we can also resize for @2x
						$resized_url = self::get_resized_url($file_path, $url, $width, $height, $crop, 2);
						$source2x = $resized_url;
					}

					$breakpoint = $rule['breakpoint'];

					if ($breakpoint < $min_breakpoint || !isset($min_breakpoint)) {
						$min_breakpoint = $breakpoint;
					}

					$sources[] = [
						'breakpoint' => $breakpoint,
						'source1x'   => $source1x,
						'source2x'   => $source2x,
						'ratio'      => $width / $height
					];

					$addedSource = true;

				} else {
					// Use original image to resize
					$source1x    = $url;
					$source2x    = null;
					$breakpoint  = $rule['breakpoint'];

					if ($breakpoint < $min_breakpoint || !isset($min_breakpoint)) {
						$min_breakpoint = $breakpoint;
					}

					$sources[] = [
						'breakpoint' => $breakpoint,
						'source1x'   => $source1x,
						'source2x'   => $source2x,
						'ratio'      => $original_width / $original_height
					];

					$addedSource = true;

					break;
				}
			}

			if (!$addedSource) {
				// add original source if no sources have been found so far
				$sources[] = [
					'source1x' => $url,
					'ratio'    => $original_width / $original_height
				];
			} else if ($min_breakpoint != 0) {
				// add minimum breakpoint if it doesn't exist (otherwise there will be no image)
				$minimum_breakpoint = [
					'breakpoint' => 0,
					'source1x'   => $url,
					'ratio'      => $original_width / $original_height
				];

				if ($reverse) {
					array_unshift($sources, $minimum_breakpoint);
				} else {
					array_push($sources, $minimum_breakpoint);
				}
			}

			return [
				'sources'  => $sources,
				'alt'      => $alt,
				'mimetype' => $mime_type,
				'alpha'    => $alpha
			];
		}

		// get a css rule for targeting high dpi screens
		private static function get_media_query_2x($breakpoint) {
			// apparently this targets high dpi screens cross-browser
			return sprintf('@media only screen and (-webkit-min-device-pixel-ratio: 2) and (min-width: %spx), only screen and (min-resolution: 192dpi) and (min-width: %spx)', $breakpoint, $breakpoint);
		}



		/**************
		 *            *
		 *   PUBLIC   *
		 *            *
		 **************/



		// set number of grid columns
		public static setColumns($value = 12) {
			self::$columns = $value;
		}

		// set grid gutter width, in pixels
		public static setGutter($value = 20) {
			self::$gutter = $value;
		}

		// breakpoints used for "media(min-width: x)" in picture element, in pixels
		public static setBreakpoints($value = [
			'xs'    => 0,
			'sm'    => 576,
			'md'    => 768,
			'lg'    => 992,
			'xl'    => 1200,
			'xxl'   => 1400,
			'xxxl'  => 1600
		]) {
			self::$breakpoints = $value;
		}

		// grid system should match the container widths in css
		public static setGridWidths($value = [
			'xs'    => 540,
			'sm'    => 720,
			'md'    => 960,
			'lg'    => 1140,
			'xl'    => 1140,
			'xxl'   => 1140,
			'xxxl'  => 1140
		]) {
			self::$grid_widths = $value;
		}

		/*
		 * Construct a background image element and a matching responsive inline style element
		 *
		 * Returns an inline <style> element with a dedicated image class with media-queries for all the different image sizes
		 * and an div with the same dedicated image class
		 */
		public static function get_background($id, $sizes, $class = '') {
			if (!isset($id)) {
				return 'image id undefined';
			}

			// Check for multiple background images
			if (is_array($id)) {
				// temp solution
				$definition = self::get_definition($id[0], $sizes, true);
			} else {
				$definition = self::get_definition($id, $sizes, true);
			}

			if (!$definition) {
				return 'no image found with id ' . $id;
			}

			$sources = $definition['sources'];

			$copy = $id;

			// prevent same id, append copy number to existing
			if (isset(self::$id_map[$id])) {
				self::$id_map[$id]++;
				$copy .= '-' . self::$id_map[$id];
			} else {
				self::$id_map[$id] = 0;
			}

			$id = sprintf('responsive-picture-background-%s', $copy);

			$background = [];
			$background[] = '<style scoped="scoped" type="text/css">';

			// add all sources as background-images
			foreach ($sources as $source) {
				if (isset($source['breakpoint'])) {
					$sources = $source['source1x'];

					$background[] = sprintf('  @media (min-width: %spx) {', $source['breakpoint']);
					$background[] = sprintf('  #%s {background-image: url("%s");}', $id, $source['source1x']);
					$background[] = '  }';

					if (isset($source['source2x'])) {
						$background[] = sprintf('  %s {', self::get_media_query_2x($source['breakpoint']));
						$background[] = sprintf('  #%s {background-image: url("%s");}', $id, $source['source2x']);
						$background[] = '  }';
					}
				} else {
					$background[] = sprintf('  #%s {background-image: url("%s");}', $id, $source['source1x']);
				}
			}

			$background[] = '  }';
			$background[] = '</style>';
			$background[] = sprintf('<div%s id="%s"></div>', $class ? ' class="' . $class . '"' : '', $id);

			return implode("\n", $background) . "\n";
		}

		/*
		 * Construct a responsive picture element
		 * returns <picture> element as html markup
		 */
		public static function get($id, $sizes, $picture_classes = null, $lazyload = false, $intrinsic = false) {
			if (!isset($id)) {
				return 'image id undefined';
			}

			$definition  = self::get_definition($id, $sizes);

			if (!$definition) {
				return 'no image found with id ' . $id;
			}

			$sources     = $definition['sources'];
			$picture     = [];

			// convert $picture_classes to array if it is a string
			if (!is_array($picture_classes) && !empty($picture_classes)) {
				$picture_classes = preg_split('/[\s,]+/', $picture_classes);
			}

			$img_classes   = [];

			// lazyload option
			if ($lazyload) {
				$img_classes[] = 'lazyload';
			}

			// exclude unsupported mime types from intrinsic
			if ($intrinsic && !in_array($definition['mimetype'], self::$supported_mime_types)) {
				$intrinsic = false;
			}

			// set intrinsic classes
			if ($intrinsic) {
				$picture_classes[] = 'intrinsic';
				$img_classes[]     = 'intrinsic__item';

				if ($definition['alpha']) {
					$img_classes[] = 'has-alpha';
				}
			}

			$picture[] = sprintf('<picture%s>', $picture_classes ? ' class="' . implode(' ', $picture_classes) . '"' : '');

			$src_attribute = $lazyload ? 'data-srcset' : 'srcset';
			$classes = $img_classes ? ' class="' . implode(' ', $img_classes) . '"' : '';

			// add all sources
			foreach ($sources as $source) {
				$data_aspectratio = $intrinsic ? ' data-aspectratio="' . $source['ratio'] . '"' : '';

				if (isset($source['breakpoint'])) {
					$urls = $source['source1x'];

					if (isset($source['source2x'])) {
						$urls .= ' 1x, ' . $source['source2x'] . ' 2x';
					}

					$picture[] = sprintf('  <source media="(min-width: %spx)" %s="%s"%s />', $source['breakpoint'], $src_attribute, $urls, $data_aspectratio);
				} else {
					$picture[] = sprintf('  <source %s="%s"%s />', $src_attribute, $source['source1x'], $data_aspectratio);
				}
			}

			// transparent gif
			$ratio     = $intrinsic ? ' data-aspectratio=""' : '';
			$picture[] = sprintf('  <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"%s alt="%s"%s />', $ratio, $definition['alt'], $classes);
			$picture[] = '</picture>';

			return implode("\n", $picture) . "\n";
		}
	}
?>