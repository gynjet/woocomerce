<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package YoastSEO\Local
 */

use Yoast\WP\Local\Repositories\Api_Keys_Repository;

/**
 * Class WPSEO_Local_Api_Keys_Repository
 *
 * This class exists to prevent breaking existing code.
 *
 * @deprecated See \Yoast\WP\Local\Repositories\Api_Keys_Repository
 */
class WPSEO_Local_Api_Keys_Repository extends Api_Keys_Repository {

	public function __construct() {
		$this->initialize();
	}
}
