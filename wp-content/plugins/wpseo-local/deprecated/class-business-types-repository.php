<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package YoastSEO\Local
 */

use Yoast\WP\Local\Repositories\Business_Types_Repository;

/**
 * Class WPSEO_Local_Business_Types_Repository
 *
 * Extension of the new class Business_Types_Repository so existing code that relies on the old name does not break.
 *
 * @deprecated Use Yoast\WP\Local\Repositories\Business_Types_Repository instead.
 */
class WPSEO_Local_Business_Types_Repository extends Business_Types_Repository {

	/**
	 * An array of business types.
	 *
	 * @deprecated Access to this array should not be relied upon.
	 *
	 * @var array
	 */
	public $business_types = null;

	/**
	 * Old setup function, present for compatibility reasons
	 *
	 * @deprecated Function exists for compatibility reasons only, but is no longer used.
	 */
	public function setup() {
		// NoOp. Setup was called in the constructor, this is no longer needed.
	}

	/**
	 * WPSEO_Local_Business_Types_Repository constructor.
	 *
	 * This initializes the business types array which is public here, just like its predecessor would
	 */
	public function __construct() {
		$this->populate_business_types();
	}
}
