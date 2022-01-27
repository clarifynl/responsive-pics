<?php

class RP_S3_Offload extends ResponsivePics {
	/**
	 * @var $as3cf
	 */
	private $as3cf;

	/**
	 * Construct S3 Offload
	 */
	public function __construct() {
		global $as3cf;
		if (!empty($as3cf_compat_check)) {
			$this->as3cf = $as3cf;
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