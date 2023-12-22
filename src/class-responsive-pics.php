<?php

class ResponsivePics
{
	public static $columns = null;
	public static $gutter = null;
	public static $grid_widths = null;
	public static $breakpoints = null;
	public static $supported_mime_types = [];
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
		'l' => 'left',
		'f' => 'focal'
	];

	// some handy shortcuts
	public static $crop_shortcuts = [
		'c' => 'c c',
		't' => 'c t',
		'r' => 'r c',
		'b' => 'c b',
		'l' => 'l c',
		'f' => 'f'
	];

	// crop values to percentage
	public static $crop_percentages = [
		'center' => 50,
		'top'    => 0,
		'right'  => 100,
		'bottom' => 100,
		'left'   => 0
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
		self::setSupportedMimeTypes();
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
		add_action('as3cf_init',                  ['ResponsivePics', 'as3cf_init']);
		add_action('delete_attachment',           ['RP_Process',     'process_delete_attachment'], 10, 2);
		add_action('process_resize_request',      ['RP_Process',     'process_resize_request'], 10, 8);
		add_action('rest_api_init',               ['RP_Rest_Api',    'register_api_routes']);
		add_filter('responsive_pics_file_exists', ['RP_Sources',     'file_exists'], 10, 3);
		add_filter('big_image_size_threshold',    '__return_false');
	}

	// as3cf init
	public static function as3cf_init() {
		include (RESPONSIVE_PICS_DIR . '/src/class-s3-offload.php');

		// Init S3_Offload if compatible S3 plugin is installed
		if (class_exists('Amazon_S3_And_CloudFront')) {
			if (isset($GLOBALS['aws_meta']['amazon-s3-and-cloudfront']['version'])) {
				define('WP_OFFLOAD_MEDIA_VERSION', $GLOBALS['aws_meta']['amazon-s3-and-cloudfront']['version']);
			}

			ResponsivePics()->s3offload = new RP_S3_Offload();
		}
	}

	// set number of grid columns
	public static function setColumns(int $value = 12) {
		self::$columns = $value;
	}

	// set grid gutter width, in pixels
	public static function setGutter(int $value = 30) {
		self::$gutter = $value;
	}

	// breakpoints used for "media(min-width: x)" in picture element, in pixels
	public static function setBreakpoints(array $value = [
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
	public static function setGridWidths(array $value = [
		'xs'  => 576, // self::$breakpoints['sm']
		'sm'  => 540,
		'md'  => 720,
		'lg'  => 960,
		'xl'  => 1140,
		'xxl' => 1320
	]) {
		self::$grid_widths = $value;
	}

	// set supported mime types
	private static function setSupportedMimeTypes(array $mime_types = [
		'image/jpeg',
		'image/png',
		'image/gif'
	]) {
		global $wp_version;

		if (version_compare($wp_version, '5.8', '>=' )) {
			$mime_types[] = 'image/webp';
		}

		self::$supported_mime_types = $mime_types;
	}

	// set max width factor
	public static function setMaxWidthFactor(int $factor = 2) {
		self::$max_width_factor = $factor;
	}

	// set lazyload classname
	public static function setLazyLoadClass(string $value = 'lazyload') {
		self::$lazyload_class = $value;
	}

	// set lqip (low quality image placeholder) image width
	public static function setLqipWidth(int $width = 100) {
		self::$lqip_width = $width;
	}

	// set lqip (low quality image placeholder) classname
	public static function setLqipClass(string $value = 'blur-up') {
		self::$lqip_class = $value;
	}

	// set image quality
	public static function setImageQuality(int $value = 90) {
		self::$image_quality = $value;
	}

	// set rest api cache
	public static function setRestApiCache(bool $boolean = false) {
		self::$wp_rest_cache = $boolean;
	}

	// set rest api cache duration (max-age)
	public static function setRestApiCacheDuration(int $value = 3600) {
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

	/**
	 * Alias old `get` function to `get_picture`
	 *
	 * @param (int) $id - attachment id
	 * @param (string) $sizes - requested picture sizes
	 * @param (string|array) $picture_classes - requested picture classes
	 * @param (bool) $lazyload - lazyload option
	 * @param (bool) $intrinsic - intrinsic option
	 *
	 * @return (string) <picture> element as html markup
	 */
	public static function get($id = null, $sizes = null, $picture_classes = null, $lazyload = false, $intrinsic = false) {
		return self::get_picture($id, $sizes, $picture_classes, $lazyload, $intrinsic);
	}

	/**
	 * Construct a responsive image element
	 *
	 * @param (int) $id - attachment id
	 * @param (string) $sizes - requested image sizes
	 * @param (string) $crop - requested image crop
	 * @param (string|array) $img_classes - requested image classes
	 * @param (bool) $lazyload - lazyload option
	 * @param (bool) $lqip - lqip option
	 * @param (string) $rest_route - rest route
	 *
	 * @return (string) <img> element as html markup
	 */
	public static function get_image($id = null, $sizes = null, $crop = false, $img_classes = null, $lazyload = false, $lqip = false, $rest_route = null) {
		// get image sources
		$definition = self::get_image_data($id, $sizes, $crop, $img_classes, $lazyload, $lqip, $rest_route);

		// check for errors
		if (count(self::$wp_error->get_error_messages()) > 0) {
			return ResponsivePics()->error->get_error(self::$wp_error);
		}

		// get data
		$lazyload     = isset($definition['lazyload']) ? $definition['lazyload'] : false;
		$img_classes  = isset($definition['classes']) ? $definition['classes'] : null;
		$lqip_img     = isset($definition['lqip']) ? $definition['lqip'] : null;

		$lazy_native  = $lazyload === 'native';
		$src_attr     = ($lazyload && !$lazy_native) ? 'data-srcset' : 'srcset';
		$classes      = $img_classes ? ' class="' . implode(' ', $img_classes) . '"' : '';
		$loading_attr = $lazy_native ? ' loading="lazy"': '';

		// return normal image if unsupported mime type
		if (!in_array($definition['mimetype'], self::$supported_mime_types) ||
			$definition['animated'])
		{
			$original_src = wp_get_attachment_image_src($id, 'original');
			$image_html   = sprintf('<img%s %s="%s"%s alt="%s"/>', $classes, $src_attr, $original_src[0], $loading_attr, $definition['alt']);

			return $image_html;
		}

		// add all sources & sizes
		$srcsets  = [];
		$sizes    = [];
		$src      = $lqip_img ? ' src="'. $lqip_img . '"' : '';

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
		$image_html = sprintf('<img%s %s="%s" sizes="%s"%s%s alt="%s"/>', $classes, $src_attr, implode(', ', $srcsets), implode(', ', $sizes), $src, $loading_attr, $definition['alt']);

		return $image_html;
	}

	/**
	 * Construct responsive image data
	 *
	 * @param (int) $id - attachment id
	 * @param (string) $sizes - requested image sizes
	 * @param (string) $crop - requested image crop
	 * @param (string|array) $img_classes - requested image classes
	 * @param (bool) $lazyload - lazyload option
	 * @param (bool) $lqip - lqip option
	 * @param (string) $rest_route - rest route
	 *
	 * @return (array) responsive image data
	 */
	public static function get_image_data($id = null, $sizes = null, $crop = false, $img_classes = null, $lazyload = false, $lqip = false, $rest_route = null) {
		// init WP_Error
		self::$wp_error = new WP_Error();

		// check for valid image id
		$image = ResponsivePics()->process->process_image($id);

		if (!$image) {
			return;
		}

		// check for valid sizes value
		$definition = ResponsivePics()->process->process_sizes($image, $sizes, 'desc', false, $crop, $rest_route);

		// convert $img_classes to array if it is a string
		if ($img_classes) {
			$img_classes = ResponsivePics()->process->process_classes($img_classes);
		} else {
			$img_classes = [];
		}

		// check for valid lazyload value
		if (isset($lazyload)) {
			$lazyload    = ResponsivePics()->process->process_lazyload($lazyload, 'lazyload');
			$lazy_native = $lazyload === 'native';
			$definition['lazyload'] = $lazyload;
		}

		// lazyload option
		if ($lazyload && !$lazy_native) {
			$img_classes[] = self::$lazyload_class;
		}

		// check for valid lqip value
		if (isset($lqip)) {
			$lqip = ResponsivePics()->process->process_boolean($lqip, 'lqip');
		}

		// low quality image placeholder option
		$lqip_img = null;

		if ($lqip) {
			$img_classes[]      = self::$lqip_class;
			$lqip_sizes         = ResponsivePics()->process->process_sizes($id, '0:' . self::$lqip_width, 'desc', false, $crop, $rest_route);
			$lqip_sources       = isset($lqip_sizes['sources']) ? $lqip_sizes['sources'] : [];
			$lqip_img           = isset($lqip_sources[0]['source1x']) ? $lqip_sources[0]['source1x'] : null;
			$definition['lqip'] = $lqip_img;
		}

		$definition['classes'] = $img_classes;

		// check for errors
		if (count(self::$wp_error->get_error_messages()) > 0) {
			return ResponsivePics()->error->get_error(self::$wp_error);
		}

		return $definition;
	}

	/**
	 * Construct a responsive picture element
	 *
	 * @param (int) $id - attachment id
	 * @param (string) $sizes - requested picture sizes
	 * @param (string|array) $picture_classes - requested picture classes
	 * @param (bool) $lazyload - lazyload option
	 * @param (bool) $intrinsic - intrinsic option
	 * @param (string) $rest_route - rest route
	 *
	 * @return (string) <picture> element as html markup
	 */
	public static function get_picture($id = null, $sizes = null, $picture_classes = null, $lazyload = false, $intrinsic = false, $rest_route = null) {
		// get picture sources
		$definition = self::get_picture_data($id, $sizes, $picture_classes, $lazyload, $intrinsic, $rest_route);

		// check for errors
		if (count(self::$wp_error->get_error_messages()) > 0) {
			return ResponsivePics()->error->get_error(self::$wp_error);
		}

		// get data
		$lazyload        = isset($definition['lazyload']) ? $definition['lazyload'] : false;
		$intrinsic       = isset($definition['intrinsic']) ? $definition['intrinsic'] : false;
		$picture_classes = isset($definition['picture_classes']) ? $definition['picture_classes'] : null;
		$image_classes   = isset($definition['image_classes']) ? $definition['image_classes'] : null;

		// construct vars
		$picture_class   = $picture_classes ? ' class="' . implode(' ', $picture_classes) . '"' : '';
		$img_class       = $image_classes ? ' class="' . implode(' ', $image_classes) . '"' : '';
		$lazy_native     = $lazyload === 'native';
		$src_attr        = ($lazyload && !$lazy_native) ? 'data-srcset' : 'srcset';
		$loading_attr    = $lazy_native ? ' loading="lazy"': '';

		// start constructing <picture> element
		$picture   = [];
		$picture[] = sprintf('<picture%s>', $picture_class);

		// add all sources
		$sources = isset($definition['sources']) ? $definition['sources'] : [];

		foreach ($sources as $source) {
			$data_aspectratio = ($intrinsic && isset($source['ratio'])) ? ' data-aspectratio="' . $source['ratio'] . '"' : '';

			if (isset($source['breakpoint'])) {
				$urls = $source['source1x'];

				if (isset($source['source2x'])) {
					$urls .= ' 1x, ' . $source['source2x'] . ' 2x';
				}

				$picture[] = sprintf('  <source media="(min-width: %spx)" %s="%s"%s />', $source['breakpoint'], $src_attr, $urls, $data_aspectratio);
			} else {
				$picture[] = sprintf('  <source %s="%s"%s />', $src_attr, $source['source1x'], $data_aspectratio);
			}
		}

		// transparent gif
		$style     = $intrinsic ? ' style="width:100%;"' : '';
		$ratio     = $intrinsic ? ' data-aspectratio=""' : '';
		$picture[] = sprintf('  <img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="%s alt="%s"%s%s%s />', $ratio, $definition['alt'], $loading_attr, $img_class, $style);
		$picture[] = '</picture>';

		return implode("\n", $picture) . "\n";
	}

	/**
	 * Construct responsive picture data
	 *
	 * @param (int) $id - attachment id
	 * @param (string) $sizes - requested picture sizes
	 * @param (string|array) $picture_classes - requested picture classes
	 * @param (bool) $lazyload - lazyload option
	 * @param (bool) $intrinsic - intrinsic option
	 * @param (string) $rest_route - rest route
	 *
	 * @return (array) responsive picture data
	 */
	public static function get_picture_data($id = null, $sizes = null, $picture_classes = null, $lazyload = false, $intrinsic = false, $rest_route = null) {
		// init WP_Error
		self::$wp_error = new WP_Error();

		// check for valid image id
		$image = ResponsivePics()->process->process_image($id);

		if (!$image) {
			return;
		}

		// check for valid sizes value
		$definition = ResponsivePics()->process->process_sizes($image, $sizes, 'desc', true, null, $rest_route);

		// check for valid classes value
		if ($picture_classes) {
			$picture_classes = ResponsivePics()->process->process_classes($picture_classes);
		} else {
			$picture_classes = [];
		}

		// check for valid lazyload value
		if (isset($lazyload)) {
			$lazyload    = ResponsivePics()->process->process_lazyload($lazyload, 'lazyload');
			$lazy_native = $lazyload === 'native';
			$definition['lazyload'] = $lazyload;
		}

		// lazyload option
		$img_classes = [];
		if ($lazyload && !$lazy_native) {
			$img_classes[] = self::$lazyload_class;
		}

		// check for valid intrinsic value
		if (isset($intrinsic)) {
			$intrinsic = ResponsivePics()->process->process_boolean($intrinsic, 'intrinsic');
			$definition['intrinsic'] = $intrinsic;
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

		$definition['picture_classes'] = $picture_classes;
		$definition['image_classes']   = $img_classes;

		// check for errors
		if (count(self::$wp_error->get_error_messages()) > 0) {
			return ResponsivePics()->error->get_error(self::$wp_error);
		}

		return $definition;
	}

	/**
	 * Construct a background image element and a matching responsive inline style element
	 *
	 * @param (int) $id - attachment id
	 * @param (string) $sizes - requested background sizes
	 * @param (string|array) $bg_classes - requested background classes
	 * @param (string) $rest_route - rest route
	 *
	 * @return an inline <style> element with a dedicated image class with media-queries for all the different image sizes and an div with the same dedicated image class
	 */
	public static function get_background($id = null, $sizes = null, $bg_classes = null, $rest_route = null) {
		// get background sources
		$definition = self::get_background_data($id, $sizes, $bg_classes, $rest_route);

		// check for errors
		if (count(self::$wp_error->get_error_messages()) > 0) {
			return ResponsivePics()->error->get_error(self::$wp_error);
		}

		// construct vars
		$bg_id        = isset($definition['id']) ? $definition['id'] : $id;
		$bg_classes   = isset($definition['classes']) ? $definition['classes'] : null;
		$bg_class     = $bg_classes ? ' class="' . implode(' ', $bg_classes) . '"' : '';

		$background   = [];
		$background[] = '<style scoped="scoped" type="text/css">';

		// add all sources as background-images
		$sources = isset($definition['sources']) ? $definition['sources'] : [];

		foreach ($sources as $source) {
			if (isset($source['breakpoint'])) {
				$sources = $source['source1x'];

				$background[] = sprintf('  @media (min-width: %spx) {', $source['breakpoint']);
				$background[] = sprintf('  #%s {background-image: url("%s");}', $bg_id, $source['source1x']);
				$background[] = '  }';

				if (isset($source['source2x'])) {
					$background[] = sprintf('  %s {', ResponsivePics()->helpers->get_media_query_2x($source['breakpoint']));
					$background[] = sprintf('  #%s {background-image: url("%s");}', $bg_id, $source['source2x']);
					$background[] = '  }';
				}
			} else {
				$background[] = sprintf('  #%s {background-image: url("%s");}', $bg_id, $source['source1x']);
			}
		}

		$background[] = '  }';
		$background[] = '</style>';
		$background[] = sprintf('<div id="%s"%s></div>', $bg_id, $bg_class);

		return implode("\n", $background) . "\n";
	}

	/**
	 * Construct a responsive background image element
	 *
	 * @param (int) $id - attachment id
	 * @param (string) $sizes - requested background sizes
	 * @param (string|array) $bg_classes - requested background classes
	 * @param (string) $rest_route - rest route
	 *
	 * @return background sources as data
	 */
	public static function get_background_data($id = null, $sizes = null, $bg_classes = null, $rest_route = null) {
		// init WP_Error
		self::$wp_error = new WP_Error();

		// check for valid image id
		$image = ResponsivePics()->process->process_image($id);
		if (!$image) {
			return;
		}

		// check for valid sizes
		$definition = ResponsivePics()->process->process_sizes($image, $sizes, 'asc', true, null, $rest_route);

		// prevent same id, append copy number to existing
		$copy = $image;
		if (isset(self::$id_map[$image])) {
			self::$id_map[$image]++;
			$copy .= '-' . self::$id_map[$image];
		} else {
			self::$id_map[$image] = 0;
		}

		// convert $bg_classes to array if it is a string
		if ($bg_classes) {
			$bg_classes = ResponsivePics()->process->process_classes($bg_classes);
		} else {
			$bg_classes = [];
		}

		$definition['id']      = sprintf('responsive-pics-background-%s', $copy);
		$definition['classes'] = $bg_classes;

		// check for errors
		if (count(self::$wp_error->get_error_messages()) > 0) {
			return ResponsivePics()->error->get_error(self::$wp_error);
		}

		return $definition;
	}
}

new ResponsivePics();

// Support older versions > 0.7
class_alias('ResponsivePics', 'ResponsivePicture');
