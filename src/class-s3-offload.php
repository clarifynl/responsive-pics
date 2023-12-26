<?php

use DeliciousBrains\WP_Offload_Media\Integrations\Media_Library;
use DeliciousBrains\WP_Offload_Media\Items\Media_Library_Item;
use DeliciousBrains\WP_Offload_Media\Items\Upload_Handler;
use DeliciousBrains\WP_Offload_Media\Items\Remove_Provider_Handler;

class RP_S3_Offload extends ResponsivePics
{
	/**
	 * S3 Offload constructor
	 */
	public function __construct() {
		add_filter('responsive_pics_file_exists', [$this, 'file_exists'], 5, 2);
	}

	/**
	 * Upload to S3 storage
	 */
	public static function upload_image($id, $file = null, $width = null, $height = null) {
		global $as3cf;

		$s3_upload = null;

		// Plugin version check
		if (version_compare(WP_OFFLOAD_MEDIA_VERSION, '2.5.5', '>')) {
			$as3cf_item  = Media_Library_Item::get_by_source_id($id);
			$size        = $width .'x'. $height;
			$source_file = $file['file'];

			if ($as3cf_item) {
				$item_objects        = $as3cf_item->objects();
				$item_objects[$size] = [
					'source_file' => $source_file,
					'is_private'  => false
				];

				$as3cf_item->set_objects($item_objects);

				// Only save if we have the primary file uploaded.
				if (isset($item_objects[$as3cf_item::primary_object_key()])) {
					$as3cf_item->save();
				}

				// Upload item
				$upload_handler = $as3cf->get_item_handler(Upload_Handler::get_item_handler_key_name());
				$s3_upload      = $upload_handler->handle($as3cf_item);

				// Re-init item cache
				Media_Library_Item::init_cache();
				do_action('responsive_pics_file_s3_uploaded', $id, $file);
			}
		} else {
			$s3_upload = $as3cf->upload_attachment($id, null, $file['path']);

			do_action('responsive_pics_file_s3_uploaded', $id, $file);
		}

		// Check for errors
		if (is_wp_error($s3_upload)) {
			$error_message = $s3_upload->get_error_message();
			error_log($error_message);
		}

		return $s3_upload;
	}

	/**
	 * Delete image in S3 storage
	 */
	public static function delete_image($id, $paths = []) {
		global $as3cf;

		$s3_remove       = null;
		$as3cf_item      = Media_Library_Item::get_by_source_id($id);
		$paths_to_remove = array_unique($paths);

		if (!$as3cf_item) {
			return;
		}

		// Plugin version check
		if (version_compare(WP_OFFLOAD_MEDIA_VERSION, '2.5.5', '<=')) {
			$objects_to_remove = [];

			foreach ($paths_to_remove as $size => $path) {
				$objects_to_remove[] = array(
					'Key' => $as3cf_item->key(wp_basename($path))
				);
			}

			$s3_remove = $as3cf->delete_objects($as3cf_item->region(), $as3cf_item->bucket(), $objects_to_remove, true, true, false);
		}

		// Check for errors
		if (is_wp_error($s3_remove)) {
			$error_message = $s3_remove->get_error_message();
			$error_data    = $s3_remove->get_error_data();

			ResponsivePics()->error->add_error('error', $error_message, $error_data);
		} else {
			do_action('responsive_pics_file_s3_deleted', $id, $paths_to_remove);
		}
	}

	/**
	 * Check if file exists in S3 storage
	 *
	 * @param int    $id    The attachment ID.
	 * @param array  $file  The file array to check.
	 *
	 * @return bool
	 */
	public static function file_exists($id, $file) {
		// Not an s3 url so it won't exist on S3
		if (strpos($file['path'], 's3') !== 0) {
			return false;
		}

		// Check the Offload Media cache if the plugin version is greater than 2.5.5
		if (version_compare(WP_OFFLOAD_MEDIA_VERSION, '2.5.5', '>')) {
			// Stop default file_exists filter from running
			remove_filter('responsive_pics_file_exists', ['RP_Sources', 'file_exists'], 10);

			// Get item object & file dimensions
			$as3cf_item    = Media_Library_Item::get_by_source_id($id);
			$ratio         = (int) $file['ratio'];
			$width         = (int) $file['width'];
			$height        = (int) $file['height'];

			// Calculate size
			$size          = round($width * $ratio) .'x'. round($height * $ratio);
			$as3cf_objects = $as3cf_item ? $as3cf_item->objects() : null;
			$size_exists   = $as3cf_objects ? array_key_exists($size, $as3cf_objects) : false;
			$file_exists   = $size_exists ? ($file['file'] === $as3cf_objects[$size]['source_file']) : false;

			return ($size_exists && $file_exists);
		}

		// Doesn't exist in the Offload Media cache, let the default file_exists filter handle it
		return false;
	}
}
