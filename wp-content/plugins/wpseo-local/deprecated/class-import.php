<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package YoastSEO\Local
 */

use Yoast\WP\Local\Tools\Import;

/**
 * Class WPSEO_Local_Import
 *
 * @deprecated Use Yoast\WP\Local\Tools\Import instead
 */
class WPSEO_Local_Import extends Import {

	public function __construct() {
		$this->initialize();
		$this->register_hooks();
	}
}
