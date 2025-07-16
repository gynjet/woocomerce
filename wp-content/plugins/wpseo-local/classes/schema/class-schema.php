<?php
/**
 * @package WPSEO_Local\Frontend\Schema
 */

/**
 * Class WPSEO_Local_JSON_LD.
 *
 * Manages the Schema.
 *
 * @property WPSEO_Schema_Context $context A value object with context variables.
 * @property array                $options Local SEO options.
 */
class WPSEO_Local_Schema {

	/**
	 * Stores the options for this plugin.
	 *
	 * @var array
	 */
	public $options = [];

	/**
	 * A value object with context variables.
	 *
	 * @var WPSEO_Schema_Context
	 */
	private $context;

	/**
	 * WPSEO_Local_JSON_LD constructor.
	 */
	public function __construct() {
		$this->options = get_option( 'wpseo_local' );

		add_filter( 'wpseo_schema_graph_pieces', [ $this, 'add_graph_piece' ], 11, 2 );
		add_filter( 'wpseo_schema_organization', [ $this, 'filter_organization_data' ] );
	}

	/**
	 * Adds the graph pieces to the Schema Graph.
	 *
	 * @param array                $pieces  Array of Graph pieces.
	 * @param WPSEO_Schema_Context $context A value object with context variables.
	 *
	 * @return array Array of Graph pieces.
	 */
	public function add_graph_piece( $pieces, WPSEO_Schema_Context $context ) {
		$this->context = $context;

		$pieces[] = new WPSEO_Local_Place( $context );
		$pieces[] = new WPSEO_Local_Organization( $context );
		$pieces[] = new WPSEO_Local_Organization_List( $context );

		return $pieces;
	}

	/**
	 * Adds data to the Organization Schema output.
	 *
	 * @param array $data Organization Schema data.
	 *
	 * @return array Organization Schema data.
	 */
	public function filter_organization_data( $data ) {
		if ( ! wpseo_has_multiple_locations() ) {
			return $this->single_location_data( $data );
		}

		return $data;
	}

	/**
	 * Returns the Organization Schema for a single location setup.
	 *
	 * @param array $data Organization Schema data.
	 *
	 * @return array Organization Schema data.
	 */
	private function single_location_data( $data ) {
		if ( ! is_array( $data['@type'] ) ) {
			$data['@type'] = [ $data['@type'] ];
		}
		$data['@type'][] = 'Place';
		if ( ! empty( $this->options['business_type'] ) ) {
			array_push( $data['@type'], $this->options['business_type'] );
		}

		$data['location'] = [ '@id' => $this->context->canonical . WPSEO_Local_Schema_IDs::PLACE_ID ];
		$data['address']  = [ '@id' => $this->context->canonical . WPSEO_Local_Schema_IDs::ADDRESS_ID ];

		$organization_attributes = [
			'email'      => 'location_email',
			'telephone'  => 'location_phone',
			'faxNumber'  => 'location_fax',
			'areaServed' => 'location_area_served',
			'vatID'      => 'location_vat_id',
			'taxID'      => 'location_tax_id',
		];

		$business_types = new WPSEO_Local_Business_Types_Repository();
		if ( $business_types->is_business_type_child_of( 'LocalBusiness', $this->options['business_type'] ) ) {
			$organization_attributes['priceRange']         = 'location_price_range';
			$organization_attributes['currenciesAccepted'] = 'location_currencies_accepted';
			$organization_attributes['paymentAccepted']    = 'location_payment_accepted';
		}

		foreach ( $organization_attributes as $attribute => $option_key ) {
			if ( ! empty( $this->options[ $option_key ] ) ) {
				$data[ $attribute ] = $this->options[ $option_key ];
			}
		}

		return $data;
	}
}

