<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package WPSEO_Local\Frontend
 */

namespace Yoast\WP\Local\Presenters\Geo;

use Yoast\WP\Local\Repositories\Locations_Repository;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter;

class Placename_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = '<meta name="geo.placename" content="%s" />';

	/**
	 * @var Locations_Repository
	 */
	private $repository;

	/**
	 * Placename_Presenter constructor.
	 *
	 * @param Locations_Repository $repository Locations repository.
	 */
	public function __construct( Locations_Repository $repository ) {
		$this->repository = $repository;
	}

	/**
	 * @inheritDoc
	 */
	public function get() {
		$location = $this->repository->for_current_page();

		if ( $location === null ) {
			return '';
		}

		return $location->business_city;
	}
}
