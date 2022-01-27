<?php

class RP_S3_Offload extends ResponsivePics {
	/**
	 * Upload to S3 storage
	 */
	public static function upload_image($id, $file = null) {
		global $as3cf;
		$s3_upload = $as3cf->upload_attachment($id, null, $file);

		if (is_wp_error($s3_upload) || empty($s3_upload) || !is_array($s3_upload)) {
			syslog(LOG_DEBUG, json_encode($s3_upload));
		}

		return $s3_upload;
	}
}