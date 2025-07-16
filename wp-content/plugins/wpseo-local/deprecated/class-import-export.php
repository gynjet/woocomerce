<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package YoastSEO\Local
 */

use Yoast\WP\Local\Tools\Import_Export;

/**
 * Class WPSEO_Local_Import_Export
 *
 * @deprecated Use Yoast\WP\Local\Tools\Import_Export instead
 */
class WPSEO_Local_Import_Export extends Import_Export {

	public function __construct() {
		$this->initialize();
		$this->register_hooks();
	}

	/**
	 * @inheritDoc
	 *
	 * An implementation is needed here because the new class is abstract.
	 */
	public static function get_conditionals() {
		return [];
	}
}
