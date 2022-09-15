<?php

class RP_Rest_Api extends ResponsivePics
{
	/*
	 * Takes a URL with unescaped query parameters and builds it up again with encoded parameters.
	 */

	private static function query_param_encode($url) {
		$url = parse_url($url);
		$url_str = "";

		if (isset($url['scheme'])) {
			$url_str .= $url['scheme'].'://';
		}

		if (isset($url['host'])) {
			$url_str .= $url['host'];
		}

		if (isset($url['path'])) {
			$url_str .= $url['path'];
		}

		if (isset($url['query'])) {
			$query = explode('&', $url['query']);

			foreach ($query as $j=>$value) {
				$value = explode('=', $value, 2);

				if (count($value) == 2) {
					$query[$j] = urlencode($value[0]) . '=' . urlencode($value[1]);
				} else {
					$query[$j] = urlencode($value[0]);
				}
			}

			$url_str .= '?' . implode('&', $query);
		}

		if (isset($url['fragment'])) {
			$url_str .= '#' . $url['fragment'];
		}

		return $url_str;
	}

	/**
	 * Register api routes
	 */
	public static function register_api_routes() {
		/**
		 * Register get-image api route
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
	}

	/**
	 * REST API get image
	 *
	 * @param   (object) WP_REST_Request
	 * @return  (string) html
	 */
	public static function rest_get_image($request) {
		$route  = $request->get_route();
		$params = $request->get_params();
		unset($params['id']);
		$route_url = self::query_param_encode(add_query_arg($params, $route));

		$id        = isset($request['id']) ? $request['id'] : null;
		$sizes     = isset($params['sizes']) ? urldecode($params['sizes']) : null;
		$classes   = isset($params['classes']) ? urldecode($params['classes']) : null;
		$crop      = isset($params['crop']) ? urldecode($params['crop']) : null;
		$lazyload  = isset($params['lazyload']) ? ($params['lazyload'] === 'true') : null;
		$lqip      = isset($params['lqip']) ? ($params['lqip'] === 'true') : null;

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
	 * REST API get picture
	 *
	 * @param   (object) WP_REST_Request
	 * @return  (string) html
	 */
	public static function rest_get_picture($request) {
		$route  = $request->get_route();
		$params = $request->get_params();
		unset($params['id']);
		$route_url = add_query_arg($params, $route);

		$id        = isset($request['id']) ? $request['id'] : null;
		$sizes     = isset($params['sizes']) ? urldecode($params['sizes']) : null;
		$classes   = isset($params['classes']) ? urldecode($params['classes']) : null;
		$lazyload  = isset($params['lazyload']) ? ($params['lazyload'] === 'true') : null;
		$intrinsic = isset($params['intrinsic']) ? ($params['intrinsic'] === 'true') : null;

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
	 * REST API get background
	 *
	 * @param   (object) WP_REST_Request
	 * @return  (string) html
	 */
	public static function rest_get_background($request) {
		$route  = $request->get_route();
		$params = $request->get_params();
		unset($params['id']);
		$route_url = add_query_arg($params, $route);

		$id      = isset($request['id']) ? $request['id'] : null;
		$sizes   = isset($params['sizes']) ? urldecode($params['sizes']) : null;
		$classes = isset($params['classes']) ? urldecode($params['classes']) : null;

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
}