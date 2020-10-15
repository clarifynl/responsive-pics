<?php


class ResponsivePics {
	public static $columns = null;
	public static $gutter = null;
	public static $grid_widths = null;
	public static $breakpoints = null;
	public static $lazyload_class = null;
	public static $image_quality = null;

	// map short letters to valid crop values
	public static $crop_map = [
		'c' => 'center',
		't' => 'top',
		'r' => 'right',
		'b' => 'bottom',
		'l' => 'left'
	];

	// some handy shortcuts
	public static $crop_shortcuts = [
		'c' => 'c c',
		't' => 'c t',
		'r' => 'r c',
		'b' => 'c b',
		'l' => 'l c'
	];

	// only resizes images with the following mime types
	public static $supported_mime_types = [
		'image/jpeg',
		'image/png',
		'image/gif'
	];

	// keeps track of added image IDs
	public static $id_map = [];

	// construct
	public function __construct() {
		add_action('plugins_loaded', ['ResponsivePics', 'init']);
	}

	// init
	public static function init() {
		$includes = [
			'class-rest-api',
			'class-definitions',
			'class-rules',
			'class-breakpoints',
			'class-process',
			'class-helpers',
			'class-error'
		];

		foreach ($includes as $inc) {
			include (RESPONSIVE_PICS_DIR . '/src/'. $inc .'.php');
		}

		// Set defaults
		self::setColumns();
		self::setGutter();
		self::setGridWidths();
		self::setBreakpoints();
		self::setLazyLoadClass();
		self::setImageQuality();

		// Init classes
		ResponsivePics()->api = new RP_Rest_Api();
		ResponsivePics()->definitions = new RP_Definitions();
		ResponsivePics()->rules = new RP_Rules();
		ResponsivePics()->breakpoints = new RP_Breakpoints();
		ResponsivePics()->process = new RP_Process();
		ResponsivePics()->helpers = new RP_Helpers();
		ResponsivePics()->error = new RP_Error();

		// Hooks
		add_action('process_resize_request', ['RP_Process', 'process_resize_request'], 10, 6);
		add_action('rest_api_init',          ['RP_Rest_Api', 'register_api_routes']);
		add_filter('big_image_size_threshold', '__return_false');
	}

	// set number of grid columns
	public static function setColumns($value = 12) {
		self::$columns = $value;
	}

	// set grid gutter width, in pixels
	public static function setGutter($value = 20) {
		self::$gutter = $value;
	}

	// breakpoints used for "media(min-width: x)" in picture element, in pixels
	public static function setBreakpoints($value = [
		'xs'  => 0,
		'sm'  => 576,
		'md'  => 768,
		'lg'  => 992,
		'xl'  => 1200,
		'xxl' => 1400
	]) {
		self::$breakpoints = $value;
	}

	// grid system should match the container widths in css
	public static function setGridWidths($value = [
		'xs'  => 576, // self::$breakpoints['sm']
		'sm'  => 540,
		'md'  => 720,
		'lg'  => 960,
		'xl'  => 1140,
		'xxl' => 1320
	]) {
		self::$grid_widths = $value;
	}

	// set lazyload classname
	public static function setLazyLoadClass($value = 'lazyload') {
		self::$lazyload_class = $value;
	}

	// set lazyload classname
	public static function setImageQuality($value = 90) {
		self::$image_quality = $value;
	}

	// get breakpoints used for "media(min-width: x)" in picture element, in pixels
	public static function getBreakpoints() {
		return self::$breakpoints;
	}

	// get the current set of container widths
	public static function getGridWidths() {
		return self::$grid_widths;
	}

	// get number of grid columns
	public static function getColumns() {
		return self::$columns;
	}

	// get grid gutter width, in pixels
	public static function getGutter() {
		return self::$gutter;
	}

	// get lazyload classname
	public static function getLazyLoadClass() {
		return self::$lazyload_class;
	}

	// get image quality
	public static function getImageQuality() {
		return self::$image_quality;
	}

	/*
	 * Alias old `get` function to `get_picture`
	 */
	public static function get($id, $sizes, $picture_classes = null, $lazyload = false, $intrinsic = false) {
		return self::get_picture($id, $sizes, $picture_classes, $lazyload, $intrinsic);
	}

	/*
	 * Construct a responsive picture element
	 * returns <picture> element as html markup
	 */
	public static function get_picture($id = null, $sizes, $picture_classes = null, $lazyload = false, $intrinsic = false) {
		// check for valid image id
		$image_id = ResponsivePics()->process->process_image_id($id);
		if (is_wp_error($image_id)) {
			return ResponsivePics()->error->get_error($image_id);
		}

		// check for valid sizes
		$definition = ResponsivePics()->definitions->get_definition($image_id, $sizes);
		if (is_wp_error($definition)) {
			return ResponsivePics()->error->get_error($definition);
		}

		$sources = $definition['sources'];
		$picture = [];

		// check for valid classes
		$img_classes = [];
		if ($picture_classes) {
			$picture_classes = ResponsivePics()->process->process_classes($picture_classes);
			if (is_wp_error($picture_classes)) {
				return ResponsivePics()->error->get_error($picture_classes);
			}
		}

		// lazyload option
		if ($lazyload) {
			$img_classes[] = self::$lazyload_class;
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
		$style     = $intrinsic ? ' style="width:100%;"' : '';
		$ratio     = $intrinsic ? ' data-aspectratio=""' : '';
		$picture[] = sprintf('  <img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="%s alt="%s"%s%s />', $ratio, $definition['alt'], $classes, $style);
		$picture[] = '</picture>';

		return implode("\n", $picture) . "\n";
	}


	/*
	 * Construct a responsive image element
	 * returns <img> element as html markup
	 */
	public static function get_image($id = null, $sizes, $crop = false, $img_classes = null, $lazyload = false) {
		// check for valid image id
		$image_id = ResponsivePics()->process->process_image_id($id);
		if (is_wp_error($image_id)) {
			return $image_id;
		}

		// check for valid definition
		$definition = ResponsivePics()->definitions->get_definition($image_id, $sizes, false, false, $crop);
		if (is_wp_error($definition)) {
			return $definition;
		}

		$sources = $definition['sources'];

		// convert $picture_classes to array if it is a string
		if ($img_classes) {
			$img_classes = ResponsivePics()->process->process_classes($img_classes);
			if (is_wp_error($img_classes)) {
				return $img_classes;
			}
		}

		// lazyload option
		if ($lazyload) {
			$img_classes[] = self::$lazyload_class;
		}

		$src_attribute = $lazyload ? 'data-srcset' : 'srcset';
		$classes = $img_classes ? ' class="' . implode(' ', $img_classes) . '"' : '';

		// add all sources & sizes
		$srcsets  = [];
		$sizes    = [];
		$full_img = wp_get_attachment_image_src($id, 'full', false);
		$fallback = ' src="'. $full_img[0] . '"';

		foreach ($sources as $source) {
			$srcsets[] = $source['source1x'] . ' ' . $source['width'] . 'w';
			if (isset($source['source2x'])) {
				$srcsets[] = $source['source2x'] . ' ' . ($source['width'] * 2) . 'w';
			}

			if (isset($source['breakpoint'])) {
				$sizes[] = '(min-width: '. $source['breakpoint'] .'px) '. $source['width'] . 'px';
			} else {
				$sizes[] = $source['width'] . 'px';
			}
		}

		// add fallback size
		$sizes[] = '100vw';

		// construct image
		$image = sprintf('<img%s %s="%s" sizes="%s"%s alt="%s"/>', $classes, $src_attribute, implode(', ', $srcsets), implode(', ', $sizes), $fallback, $definition['alt']);
		return $image;
	}

	/*
	 * Construct a background image element and a matching responsive inline style element
	 *
	 * Returns an inline <style> element with a dedicated image class with media-queries for all the different image sizes
	 * and an div with the same dedicated image class
	 */
	public static function get_background($id = null, $sizes, $bg_classes = null) {
		// check for valid image id
		$image_id = ResponsivePics()->process->process_image_id($id);
		if (is_wp_error($image_id)) {
			return $image_id;
		}

		// check for valid definition
		$definition = ResponsivePics()->definitions->get_definition($image_id, $sizes, true);
		if (is_wp_error($definition)) {
			return $definition;
		}

		$sources = $definition['sources'];
		$copy = $image_id;

		// convert $classes to array if it is a string
		if ($bg_classes) {
			$bg_classes = ResponsivePics()->process->process_classes($bg_classes);
			if (is_wp_error($bg_classes)) {
				return $bg_classes;
			}
		}

		// prevent same id, append copy number to existing
		if (isset(self::$id_map[$image_id])) {
			self::$id_map[$image_id]++;
			$copy .= '-' . self::$id_map[$image_id];
		} else {
			self::$id_map[$image_id] = 0;
		}

		$id = sprintf('responsive-pics-background-%s', $copy);

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
					$background[] = sprintf('  %s {', ResponsivePics()->helpers->get_media_query_2x($source['breakpoint']));
					$background[] = sprintf('  #%s {background-image: url("%s");}', $id, $source['source2x']);
					$background[] = '  }';
				}
			} else {
				$background[] = sprintf('  #%s {background-image: url("%s");}', $id, $source['source1x']);
			}
		}

		$background[] = '  }';
		$background[] = '</style>';
		$background[] = sprintf('<div%s id="%s"></div>', $bg_classes ? ' class="' . implode(' ', $bg_classes) . '"' : '', $id);

		return implode("\n", $background) . "\n";
	}

	/*
	 * Construct a sources array
	 *
	 * Returns an array with all the available image sources and classes for the request
	 */
	public static function get_sources($id, $sizes, $classes = null, $lazyload = false) {
		if (!isset($id)) {
			return 'image id undefined';
		}

		$definition  = ResponsivePics()->definitions->get_definition($id, $sizes);
		if (!$definition) {
			return 'no image found with id ' . $id;
		}

		$sources = $definition['sources'];

		// convert $classes to array if it is a string
		if (!is_array($classes)) {
			if (!empty($classes)) {
				$classes = preg_split('/[\s,]+/', $classes);
			} else {
				$classes = [];
			}
		}

		// lazyload option
		if ($lazyload) {
			$classes[] = self::$lazyload_class;
		}

		$properties = [
			'alt'     => $definition['alt'],
			'classes' => $classes
		];

		// add all sources
		foreach ($sources as $source) {
			if (isset($source['breakpoint'])) {
				$src = [
					'breakpoint'  => $source['breakpoint'],
					'srcset'      => [
						'1x'      => $source['source1x']
					],
					'width'       => $source['width'],
					'height'      => $source['height'],
					'aspectratio' => $source['ratio']
				];

				if (isset($source['source2x'])) {
					$src['srcset']['2x'] = $source['source2x'];
				}

				$properties['sources'][] = $src;
			} else {
				$properties['sources'][] = [
					'srcset'      => [
						'1x'      => $source['source1x']
					],
					'width'       => $source['width'],
					'height'      => $source['height'],
					'aspectratio' => $source['ratio']
				];
			}
		}

		return $properties;
	}
}

new ResponsivePics();

// Support older versions > 0.7
class_alias('ResponsivePics', 'ResponsivePicture');