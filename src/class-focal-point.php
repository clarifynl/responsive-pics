<?php

class RP_Focal_Point extends ResponsivePics {

	public function __construct() {
		$this->addHooks();
	}

	/**
	 * Make sure all hooks are being executed.
	 */
	private function addHooks() {
		add_action('wp_ajax_initialize-crop', [$this, 'initializeCrop']);
		add_action('wp_ajax_get-focuspoint', [$this, 'getFocusPoint']);
		add_action('admin_enqueue_scripts', [$this, 'loadScripts']);
	}

	/**
	 * Enqueues all necessary CSS and Scripts
	 */
	public function loadScripts() {
		// wp_enqueue_script('focuspoint-js', IMAGEFOCUS_ASSETS . 'js/focuspoint.min.js', ['jquery']);
		// wp_localize_script('focuspoint-js', 'focusPointL10n', $this->focusPointL10n());
		// wp_enqueue_script('focuspoint-js');

		// wp_enqueue_style('image-focus-css', IMAGEFOCUS_ASSETS . 'css/style.min.css');
	}

	/**
	 * Return all the translation strings necessary for the javascript
	 *
	 * @return array
	 */
	private function focusPointL10n() {
		return [
			'cropButton' => __('Crop image', IMAGEFOCUS_TEXTDOMAIN),
		];
	}

	/**
	 * Get the focuspoint of the attachment from the post meta
	 */
	public function getFocusPoint() {
		// Get $_POST['attachment']
		// $attachment = getGlobalPostData('attachment');

		// // Get the post meta
		// $attachment['focusPoint'] = get_post_meta($attachment['id'], 'focus_point', true);

		// $die = json_encode(['success' => false]);

		// // Return the focus point if there is one
		// if (null !== $attachment['id'] || is_array($attachment['focusPoint'])) {
		// 	$die = json_encode([
		// 		'success'    => true,
		// 		'focusPoint' => $attachment['focusPoint']
		// 	]);
		// }

		// // Return the ajax call
		// die($die);
	}

	/**
	 * Initialize a new crop
	 */
	public function initializeCrop() {
		// Get $_POST['attachment']
		// $attachment = getGlobalPostData('attachment');

		// $die = json_encode(['success' => false]);

		// // Crop the attachment if there is a focus point
		// if (null !== $attachment['id'] && is_array($attachment['focusPoint'])) {
		// 	$crop = new CropService();
		// 	$crop->crop($attachment['id'], $attachment['focusPoint']);

		// 	$die = json_encode(['success' => true]);
		// }

		// // Return the ajax call
		// die($die);
	}
}