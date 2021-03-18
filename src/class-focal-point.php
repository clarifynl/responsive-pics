<?php

class RP_Focal_Point extends ResponsivePics {

	public function __construct() {
		add_action('wp_ajax_initialize-crop', ['RP_Focal_Point', 'initialize_crop']);
		add_action('wp_ajax_get-focalpoint',  ['RP_Focal_Point', 'get_focal_point']);
		add_action('admin_enqueue_scripts',   ['RP_Focal_Point', 'load_scripts']);
	}

	/**
	 * Enqueues all necessary CSS and Scripts
	 */
	public static function load_scripts() {
		// wp_enqueue_script('focalpoint-js', RESPONSIVE_PICS_ASSETS . 'js/focalpoint.min.js', ['jquery']);
		wp_enqueue_script('focalpoint-js', RESPONSIVE_PICS_ASSETS . 'js/focalpoint.js', ['jquery']);
		wp_localize_script('focalpoint-js', 'focalPointL10n', self::focal_point_l10n());
		wp_enqueue_script('focalpoint-js');
		wp_enqueue_style('focalpoint-css', RESPONSIVE_PICS_ASSETS . 'css/focalpoint.min.css');
	}

	/**
	 * Return all the translation strings necessary for the javascript
	 *
	 * @return array
	 */
	private static function focal_point_l10n() {
		return [
			'saveButton' => __('Save Focal Point', RESPONSIVE_PICS_TEXTDOMAIN),
			'saving'     => __('Saving…', RESPONSIVE_PICS_TEXTDOMAIN),
			'tryAgain'   => __('Please Try Again', RESPONSIVE_PICS_TEXTDOMAIN)
		];
	}

	/**
	 * Get the focalpoint of the attachment from the post meta
	 */
	public static function get_focal_point() {
		$attachment = getGlobalPostData('attachment');
		$attachment['focal_point'] = get_post_meta($attachment['id'], 'focal_point', true);
		$die = json_encode(['success' => false]);

		// Return the focal point if there is one
		if (null !== $attachment['id'] || is_array($attachment['focal_point'])) {
			$die = json_encode([
				'success'     => true,
				'focal_point' => $attachment['focal_point']
			]);
		}

		// Return the ajax call
		die($die);
	}

	/**
	 * Initialize a new crop
	 */
	public static function initialize_crop() {
		$attachment = getGlobalPostData('attachment');
		$die = json_encode(['success' => false]);

		// Crop the attachment if there is a focus point
		if (null !== $attachment['id'] && is_array($attachment['focal_point'])) {
			$crop = new CropService();
			$crop->crop($attachment['id'], $attachment['focal_point']);

			$die = json_encode(['success' => true]);
		}

		// Return the ajax call
		die($die);
	}
}