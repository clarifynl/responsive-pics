<?php

class RP_Focal_Point extends ResponsivePics {

	public function __construct() {
		add_action('admin_enqueue_scripts',   ['RP_Focal_Point', 'load_scripts']);
		add_action('print_media_templates',   ['RP_Focal_Point', 'customize_attachment_template']);
		add_action('wp_ajax_get_focal_point', ['RP_Focal_Point', 'get_focal_point']);
		add_action('wp_ajax_set_focal_point', ['RP_Focal_Point', 'set_focal_point']);
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
	 * Add attachment id to "Attachments Details Two Column" Backbone micro template
	 *
	 * @see https://stackoverflow.com/a/25948448/2078474
	 */
	public static function customize_attachment_template() { ?>
		<script>
			jQuery(document).ready(function($) {
				jQuery('script#tmpl-attachment-details-two-column:first').prepend('<div class="attachment-id hidden" id="attachment-id" data-id="{{ data.id }}"/>');
			});
		</script>
	<?php }

	/**
	 * Get the focalpoint of the attachment from the post meta
	 */
	public static function get_focal_point() {
		$attachment  = isset($_POST['attachment']) ? $_POST['attachment'] : [];
		$post_id     = isset($_GET['item']) ? $_GET['item'] : null;
		$focal_point = get_post_meta($post_id, 'focal_point', true);

		// Return the focal point if there is one
		if ($post_id && is_array($focal_point)) {
			wp_send_json_success([
				'post_id'     => $post_id,
				'focal_point' => $focal_point
			]);
		}

		// Return the ajax call
		wp_send_json_error();
	}

	/**
	 * Set the focalpoint of the attachment as post meta
	 */
	public static function set_focal_point() {
		$attachment  = isset($_POST['attachment']) ? $_POST['attachment'] : [];
		$focal_point = isset($attachment['focal_point']) ? $attachment['focal_point'] : null;
		$post_id     = isset($_SERVER['item']) ? $_SERVER['item'] : null;

		// Save the focal point if there is one
		if ($post_id && is_array($focal_point)) {
			update_post_meta($post_id, 'focal_point', $focal_point);
			wp_send_json_success([
				'post_id'     => $post_id,
				'focal_point' => $focal_point
			]);
		}

		// Return the ajax call
		wp_send_json_error([
			'post_id' => $post_id
		]);
	}
}