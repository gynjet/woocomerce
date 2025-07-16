<?php

namespace Yoast\WP\Local\Integrations;

use Yoast\WP\Local\PostType\PostType;
use Yoast\WP\Local\Presenters\Geo\Placename_Presenter;
use Yoast\WP\Local\Presenters\Geo\Position_Presenter;
use Yoast\WP\Local\Presenters\Geo\Region_Presenter;
use Yoast\WP\Local\Repositories\Locations_Repository;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Class Front_End_Integration.
 */
class Front_End_Integration implements Integration_Interface {

	/**
	 * The Location repository.
	 *
	 * @var Locations_Repository
	 */
	private $locations;

	/**
	 * The Post Type object.
	 *
	 * @var \Yoast\WP\Local\PostType\PostType
	 */
	private $post_type;

	/**
	 * The presenters needed by Local SEO.
	 *
	 * @var array
	 */
	private $needed_presenters = [];

	/**
	 * Front_End_Integration constructor.
	 *
	 * @param Locations_Repository $locations The Location repository.
	 * @param PostType             $post_type The PostType object.
	 */
	public function __construct( Locations_Repository $locations, PostType $post_type ) {
		$this->locations = $locations;
		$this->post_type = $post_type;
	}

	/**
	 * @inheritDoc
	 */
	public function register_hooks() {
		add_filter( 'wpseo_frontend_presenters', [ $this, 'add_presenters' ] );
	}

	/**
	 * @inheritDoc
	 */
	public static function get_conditionals() {
		return [];
	}

	/**
	 * Adds needed presenters for Local SEO.
	 *
	 * @param array $presenters The array of presenters.
	 *
	 * @return array The array of presenters.
	 */
	public function add_presenters( $presenters ) {
		$this->add_geo_presenters();

		return array_merge( $presenters, $this->needed_presenters );
	}

	/**
	 * Adds the GEO presenters if they're needed.
	 */
	private function add_geo_presenters() {
		if ( ! wpseo_has_multiple_locations() || is_singular( $this->post_type->get_post_type() ) ) {
			$this->needed_presenters[] = new Placename_Presenter( $this->locations );
			$this->needed_presenters[] = new Position_Presenter( $this->locations );
			$this->needed_presenters[] = new Region_Presenter( $this->locations );
		}
	}
}
