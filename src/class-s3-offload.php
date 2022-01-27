<?php

class RP_S3_Offload extends ResponsivePics {
	/**
	 * Upload to S3 storage
	 */
	public static function upload_image($id, $file = null) {
		global $as3cf;
		syslog(LOG_DEBUG, '$as3cf: ' . json_encode($as3cf));
		$uploaded = $as3cf->upload_attachment($id);

		if (is_wp_error($uploaded) || empty($uploaded) || !is_array($uploaded)) {
			syslog(LOG_DEBUG, json_encode($uploaded));
		}
	}
}