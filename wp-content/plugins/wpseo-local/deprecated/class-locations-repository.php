<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package YoastSEO\Local
 */

use Yoast\WP\Local\PostType\PostType;
use Yoast\WP\Local\Repositories\Locations_Repository;

/**
 * Class WPSEO_Local_Locations_Repository
 *
 * The original class that used to be here has been moved to \Yoast\WP\Local\Repositories\Locations_Repository.
 * This is a class that can be used in the transition period so it is not necessary to update all usages at once.
 *
 * @deprecated Use Yoast\WP\Local\Repositories\Locations_Repository instead (using DI)
 */
class WPSEO_Local_Locations_Repository extends Locations_Repository {

	/**
	 * WPSEO_Local_Locations_Repository constructor.
	 *
	 * The DI container handles this for the Locations_Repository class. Since we can't use that
	 * here we need to call initialize() manually when the class is constructed.
	 */
	public function __construct() {
		$post_type = new PostType();
		$post_type->initialize();

		parent::__construct( $post_type );

		$this->initialize();
	}
}
