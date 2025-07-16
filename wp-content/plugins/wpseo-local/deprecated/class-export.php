<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package YoastSEO\Local
 */

use Yoast\WP\Local\Repositories\Locations_Repository;
use Yoast\WP\Local\Tools\Export;

/**
 * Class WPSEO_Local_Export
 *
 * @deprecated Use Yoast\WP\Local\Tools\Export instead
 */
class WPSEO_Local_Export extends Export {

	public function __construct() {
		$repository = new Locations_Repository();

		parent::__construct( $repository );

		$this->initialize();
		$this->register_hooks();
	}
}
