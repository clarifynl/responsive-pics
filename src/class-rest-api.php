<?php

class RP_Rest_Api extends ResponsivePics
{
	/**
	 * Register api routes
	 */
	public static function register_api_routes() {
		/**
		 * Register image api routes
		 */
		register_rest_route('responsive-pics/v1', '/(get-)?image/(?P<id>\d+)', [
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

		register_rest_route('responsive-pics/v1', '/(get-)?image-data/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => ['RP_Rest_Api', 'rest_get_image_data'],
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

		/**
		 * Register get-picture api route
		 */
		register_rest_route('responsive-pics/v1', '/(get-)?picture/(?P<id>\d+)', [
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

		register_rest_route('responsive-pics/v1', '/(get-)?picture-data/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => ['RP_Rest_Api', 'rest_get_picture_data'],
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

		/**
		 * Register get-background api route
		 */
		register_rest_route('responsive-pics/v1', '/(get-)?background/(?P<id>\d+)', [
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

		register_rest_route('responsive-pics/v1', '/(get-)?background-data/(?P<id>\d+)', [
			'methods'             => 'GET',
			'callback'            => ['RP_Rest_Api', 'rest_get_background_data'],
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

	/**
	 * REST API get image
	 *
	 * @param   (object) WP_REST_Request
	 * @return  (string) html
	 */
	public static function rest_get_image($request) {
		$route        = $request->get_route();
		$params       = $request->get_params();
		$query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
		$route_url    = $query_string ? $route .'?'. $query_string : $route;

		// Decode Parameters
		$id           = isset($request['id']) ? $request['id'] : null;
		$sizes        = isset($params['sizes']) ? urldecode($params['sizes']) : null;
		$classes      = isset($params['classes']) ? urldecode($params['classes']) : null;
		$crop         = isset($params['crop']) ? urldecode($params['crop']) : null;
		$lazyload     = isset($params['lazyload']) ? ($params['lazyload'] === 'true') : null;
		$lqip         = isset($params['lqip']) ? ($params['lqip'] === 'true') : null;

		if (class_exists('ResponsivePics')) {
			if ($sizes) {
				$image  = ResponsivePics::get_image($id, $sizes, $crop, $classes, $lazyload, $lqip, $route_url);

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


	/**
	 * REST API get image data
	 *
	 * @param   (object) WP_REST_Request
	 * @return  (object) image data
	 */
	public static function rest_get_image_data($request) {
		$route        = $request->get_route();
		$params       = $request->get_params();
		$query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
		$route_url    = $query_string ? $route .'?'. $query_string : $route;

		// Decode Parameters
		$id       = isset($request['id']) ? $request['id'] : null;
		$sizes    = isset($params['sizes']) ? urldecode($params['sizes']) : null;
		$crop     = isset($params['crop']) ? urldecode($params['crop']) : null;
		$classes  = isset($params['classes']) ? urldecode($params['classes']) : null;
		$lazyload = isset($params['lazyload']) ? urldecode($params['lazyload']) : false;
		$lqip     = isset($params['lqip']) ? urldecode($params['lqip']) : false;

		if (class_exists('ResponsivePics')) {
			if ($sizes) {
				$data = ResponsivePics::get_image_data($id, $sizes, $crop, $classes, $lazyload, $lqip, $route_url);

				// Check for errors
				if (is_wp_error($data)) {
					return $data;
				} else {
					$result = new WP_REST_Response($data, 200);

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

	/**
	 * REST API get picture
	 *
	 * @param   (object) WP_REST_Request
	 * @return  (string) html
	 */
	public static function rest_get_picture($request) {
		$route        = $request->get_route();
		$params       = $request->get_params();
		$query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
		$route_url    = $query_string ? $route .'?'. $query_string : $route;

		// Decode Parameters
		$id           = isset($request['id']) ? $request['id'] : null;
		$sizes        = isset($params['sizes']) ? urldecode($params['sizes']) : null;
		$classes      = isset($params['classes']) ? urldecode($params['classes']) : null;
		$lazyload     = isset($params['lazyload']) ? ($params['lazyload'] === 'true') : null;
		$intrinsic    = isset($params['intrinsic']) ? ($params['intrinsic'] === 'true') : null;

		if (class_exists('ResponsivePics')) {
			if ($sizes) {
				$picture = ResponsivePics::get_picture($id, $sizes, $classes, $lazyload, $intrinsic, $route_url);

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

	/**
	 * REST API get picture data
	 *
	 * @param   (object) WP_REST_Request
	 * @return  (object) picture data
	 */
	public static function rest_get_picture_data($request) {
		$route        = $request->get_route();
		$params       = $request->get_params();
		$query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
		$route_url    = $query_string ? $route .'?'. $query_string : $route;

		// Decode Parameters
		$id           = isset($request['id']) ? $request['id'] : null;
		$sizes        = isset($params['sizes']) ? urldecode($params['sizes']) : null;
		$classes      = isset($params['classes']) ? urldecode($params['classes']) : null;
		$lazyload     = isset($params['lazyload']) ? ($params['lazyload'] === 'true') : null;
		$intrinsic    = isset($params['intrinsic']) ? ($params['intrinsic'] === 'true') : null;

		if (class_exists('ResponsivePics')) {
			if ($sizes) {
				$data = ResponsivePics::get_picture_data($id, $sizes, $classes, $lazyload, $intrinsic, $route_url);

				// Check for errors
				if (is_wp_error($data)) {
					return $data;
				} else {
					$result = new WP_REST_Response($data, 200);

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

	/**
	 * REST API get background
	 *
	 * @param   (object) WP_REST_Request
	 * @return  (string) html
	 */
	public static function rest_get_background($request) {
		$route        = $request->get_route();
		$params       = $request->get_params();
		$query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
		$route_url    = $query_string ? $route .'?'. $query_string : $route;

		// Decode Parameters
		$id           = isset($request['id']) ? $request['id'] : null;
		$sizes        = isset($params['sizes']) ? urldecode($params['sizes']) : null;
		$classes      = isset($params['classes']) ? urldecode($params['classes']) : null;

		if (class_exists('ResponsivePics')) {
			if ($sizes) {
				$background = ResponsivePics::get_background($id, $sizes, $classes, $route_url);

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

	/**
	 * REST API get background data
	 *
	 * @param   (object) WP_REST_Request
	 * @return  (object) background data
	 */
	public static function rest_get_background_data($request) {
		$route        = $request->get_route();
		$params       = $request->get_params();
		$query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
		$route_url    = $query_string ? $route .'?'. $query_string : $route;

		// Decode Parameters
		$id           = isset($request['id']) ? $request['id'] : null;
		$sizes        = isset($params['sizes']) ? urldecode($params['sizes']) : null;
		$classes      = isset($params['classes']) ? urldecode($params['classes']) : null;

		if (class_exists('ResponsivePics')) {
			if ($sizes) {
				$data = ResponsivePics::get_background_data($id, $sizes, $classes, $route_url);

				// Check for errors
				if (is_wp_error($data)) {
					return $data;
				} else {
					$result = new WP_REST_Response($data, 200);

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