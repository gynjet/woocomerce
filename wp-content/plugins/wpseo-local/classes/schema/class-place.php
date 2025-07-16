<?php
/**
 * @package WPSEO_Local\Frontend\Schema
 */

use Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;

/**
 * Class WPSEO_Local_JSON_LD
 *
 * Manages the Schema for a Place.
 *
 * @property WPSEO_Schema_Context $context A value object with context variables.
 * @property array                $options Local SEO options.
 */
class WPSEO_Local_Place extends Abstract_Schema_Piece {

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
	public $context;

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
		return ! wpseo_has_multiple_locations() || is_singular( PostType::get_instance()->get_post_type() );
	}

	/**
	 * Generates JSON+LD output for locations.
	 *
	 * @return false|array Array with Place schema data. Returns false no valid location is found.
	 */
	public function generate() {
		return $this->get_place_data();
	}

	/**
	 * Given an array of locations returns Place Schema data for the first.
	 *
	 * @return array|false Place Schema data.
	 */
	public function get_place_data() {
		$repository = new WPSEO_Local_Locations_Repository();
		$location   = $repository->for_current_page();

		// Bail if the $location object is empty.
		if ( ! $location ) {
			return false;
		}

		$data          = [];
		$data['@type'] = 'Place';
		$data['@id']   = $this->context->canonical . WPSEO_Local_Schema_IDs::PLACE_ID;

		// Add Address field.
		$business_address = [];
		if ( ! empty( $location->business_address ) ) {
			$business_address[] = $location->business_address;
		}
		if ( ! empty( $location->business_address_2 ) ) {
			$business_address[] = $location->business_address_2;
		}

		$data['address'] = [
			'@type'           => 'PostalAddress',
			'@id'             => $this->context->canonical . WPSEO_Local_Schema_IDs::ADDRESS_ID,
			'streetAddress'   => ( ! empty( $business_address ) ) ? implode( ' ', $business_address ) : '',
			'addressLocality' => ( ! empty( $location->business_city ) ) ? $location->business_city : '',
			'postalCode'      => ( ! empty( $location->business_zipcode ) ) ? $location->business_zipcode : '',
			'addressRegion'   => ( ! empty( $location->business_state ) ) ? $location->business_state : '',
			'addressCountry'  => ( ! empty( $location->business_country ) ) ? $location->business_country : '',
		];

		// Add coordinates.
		if ( isset( $location->coords ) ) {
			$data['geo'] = [
				'@type'     => 'GeoCoordinates',
				'latitude'  => ( ! empty( $location->coords['lat'] ) ) ? $location->coords['lat'] : '',
				'longitude' => ( ! empty( $location->coords['long'] ) ) ? $location->coords['long'] : '',
			];
		}

		// Add Opening Hours.
		$data['openingHoursSpecification'] = $this->add_opening_hours( $location );

		// Add additional regular fields.
		$standard_fields = [
			'telephone' => 'business_phone',
			'faxNumber' => 'business_fax',
		];

		foreach ( $standard_fields as $data_key => $option_field ) {
			if ( ! empty( $location->$option_field ) ) {
				$data[ $data_key ] = $location->$option_field;
			}
		}

		return $data;
	}

	/**
	 * Calculates the opening hours schema for a location.
	 *
	 * @link https://developers.google.com/search/docs/data-types/local-business
	 * @link https://schema.org/OpeningHoursSpecification
	 *
	 * @param object $location Location data.
	 *
	 * @return array Array with openingHoursSpecification data.
	 */
	private function add_opening_hours( $location ) {
		if ( ! isset( $this->options['hide_opening_hours'] ) || ( isset( $this->options['hide_opening_hours'] ) && $this->options['hide_opening_hours'] !== 'on' ) ) {
			// Force all days to show 24h opening times.
			if ( $this->is_open_247( $location ) ) {
				return $this->opening_hours_247();
			}

			return $this->specific_opening_hours( $location );
		}

		return [];
	}

	/**
	 * Function to determine whether a location is open 24/7 or not.
	 *
	 * @param object $location Location data.
	 *
	 * @return bool False when location is not open 24/7, true when it is.
	 */
	private function is_open_247( $location ) {
		if ( wpseo_has_multiple_locations() ) {
			$open_247 = get_post_meta( $location->post_id, '_wpseo_open_247', true );

			return ( $open_247 === 'on' );
		}

		$open_247 = isset( $this->options['open_247'] ) ? $this->options['open_247'] : '';

		return ( $open_247 === 'on' );
	}

	/**
	 * Returns 24/7 opening hours Schema.
	 *
	 * @return array Array with openingHoursSpecification data.
	 */
	private function opening_hours_247() {
		return [
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ],
			'opens'     => '00:00',
			'closes'    => '23:59',
		];
	}

	/**
	 * Returns 24/7 opening hours Schema.
	 *
	 * @param object $location Location information.
	 *
	 * @return array Array with openingHoursSpecification data.
	 */
	private function specific_opening_hours( $location ) {
		$output                 = [];
		$opening_hours_repo     = new WPSEO_Local_Opening_Hours_Repository();
		$days                   = $opening_hours_repo->get_days();
		$location_opening_hours = [];

		foreach ( $days as $key => $day ) {
			$opening_hours = $opening_hours_repo->get_opening_hours( $key, ( ! empty( $location->post_id ) ? $location->post_id : 'options' ), $this->options, true );

			$opens  = $opening_hours['value_from'];
			$closes = 'closed';

			if ( $opens !== 'closed' ) {
				$closes = ( ( $opening_hours['value_second_to'] !== 'closed' && $opening_hours['use_multiple_times'] === true ) ? $opening_hours['value_second_to'] : $opening_hours['value_to'] );
			}

			if ( $opening_hours['open_24h'] === 'on' ) {
				$location_opening_hours['open_24h']['days'][] = $this->get_day_of_week( $opening_hours['value_abbr'] );
			}

			if ( isset( $location_opening_hours[ $opens . $closes ] ) ) {
				$location_opening_hours[ $opens . $closes ]['days'][] = $this->get_day_of_week( $opening_hours['value_abbr'] );
			}

			if ( ! isset( $location_opening_hours[ $opens . $closes ] ) && $opening_hours['open_24h'] !== 'on' ) {
				$location_opening_hours[ $opens . $closes ] = [
					'opens'  => $opens,
					'closes' => $closes,
					'days'   => [
						$this->get_day_of_week( $opening_hours['value_abbr'] ),
					],
				];
			}
		}

		foreach ( $location_opening_hours as $key => $value ) {
			$day = [
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => $value['days'],
			];
			if ( isset( $value['opens'] ) && $value['opens'] === 'closed' ) {
				$day['opens']  = '00:00';
				$day['closes'] = '00:00';
			}
			elseif ( $key === 'open_24h' ) {
				$day['opens']  = '00:00';
				$day['closes'] = '23:59';
			}
			else {
				$day['opens']  = $value['opens'];
				$day['closes'] = $value['closes'];
			}

			$output[] = $day;
		}

		return $output;
	}

	/**
	 * Returns long day name based on our shortened days of week.
	 *
	 * @param string $day Day of week in short notation.
	 *
	 * @return string Day of week.
	 */
	private function get_day_of_week( $day ) {
		switch ( $day ) {
			case 'Mo':
				return 'Monday';
			case 'Tu':
				return 'Tuesday';
			case 'We':
				return 'Wednesday';
			case 'Th':
				return 'Thursday';
			case 'Fr':
				return 'Friday';
			case 'Sa':
				return 'Saturday';
			case 'Su':
			default:
				return 'Sunday';
		}
	}
}
