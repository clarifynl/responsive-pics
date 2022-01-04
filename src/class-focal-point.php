<?php

class RP_Focal_Point extends ResponsivePics {

	public function __construct() {
		add_action('admin_enqueue_scripts',        ['RP_Focal_Point', 'load_scripts']);
		add_action('print_media_templates',        ['RP_Focal_Point', 'print_media_templates'], 10, 1);
		add_filter('attachment_fields_to_edit',    ['RP_Focal_Point', 'attachment_fields_to_edit'], 10, 2);
		add_filter('attachment_fields_to_save',    ['RP_Focal_Point', 'attachment_fields_to_save'], 10, 2);
		add_filter('wp_prepare_attachment_for_js', ['RP_Focal_Point', 'wp_prepare_attachment_for_js'], 10, 3);
		add_action('wp_ajax_get_focal_point',      ['RP_Focal_Point', 'get_focal_point']);
		add_action('wp_ajax_set_focal_point',      ['RP_Focal_Point', 'set_focal_point']);
	}

	/**
	 * Enqueues all necessary CSS and Scripts
	 */
	public static function load_scripts() {
		// Scripts
		$assets = ResponsivePicsWP::$enqueue->enqueue('focalpoint', 'admin', [
			'js'        => true,
			'css'       => true,
			'js_dep'    => ['jquery', 'jquery-ui-draggable'],
			'css_dep'   => [],
			'in_footer' => false
		]);
		$entry_point = array_pop($assets['js']);
		wp_localize_script($entry_point['handle'], 'responsivePicsL10n', self::focal_point_l10n());

		// Styles
		$assets = ResponsivePicsWP::$enqueue->enqueue('focalpoint', 'admin', [
			'js'  => false,
			'css' => true
		]);
	}

	/**
	 * Print custom media templates
	 */
	public static function print_media_templates() {
		?>
		<script type="text/html" id="tmpl-attachment-select-focal-point">
			<div class="image-focal">
				<div class="image-focal__wrapper">
					<div class="image-focal__point"></div>
					<div class="image-focal__clickarea"></div>
				</div>
			</div>
		</script>
		<script type="text/html" id="tmpl-attachment-save-focal-point">
			<button type="button" class="button button-disabled save-attachment-focal-point">
				<?php _e('Save Focal Point', RESPONSIVE_PICS_TEXTDOMAIN); ?>
			</button>
		</script>
		<?php
	}

	/**
	 * Add custom focal point attachment field
	 */
	public static function attachment_fields_to_edit($form_fields, $post) {
		// Exclude applications
		if (preg_match('/application/', $post->post_mime_type)) {
			return $form_fields;
		}

		$focal_point = get_post_meta($post->ID, 'responsive_pics_focal_point', true);
		$form_fields['responsive_pics_focal_point_x'] = array(
			'label'      => 'Focal Point X-axis (%)',
			'input'      => 'number',
			'value'      => isset($focal_point['x']) ? $focal_point['x'] : 50,
			'exclusions' => array('audio', 'video')
		);
		$form_fields['responsive_pics_focal_point_y'] = array(
			'label'      => 'Focal Point Y-axis (%)',
			'input'      => 'number',
			'value'      => isset($focal_point['y']) ? $focal_point['y'] : 50,
			'exclusions' => array('audio', 'video')
		);

		return $form_fields;
	}

	/**
	 * Save custom focal point attachment field
	 */
	public static function attachment_fields_to_save($post, $attachment) {
		if (isset($attachment['responsive_pics_focal_point_x']) &&
			isset($attachment['responsive_pics_focal_point_y'])) {
			$focal_point_x = $attachment['responsive_pics_focal_point_x'];
			$focal_point_y = $attachment['responsive_pics_focal_point_y'];
			$focal_point   = [
				'x' => $focal_point_x,
				'y' => $focal_point_y
			];
			update_post_meta($post['ID'], 'responsive_pics_focal_point', $focal_point);
		} else {
			delete_post_meta($post['ID'], 'responsive_pics_focal_point' );
		}

		return $post;
	}

	/**
	 * Add focal point to attachment data for JavaScript
	 */
	public static function wp_prepare_attachment_for_js($response, $attachment, $meta) {
		$focal_point = get_post_meta($attachment->ID, 'responsive_pics_focal_point', true);

		if (!empty($focal_point)) {
			$response['focalPoint'] = $focal_point;
		}

		return $response;
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
			'saved'      => __('Saved', RESPONSIVE_PICS_TEXTDOMAIN),
			'tryAgain'   => __('Please Try Again', RESPONSIVE_PICS_TEXTDOMAIN)
		];
	}

	/**
	 * Get the focalpoint of the attachment from the post meta
	 */
	public static function get_focal_point() {
		$attachment  = isset($_POST['attachment']) ? $_POST['attachment'] : [];
		$post_id     = isset($attachment['id']) ? $attachment['id'] : null;
		$focal_point = get_post_meta($post_id, 'responsive_pics_focal_point', true);

		// Return the focal point if there is one
		if ($post_id && is_array($focal_point)) {
			wp_send_json_success([
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
		$post_id     = isset($attachment['id']) ? $attachment['id'] : null;

		// Save the focal point if there is one
		if ($post_id && is_array($focal_point)) {
			update_post_meta($post_id, 'responsive_pics_focal_point', $focal_point);
			wp_send_json_success();
		}

		// Return the ajax call
		wp_send_json_error();
	}
}