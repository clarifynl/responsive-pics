<?php

class RP_S3_Offload extends ResponsivePics {
	/**
	 * Upload to S3 storage
	 */
	public static function upload_image($id, $file = null) {
		global $as3cf;
		$s3_upload = $as3cf->upload_attachment($id, null, $file);

		// Check for errors
		if (is_wp_error($s3_upload) || empty($s3_upload) || !is_array($s3_upload)) {
			$error_message = $s3_upload->get_error_message();
			$error_data    = $s3_upload->get_error_data();

			ResponsivePics()->error->add_error('error', $error_message, $error_data);
		}

		return $s3_upload;
	}
}