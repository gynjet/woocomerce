<?php
/**
 * @package WPSEO_Local\Frontend\Schema
 */

use Yoast\WP\SEO\Config\Schema_IDs;
use Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;

/**
 * Class WPSEO_Local_Organization.
 *
 * Manages the Schema for an Organization.
 *
 * @property WPSEO_Schema_Context $context A value object with context variables.
 * @property array                $options Local SEO options.
 */
class WPSEO_Local_Organization extends Abstract_Schema_Piece {

	/**
	 * A value object with context variables.
	 *
	 * @var WPSEO_Schema_Context
	 */
	public $context;

	/**
	 * Stores the options for this plugin.
	 *
	 * @var array
	 */
	public $options = [];

	/**
	 * Constructor.
	 *
	 * @param WPSEO_Schema_Context $context A value object with context variables.
	 */
	public function __construct( WPSEO_Schema_Context $context ) {
		$this->context = $context;
		$this->options = get_option( 'wpseo_local' );
	}

	/**
	 * Determines whether or not this piece should be added to the graph.
	 *
	 * @return bool
	 */
	public function is_needed() {
		return is_singular( PostType::get_instance()->get_post_type() );
	}

	/**
	 * Generates JSON+LD output for locations.
	 *
	 * @return bool|array Array with Place schema data. Returns false when no valid location is found.
	 */
	public function generate() {
		$repository = new WPSEO_Local_Locations_Repository();
		$place_data = $repository->for_current_page();

		$data = [
			'@type'            => empty( $place_data->business_type ) ? 'Organization' : $place_data->business_type,
			'@id'              => $this->context->canonical . WPSEO_Local_Schema_IDs::ORGANIZATION_ID,
			'name'             => get_the_title(),
			'location'         => [ '@id' => $this->context->canonical . WPSEO_Local_Schema_IDs::PLACE_ID ],
			'address'          => [ '@id' => $this->context->canonical . WPSEO_Local_Schema_IDs::ADDRESS_ID ],
			'mainEntityOfPage' => [ '@id' => $this->context->canonical . Schema_IDs::WEBPAGE_HASH ],
		];

		if ( wpseo_multiple_location_one_organization() ) {
			$data['parentOrganization'] = [ '@id' => $this->context->site_url . Schema_IDs::ORGANIZATION_HASH ];
		}

		$data = $this->add_logo( $data, $place_data );
		$data = $this->add_organization_attributes( $data, $place_data );

		return $data;
	}

	/**
	 * Adds the logo for the current business.
	 *
	 * @param array  $data       Array with Organization schema data.
	 * @param object $place_data Location data.
	 *
	 * @return array Array with Organization schema data.
	 */
	private function add_logo( $data, $place_data ) {
		$id      = $this->context->canonical . WPSEO_Local_Schema_IDs::ORGANIZATION_LOGO;
		$logo_id = $place_data->business_logo;

		if ( $logo_id === null ) {
			$logo_id = $this->context->company_logo_id;
		}

		$data['logo']  = $this->helpers->schema->image->generate_from_attachment_id( $id, $logo_id, get_the_title() );
		$data['image'] = [ '@id' => $id ];

		return $data;
	}

	/**
	 * Adds attributes to the organization schema data.
	 *
	 * @param array  $data       Array with Organization schema data.
	 * @param object $place_data Location data.
	 *
	 * @return array Array with Organization schema data.
	 */
	private function add_organization_attributes( $data, $place_data ) {
		$organization_attributes = [
			'email'              => 'business_email',
			'telephone'          => 'business_phone',
			'faxNumber'          => 'business_fax',
			'areaServed'         => 'business_area_served',
			'vatID'              => 'business_vat_id',
			'taxID'              => 'business_tax_id',
			'url'                => 'business_url',
		];

		$business_types = new WPSEO_Local_Business_Types_Repository();

		if ( $business_types->is_business_type_child_of( 'LocalBusiness', $place_data->business_type ) ) {
			$organization_attributes['priceRange']         = 'business_price_range';
			$organization_attributes['currenciesAccepted'] = 'business_currencies_accepted';
			$organization_attributes['paymentAccepted']    = 'business_payment_accepted';
		}

		foreach ( $organization_attributes as $attribute => $option_key ) {
			if ( ! empty( $place_data->$option_key ) ) {
				$data[ $attribute ] = $place_data->$option_key;
			}
		}

		return $data;
	}
}

