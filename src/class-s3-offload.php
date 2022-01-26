<?php

class RP_S3_Offload extends ResponsivePics {
	/**
	 * @var $as3cf
	 */
	public $as3cf;

	/**
	 * Construct S3 Offload
	 */
	public function __construct() {
		syslog(LOG_DEBUG, 'class_exists: '. class_exists('DeliciousBrains\WP_Offload_Media\Amazon_S3_And_CloudFront'));
		if (class_exists('DeliciousBrains\WP_Offload_Media\Amazon_S3_And_CloudFront')) {
			$this->as3cf = new DeliciousBrains\WP_Offload_Media\Amazon_S3_And_CloudFront();
			syslog(LOG_DEBUG, 'RP_S3_Offload construct: ' . json_encode($this->as3cf));
		}
	}

	/**
	 * Upload to S3 storage
	 */
	public static function upload_image($id, $file = null) {
		$uploaded = $this->as3cf->upload_attachment($id);

		if (is_wp_error($uploaded) || empty($uploaded) || !is_array($uploaded)) {
			syslog(LOG_DEBUG, json_encode($uploaded));
		}
	}
}