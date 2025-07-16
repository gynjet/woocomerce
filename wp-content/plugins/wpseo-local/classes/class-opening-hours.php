<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package WPSEO_Local\Main
 */

if ( ! class_exists( 'WPSEO_Local_Opening_Hours_Repository' ) ) {

	/**
	 * Class WPSEO_Local_Opening_Hours_Repository
	 *
	 * This class handles the querying of all locations
	 */
	class WPSEO_Local_Opening_Hours_Repository {

		/**
		 * Contains array for days with its translations and notations.
		 *
		 * @var LimitIterator
		 */
		protected $days;

		/**
		 * WPSEO_Local_Opening_Hours_Repository constructor.
		 */
		public function __construct() {
			$this->run();
		}

		/**
		 * Runs default actions when instantiating the class.
		 */
		public function run() {
			$this->set_days();
		}

		/**
		 * Set property Days.
		 */
		private function set_days() {
			$day_labels = [
				'sunday'    => __( 'Sunday', 'yoast-local-seo' ),
				'monday'    => __( 'Monday', 'yoast-local-seo' ),
				'tuesday'   => __( 'Tuesday', 'yoast-local-seo' ),
				'wednesday' => __( 'Wednesday', 'yoast-local-seo' ),
				'thursday'  => __( 'Thursday', 'yoast-local-seo' ),
				'friday'    => __( 'Friday', 'yoast-local-seo' ),
				'saturday'  => __( 'Saturday', 'yoast-local-seo' ),
			];

			$days       = new ArrayIterator( $day_labels );
			$days       = new InfiniteIterator( $days );
			$this->days = new LimitIterator( $days, get_option( 'start_of_week' ), 7 );
		}

		/**
		 * Returns an array of days.
		 *
		 * @return array
		 */
		public function get_days() {
			return iterator_to_array( $this->days );
		}

		/**
		 * @todo Passing through the $post_id should be solved in a nicer way,
		 * since when using a single-location setup, it doesn't need a post ID.
		 *
		 * @param string          $day        Lowercase key of the day (in english).
		 * @param int|string|null $post_id    Use 'option' when using single-location setup.
		 *                                    Use the Post ID (int) when using multiple locations setup.
		 * @param array           $options    Optional options array.
		 * @param bool|null       $format_24h Whether or not 24-hour time format should be used.
		 *
		 * @return array Array of opening hours in all needed formats.
		 */
		public function get_opening_hours( $day, $post_id = null, $options = [], $format_24h = null ) {
			if ( wpseo_has_multiple_locations() ) {
				if ( $post_id === null ) {
					$post_id = get_the_ID();
				}

				$field_name        = '_wpseo_opening_hours_' . $day;
				$value_from        = get_post_meta( $post_id, $field_name . '_from', true );
				$value_to          = get_post_meta( $post_id, $field_name . '_to', true );
				$value_second_from = get_post_meta( $post_id, $field_name . '_second_from', true );
				$value_second_to   = get_post_meta( $post_id, $field_name . '_second_to', true );
				$open_24h          = get_post_meta( $post_id, $field_name . '_24h', true );
			}
			else {
				$field_name        = 'opening_hours_' . $day;
				$value_from        = isset( $options[ $field_name . '_from' ] ) ? esc_attr( $options[ $field_name . '_from' ] ) : '';
				$value_to          = isset( $options[ $field_name . '_to' ] ) ? esc_attr( $options[ $field_name . '_to' ] ) : '';
				$value_second_from = isset( $options[ $field_name . '_second_from' ] ) ? esc_attr( $options[ $field_name . '_second_from' ] ) : '';
				$value_second_to   = isset( $options[ $field_name . '_second_to' ] ) ? esc_attr( $options[ $field_name . '_second_to' ] ) : '';
				$open_24h          = isset( $options[ $field_name . '_24h' ] ) ? esc_attr( $options[ $field_name . '_24h' ] ) : '';
			}

			$value_from_formatted        = $value_from;
			$value_to_formatted          = $value_to;
			$value_second_from_formatted = $value_second_from;
			$value_second_to_formatted   = $value_second_to;

			if ( $format_24h !== true ) {
				$value_from_formatted        = gmdate( 'g:i A', strtotime( $value_from ) );
				$value_to_formatted          = gmdate( 'g:i A', strtotime( $value_to ) );
				$value_second_from_formatted = gmdate( 'g:i A', strtotime( $value_second_from ) );
				$value_second_to_formatted   = gmdate( 'g:i A', strtotime( $value_second_to ) );
			}

			if ( wpseo_has_multiple_locations() === true ) {
				$multiple_opening_hours = get_post_meta( $post_id, '_wpseo_multiple_opening_hours', true );
				$use_multiple_times     = ! empty( $multiple_opening_hours );
			}
			else {
				$use_multiple_times = isset( $options['multiple_opening_hours'] ) && $options['multiple_opening_hours'] === 'on';
			}

			if ( wpseo_check_falses( $open_24h ) ) {
				$value_from_formatted        = '00:00';
				$value_to_formatted          = '23:59';
				$value_second_from_formatted = false;
				$value_second_to_formatted   = false;
			}

			return [
				'value_abbr'                  => ucfirst( substr( $day, 0, 2 ) ),
				'value_from'                  => $value_from,
				'value_to'                    => $value_to,
				'value_second_from'           => $value_second_from,
				'value_second_to'             => $value_second_to,
				'value_from_formatted'        => $value_from_formatted,
				'value_to_formatted'          => $value_to_formatted,
				'value_second_from_formatted' => $value_second_from_formatted,
				'value_second_to_formatted'   => $value_second_to_formatted,
				'use_multiple_times'          => $use_multiple_times,
				'open_24h'                    => $open_24h,
			];
		}
	}
}
