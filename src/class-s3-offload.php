<?php

use DeliciousBrains\WP_Offload_Media\Items\Media_Library_Item;
use DeliciousBrains\WP_Offload_Media\Items\Upload_Handler;

class RP_S3_Offload extends ResponsivePics {
	/**
	 * Upload to S3 storage
	 */
	public static function upload_image($id, $file = null) {
		global $as3cf;
		$s3_upload = null;

		// Plugin version check
		if (version_compare(WP_OFFLOAD_MEDIA_VERSION, '2.5.5', '>')) {
			$as3cf_item = Media_Library_Item::get_by_source_id($id);
			$objects    = $as3cf_item->objects();
			$size       = $file['width'] .'x'. $file['height'];

			syslog(LOG_DEBUG, json_encode($as3cf_item));
			/* This will offload ALL default wordpress sizes on each custom image in a new s3 folder
			$objects[$size] = [
				'source_file' => $file['file'],
				'is_private'  => false
			];
			$as3cf_item->set_objects($objects);

			if ($as3cf_item) {
				$upload_handler = $as3cf->get_item_handler(Upload_Handler::get_item_handler_key_name());
				$s3_upload      = $upload_handler->handle($as3cf_item);
			}*/
		} else {
			$s3_upload = $as3cf->upload_attachment($id, null, $file['path']);
		}

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
		$s3_delete  = null;
		$as3cf_item = Media_Library_Item::get_by_source_id($id);

		if (!$as3cf_item) {
			return;
		}

		// Plugin version check
		if (version_compare(WP_OFFLOAD_MEDIA_VERSION, '2.5.5', '>')) {
			/* Needs refractoring
			$chunks = array_chunk($manifest->objects, 1000);
			$region = $as3cf_item->region();
			$bucket = $as3cf_item->bucket();

			try {
				foreach ($chunks as $chunk) {
					$as3cf->get_provider_client($region)->delete_objects(array(
						'Bucket'  => $bucket,
						'Objects' => $chunk,
					));
				}
			} catch (Exception $e) {
				$error_msg = sprintf(__('Error removing files from bucket: %s', 'amazon-s3-and-cloudfront' ), $e->getMessage());
				$s3_delete = $this->return_handler_error($error_msg);
			}*/

		} else {
			$objects_to_remove = [];
			$paths_to_remove   = array_unique($paths);

			foreach ($paths_to_remove as $size => $path) {
				$objects_to_remove[] = array(
					'Key' => $as3cf_item->key(wp_basename($path))
				);
			}

			$s3_delete = $as3cf->delete_objects($as3cf_item->region(), $as3cf_item->bucket(), $objects_to_remove, true, true, false);
		}

		// Check for errors
		if (is_wp_error($s3_delete)) {
			$error_message = $s3_delete->get_error_message();
			$error_data    = $s3_delete->get_error_data();

			ResponsivePics()->error->add_error('error', $error_message, $error_data);
		}
	}
}