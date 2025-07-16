<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package YoastSEO\Local
 */

use Yoast\WP\Local\Repositories\Timezone_Repository;

/**
 * Class WPSEO_Local_Timezone_Repository
 *
 * This is the old Timezone Repository.
 *
 * @deprecated Use \Yoast\WP\Local\Repositories\Timezone_Repository instead
 */
class WPSEO_Local_Timezone_Repository extends Timezone_Repository {

	public function __construct() {
		// Code that relies on this class is unlikely to call initialize, so we make sure to call it here.
		$this->initialize();
	}
}
