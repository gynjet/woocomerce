<?php
/**
 * @package Yoast SEO Local.
 */

/**
 * Class PostType
 *
 * This class is added to support the transition from the old codebase to the new DI container in `/src/`.
 *
 * @deprecated Use \Yoast\WP\Local\PostType\PostType in stead.
 */
class PostType extends \Yoast\WP\Local\PostType\PostType {

	/**
	 * @var \Yoast\WP\Local\PostType\PostType
	 */
	private static $instance;

	/**
	 * @return \Yoast\WP\Local\PostType\PostType
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new Yoast\WP\Local\PostType\PostType();
			self::$instance->initialize();
		}

		return self::$instance;
	}
}
