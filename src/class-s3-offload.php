<?php

use DeliciousBrains\WP_Offload_Media\Items\Media_Library_Item;

class RP_S3_Offload extends ResponsivePics {
	/**
	 * Upload to S3 storage
	 */
	public static function upload_image($id, $file = null) {
		global $as3cf;
		$s3_upload = $as3cf->upload_attachment($id, null, $file);

		// Check for errors
		if (is_wp_error($s3_upload)) {
			$error_message = $s3_upload->get_error_message();
			$error_data    = $s3_upload->get_error_data();

			ResponsivePics()->error->add_error('error', $error_message, $error_data);
		}

		return $s3_upload;
	}

	/**
	 * Delete image in S3 storage
	 */
	public static function delete_image($id, $paths = []) {
		global $as3cf;

		$objects_to_remove = [];
		$paths_to_remove   = array_unique($paths);
		$as3cf_item        = Media_Library_Item::get_by_source_id($id);

		// Check if file exists on storage
		if ($as3cf_item) {
			foreach ($paths_to_remove as $size => $path) {
				$objects_to_remove[] = array(
					'Key' => $as3cf_item->key(wp_basename($path))
				);
			}

			// Delete files on storage
			$s3_delete = $as3cf->delete_objects($as3cf_item->region(), $as3cf_item->bucket(), $objects_to_remove, true, true, false);

			// Check for errors
			if (is_wp_error($s3_delete)) {
				$error_message = $s3_delete->get_error_message();
				$error_data    = $s3_delete->get_error_data();

				ResponsivePics()->error->add_error('error', $error_message, $error_data);
			}
		}
	}
}