<?php


class ResponsivePics {
	public static $columns = null;
	public static $gutter = null;
	public static $grid_widths = null;
	public static $breakpoints = null;
	public static $max_width_factor = null;
	public static $lazyload_class = null;
	public static $lqip_width = null;
	public static $lqip_class = null;
	public static $image_quality = null;
	public static $wp_rest_cache = null;
	public static $wp_rest_cache_duration = null;
	public static $wp_error = null;

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
			'class-helpers',
			'class-error',
			'class-process',
			'class-rules',
			'class-breakpoints',
			'class-grid',
			'class-sources',
			'class-focal-point'
		];

		foreach ($includes as $inc) {
			include (RESPONSIVE_PICS_DIR . '/src/'. $inc .'.php');
		}

		// Set defaults
		self::setColumns();
		self::setGutter();
		self::setGridWidths();
		self::setBreakpoints();
		self::setMaxWidthFactor();
		self::setLazyLoadClass();
		self::setLqipWidth();
		self::setLqipClass();
		self::setImageQuality();
		self::setRestApiCache();
		self::setRestApiCacheDuration();

		// Init classes
		ResponsivePics()->api = new RP_Rest_Api();
		ResponsivePics()->helpers = new RP_Helpers();
		ResponsivePics()->error = new RP_Error();
		ResponsivePics()->process = new RP_Process();
		ResponsivePics()->rules = new RP_Rules();
		ResponsivePics()->breakpoints = new RP_Breakpoints();
		ResponsivePics()->grid = new RP_Grid();
		ResponsivePics()->sources = new RP_Sources();

		// Init Focal Point if user is allowed to upload media
		if (current_user_can('upload_files') === true) {
			ResponsivePics()->focalpoint = new RP_Focal_Point();
		}

		// Hooks
		add_action('process_resize_request',   ['RP_Process', 'process_resize_request'], 10, 6);
		add_action('rest_api_init',            ['RP_Rest_Api', 'register_api_routes']);
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

	// set max width factor
	public static function setMaxWidthFactor($factor = 2) {
		self::$max_width_factor = $factor;
	}

	// set lazyload classname
	public static function setLazyLoadClass($value = 'lazyload') {
		self::$lazyload_class = $value;
	}

	// set lqip (low quality image placeholder) image width
	public static function setLqipWidth($width = 200) {
		self::$lqip_width = $width;
	}

	// set lqip (low quality image placeholder) classname
	public static function setLqipClass($value = 'blur-up') {
		self::$lqip_class = $value;
	}

	// set image quality
	public static function setImageQuality($value = 90) {
		self::$image_quality = $value;
	}

	// set rest api cache
	public static function setRestApiCache($boolean = false) {
		self::$wp_rest_cache = $boolean;
	}

	// set rest api cache duration (max-age)
	public static function setRestApiCacheDuration($value = 3600) {
		self::$wp_rest_cache_duration = $value;
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

	// get max width factor
	public static function getMaxWidthFactor() {
		return self::$max_width_factor;
	}

	// get lazyload classname
	public static function getLazyLoadClass() {
		return self::$lazyload_class;
	}

	// get lqip width
	public static function getLqipWidth() {
		return self::$lqip_width;
	}

	// get lqip classname
	public static function getLqipClass() {
		return self::$lqip_class;
	}

	// get image quality
	public static function getImageQuality() {
		return self::$image_quality;
	}

	// get rest api cache
	public static function getRestApiCache() {
		return self::$wp_rest_cache;
	}

	// get rest api cache duration
	public static function getRestApiCacheDuration() {
		return self::$wp_rest_cache_duration;
	}

	/*
	 * Alias old `get` function to `get_picture`
	 */
	public static function get($id = null, $sizes = null, $picture_classes = null, $lazyload = false, $intrinsic = false) {
		return self::get_picture($id, $sizes, $picture_classes, $lazyload, $intrinsic);
	}

	/*
	 * Construct a responsive picture element
	 * returns <picture> element as html markup
	 */
	public static function get_picture($id = null, $sizes = null, $picture_classes = null, $lazyload = false, $intrinsic = false) {
		// init WP_Error
		self::$wp_error = new WP_Error();

		// check for valid image id
		$image = ResponsivePics()->process->process_image($id);

		// check for valid sizes
		$definition = [];
		if ($image) {
			$definition = ResponsivePics()->process->process_sizes($image, $sizes);
		}

		// check for valid classes value
		if ($picture_classes) {
			$picture_classes = ResponsivePics()->process->process_classes($picture_classes);
		} else {
			$picture_classes = [];
		}

		// check for valid lazyload value
		if (isset($lazyload)) {
			$lazyload = ResponsivePics()->process->process_boolean($lazyload, 'lazyload');
		}

		// check for valid intrinsic value
		if (isset($intrinsic)) {
			$intrinsic = ResponsivePics()->process->process_boolean($intrinsic, 'intrinsic');
		}

		// check for errors
		if (count(self::$wp_error->get_error_messages()) > 0) {
			return ResponsivePics()->error->get_error(self::$wp_error);
		}

		// set img classes
		$img_classes = [];
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

		// start constructing <picture> element
		$picture = [];
		$picture[] = sprintf('<picture%s>', $picture_classes ? ' class="' . implode(' ', $picture_classes) . '"' : '');

		$src_attribute = $lazyload ? 'data-srcset' : 'srcset';
		$classes = $img_classes ? ' class="' . implode(' ', $img_classes) . '"' : '';

		// add all sources
		$sources = isset($definition['sources']) ? $definition['sources'] : [];
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
	public static function get_image($id = null, $sizes = null, $crop = false, $img_classes = null, $lazyload = false, $lqip = false) {
		// init WP_Error
		self::$wp_error = new WP_Error();

		// check for valid image id
		$image = ResponsivePics()->process->process_image($id);

		// check for valid sizes value
		$definition = [];
		if ($image) {
			$definition = ResponsivePics()->process->process_sizes($image, $sizes, 'desc', false, $crop);
		}

		// convert $picture_classes to array if it is a string
		if ($img_classes) {
			$img_classes = ResponsivePics()->process->process_classes($img_classes);
		} else {
			$img_classes = [];
		}

		// check for valid lazyload value
		if (isset($lazyload)) {
			$lazyload = ResponsivePics()->process->process_boolean($lazyload, 'lazyload');
		}

		// check for valid lqip value
		if (isset($lqip)) {
			$lqip = ResponsivePics()->process->process_boolean($lqip, 'lqip');
		}

		// check for errors
		if (count(self::$wp_error->get_error_messages()) > 0) {
			return ResponsivePics()->error->get_error(self::$wp_error);
		}

		// lazyload option
		if ($lazyload) {
			$img_classes[] = self::$lazyload_class;
		}

		// low quality image placeholder option
		$lqip_img = null;
		if ($lqip) {
			$img_classes[] = self::$lqip_class;
			$lqip_sizes    = ResponsivePics()->process->process_sizes($image, '0:' . self::$lqip_width, 'desc', false, $crop);
			$lqip_sources  = isset($lqip_sizes['sources']) ? $lqip_sizes['sources'] : [];
			$lqip_img      = isset($lqip_sources[0]['source1x']) ? $lqip_sources[0]['source1x'] : null;
		}

		$src_attribute = $lazyload ? 'data-srcset' : 'srcset';
		$classes = $img_classes ? ' class="' . implode(' ', $img_classes) . '"' : '';

		// return normal image if unsupported mime type
		if (!in_array($definition['mimetype'], self::$supported_mime_types)) {
			$original_src = wp_get_attachment_image_src($image);
			$image_html   = sprintf('<img%s %s="%s" alt="%s"/>', $classes, $src_attribute, $original_src[0], $definition['alt']);
			return $image_html;
		}

		// add all sources & sizes
		$srcsets  = [];
		$sizes    = [];
		$src      = ($lqip && $lqip_img) ? ' src="'. $lqip_img . '"' : '';

		// start constructing <img> element
		$sources = isset($definition['sources']) ? $definition['sources'] : [];
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
		$image_html = sprintf('<img%s %s="%s" sizes="%s"%s alt="%s"/>', $classes, $src_attribute, implode(', ', $srcsets), implode(', ', $sizes), $src, $definition['alt']);
		return $image_html;
	}

	/*
	 * Construct a background image element and a matching responsive inline style element
	 *
	 * Returns an inline <style> element with a dedicated image class with media-queries for all the different image sizes
	 * and an div with the same dedicated image class
	 */
	public static function get_background($id = null, $sizes = null, $bg_classes = null) {
		// init WP_Error
		self::$wp_error = new WP_Error();

		// check for valid image id
		$image = ResponsivePics()->process->process_image($id);

		// check for valid sizes
		$definition = [];
		if ($image) {
			$definition = ResponsivePics()->process->process_sizes($image, $sizes, 'asc');
		}

		// convert $classes to array if it is a string
		if ($bg_classes) {
			$bg_classes = ResponsivePics()->process->process_classes($bg_classes);
		} else {
			$bg_classes = [];
		}

		// check for errors
		if (count(self::$wp_error->get_error_messages()) > 0) {
			return ResponsivePics()->error->get_error(self::$wp_error);
		}

		$sources = isset($definition['sources']) ? $definition['sources'] : [];
		$copy = $image;

		// prevent same id, append copy number to existing
		if (isset(self::$id_map[$image])) {
			self::$id_map[$image]++;
			$copy .= '-' . self::$id_map[$image];
		} else {
			self::$id_map[$image] = 0;
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
}

new ResponsivePics();

// Support older versions > 0.7
class_alias('ResponsivePics', 'ResponsivePicture');