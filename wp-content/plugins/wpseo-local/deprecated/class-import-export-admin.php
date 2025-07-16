<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package YoastSEO\Local
 */

use Yoast\WP\Local\Tools\Import_Export_Admin;

/**
 * Class WPSEO_Local_Import_Export_Admin
 *
 * @deprecated Use Yoast\WP\Local\Tools\Import_Export_Admin instead
 */
class WPSEO_Local_Import_Export_Admin extends Import_Export_Admin {

	public function __construct() {
		$this->register_hooks();
	}
}
