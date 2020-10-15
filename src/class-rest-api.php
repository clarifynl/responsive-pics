<?php

class RP_Rest_Api extends ResponsivePics {

	public static function register_api_routes() {
		// picture
		register_rest_route('responsive-pics/v1', '/get(-picture)?/(?P<id>\d+)/(?P<sizes>\S+)(?:/(?P<classes>\S+))?', [
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
				],
				'sizes' => [
					'validate_callback' => function($sizes, $request, $key) {
						return is_string($sizes);
					},
					'sanitize_callback' => function($sizes, $request, $key) {
						return urldecode($sizes);
					}
				],
				'classes' => [
					'validate_callback' => function($classes, $request, $key) {
						return is_string($classes);
					},
					'sanitize_callback' => function($classes, $request, $key) {
						return urldecode($classes);
					}
				]
			]
		]);

		// image
		register_rest_route('responsive-pics/v1', '/get-image/(?P<id>\d+)/(?P<sizes>\S+)(?:/(?P<crop>\S+))?(?:/(?P<classes>\S+))?', [
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
				],
				'sizes' => [
					'validate_callback' => function($sizes, $request, $key) {
						return is_string($sizes);
					},
					'sanitize_callback' => function($sizes, $request, $key) {
						return urldecode($sizes);
					}
				],
				'crop' => [
					'validate_callback' => function($crop, $request, $key) {
						return is_string($crop) || is_numeric($crop);
					},
					'sanitize_callback' => function($crop, $request, $key) {
						return urldecode($crop);
					}
				]
				'classes' => [
					'validate_callback' => function($classes, $request, $key) {
						return is_string($classes);
					},
					'sanitize_callback' => function($classes, $request, $key) {
						return urldecode($classes);
					}
				]
			]
		]);

		// background
		register_rest_route('responsive-pics/v1', '/get-background/(?P<id>\d+)/(?P<sizes>\S+)(?:/(?P<classes>\S+))?', [
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
				],
				'sizes' => [
					'validate_callback' => function($sizes, $request, $key) {
						return is_string($sizes);
					},
					'sanitize_callback' => function($sizes, $request, $key) {
						return urldecode($sizes);
					}
				],
				'classes' => [
					'validate_callback' => function($classes, $request, $key) {
						return is_string($classes);
					},
					'sanitize_callback' => function($classes, $request, $key) {
						return urldecode($classes);
					}
				]
			]
		]);
	}

	// get picture
	public static function rest_get_picture($request) {
		$id      = isset($request['id']) ? (int) $request['id'] : null;
		$sizes   = isset($request['sizes']) ? $request['sizes'] : null;
		$classes = isset($request['classes']) ? $request['classes'] : null;

		$params    = $request->get_params();
		$lazyload  = isset($params['lazyload']) ? ($params['lazyload'] === 'true') : null;
		$intrinsic = isset($params['intrinsic']) ? ($params['intrinsic'] === 'true') : null;

		if (class_exists('ResponsivePics')) {
			if ($id && $sizes) {
				$picture = ResponsivePics::get_picture($id, $sizes, $classes, $lazyload, $intrinsic);

				// Check for errors
				if (is_wp_error($picture)) {
					return $picture;
				} else {
					$result = new WP_REST_Response($picture, 200);
					// Set caching
					$result->set_headers(array('Cache-Control' => 'max-age=3600'));
					return $result;
				}
			} else {
				return new WP_Error('responsive_pics_invalid', __('the request is missing required parameters', 'responsive-pics'), $params);
			}
		} else {
			return new WP_Error('responsive_pics_missing', __('the responsive pics plugin was not found', 'responsive-pics'));
		}
	}

	// get image
	public static function rest_get_image($request) {
		$id    = isset($request['id']) ? (int) $request['id'] : null;
		$sizes = isset($request['sizes']) ? $request['sizes'] : null;
		$classes = isset($request['classes']) ? $request['classes'] : null;

		$params    = $request->get_params();
		$crop      = isset($params['crop']) ? urldecode($params['crop']) : null;
		$lazyload  = isset($params['lazyload']) ? ($params['lazyload'] === 'true') : null;

		if (class_exists('ResponsivePics')) {
			if ($id && $sizes) {
				$image  = ResponsivePics::get_image($id, $sizes, $crop, $classes, $lazyload);

				// Check for errors
				if (is_wp_error($image)) {
					return $image;
				} else {
					$result = new WP_REST_Response($image, 200);
					// Set caching
					$result->set_headers(array('Cache-Control' => 'max-age=3600'));
					return $result;
				}
			} else {
				return new WP_Error('responsive_pics_invalid',  __('the request is missing required parameters', 'responsive-pics'), $params);
			}
		} else {
			return new WP_Error('responsive_pics_missing', __('the responsive pics plugin was not found', 'responsive-pics'));
		}
	}

	// get background
	public static function rest_get_background($request) {
		$id      = isset($request['id']) ? (int) $request['id'] : null;
		$sizes   = isset($request['sizes']) ? $request['sizes'] : null;
		$classes = isset($request['classes']) ? $request['classes'] : null;

		if (class_exists('ResponsivePics')) {
			if ($id && $sizes) {
				$background = ResponsivePics::get_background($id, $sizes, $classes);

				// Check for errors
				if (is_wp_error($background)) {
					return $background;
				} else {
					$result = new WP_REST_Response($background, 200);
					// Set caching
					$result->set_headers(array('Cache-Control' => 'max-age=3600'));
					return $result;
				}
			} else {
				return new WP_Error('responsive_pics_invalid',  __('the request is missing required parameters', 'responsive-pics'), $request);
			}
		} else {
			return new WP_Error('responsive_pics_missing', __('the responsive pics plugin was not found', 'responsive-pics'));
		}
	}
}