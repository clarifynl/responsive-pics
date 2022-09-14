<?php

class RP_Rest_Api extends ResponsivePics {

	public static function register_api_routes() {
		// image
		register_rest_route('responsive-pics/v1', '/get-image/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => ['RP_Rest_Api', 'rest_get_image'],
			'permission_callback' => '__return_true',
			'args'                => [
				'id' => [
					'validate_callback' => function($id, $request, $key) {
						return is_numeric($id);
					},
					'sanitize_callback' => function($id, $request, $key) {
						return (int) $id;
					}
				]
			]
		]);

		// picture
		register_rest_route('responsive-pics/v1', '/get(-picture)?/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => ['RP_Rest_Api', 'rest_get_picture'],
			'permission_callback' => '__return_true',
			'args'                => [
				'id' => [
					'validate_callback' => function($id, $request, $key) {
						return is_numeric($id);
					},
					'sanitize_callback' => function($id, $request, $key) {
						return (int) $id;
					}
				]
			]
		]);

		// background
		register_rest_route('responsive-pics/v1', '/get-background/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => ['RP_Rest_Api', 'rest_get_background'],
			'permission_callback' => '__return_true',
			'args'                => [
				'id' => [
					'validate_callback' => function($id, $request, $key) {
						return is_numeric($id);
					},
					'sanitize_callback' => function($id, $request, $key) {
						return (int) $id;
					}
				]
			]
		]);
	}

	// get image
	public static function rest_get_image($request) {
		$referer   = $request->get_header('referer');
		$params    = $request->get_params();
		$id        = isset($request['id']) ? $request['id'] : null;
		$sizes     = isset($params['sizes']) ? urldecode($params['sizes']) : null;
		$classes   = isset($params['classes']) ? urldecode($params['classes']) : null;
		$crop      = isset($params['crop']) ? urldecode($params['crop']) : null;
		$lazyload  = isset($params['lazyload']) ? ($params['lazyload'] === 'true') : null;
		$lqip      = isset($params['lqip']) ? ($params['lqip'] === 'true') : null;

		if (class_exists('ResponsivePics')) {
			if ($sizes) {
				$image  = ResponsivePics::get_image($id, $sizes, $crop, $classes, $lazyload, $lqip, $referer);

				// Check for errors
				if (is_wp_error($image)) {
					return $image;
				} else {
					$result = new WP_REST_Response($image, 200);

					// Set cache duration
					if (self::$wp_rest_cache) {
						$cache_duration = self::$wp_rest_cache_duration;
					} else {
						$cache_duration = 0;
					}

					$result->set_headers(array('Cache-Control' => 'max-age=' . $cache_duration));
					return $result;
				}
			} else {
				return new WP_Error('responsive_pics_invalid',  __('the request is missing the required sizes parameter', 'responsive-pics'), $params);
			}
		} else {
			return new WP_Error('responsive_pics_missing', __('the responsive pics plugin was not found', 'responsive-pics'));
		}
	}

	// get picture
	public static function rest_get_picture($request) {
		$params    = $request->get_params();
		$id        = isset($request['id']) ? $request['id'] : null;
		$sizes     = isset($params['sizes']) ? urldecode($params['sizes']) : null;
		$classes   = isset($params['classes']) ? urldecode($params['classes']) : null;
		$lazyload  = isset($params['lazyload']) ? ($params['lazyload'] === 'true') : null;
		$intrinsic = isset($params['intrinsic']) ? ($params['intrinsic'] === 'true') : null;

		if (class_exists('ResponsivePics')) {
			if ($sizes) {
				$picture = ResponsivePics::get_picture($id, $sizes, $classes, $lazyload, $intrinsic);

				// Check for errors
				if (is_wp_error($picture)) {
					return $picture;
				} else {
					$result = new WP_REST_Response($picture, 200);

					// Set cache duration
					if (self::$wp_rest_cache) {
						$cache_duration = self::$wp_rest_cache_duration;
					} else {
						$cache_duration = 0;
					}

					$result->set_headers(array('Cache-Control' => 'max-age=' . $cache_duration));
					return $result;
				}
			} else {
				return new WP_Error('responsive_pics_invalid',  __('the request is missing the required sizes parameter', 'responsive-pics'), $params);
			}
		} else {
			return new WP_Error('responsive_pics_missing', __('the responsive pics plugin was not found', 'responsive-pics'));
		}
	}

	// get background
	public static function rest_get_background($request) {
		$params  = $request->get_params();
		$id      = isset($request['id']) ? $request['id'] : null;
		$sizes   = isset($params['sizes']) ? urldecode($params['sizes']) : null;
		$classes = isset($params['classes']) ? urldecode($params['classes']) : null;

		if (class_exists('ResponsivePics')) {
			if ($sizes) {
				$background = ResponsivePics::get_background($id, $sizes, $classes);

				// Check for errors
				if (is_wp_error($background)) {
					return $background;
				} else {
					$result = new WP_REST_Response($background, 200);

					// Set cache duration
					if (self::$wp_rest_cache) {
						$cache_duration = self::$wp_rest_cache_duration;
					} else {
						$cache_duration = 0;
					}

					$result->set_headers(array('Cache-Control' => 'max-age=' . $cache_duration));
					return $result;
				}
			} else {
				return new WP_Error('responsive_pics_invalid',  __('the request is missing the required sizes parameter', 'responsive-pics'), $params);
			}
		} else {
			return new WP_Error('responsive_pics_missing', __('the responsive pics plugin was not found', 'responsive-pics'));
		}
	}
}