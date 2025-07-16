<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package WPSEO_Local\Admin
 */

/**
 * This class holds the assets for Yoast Local SEO.
 */
class WPSEO_Local_Admin_Assets extends WPSEO_Admin_Asset_Manager {

	/**
	 * Prefix for naming the assets.
	 *
	 * @var string
	 */
	const PREFIX = 'wp-seo-local-';

	/**
	 * Child constructor for WPSEO_Local_Admin_Assets
	 */
	public function __construct() {
		parent::__construct( new WPSEO_Admin_Asset_SEO_Location( WPSEO_LOCAL_FILE ), self::PREFIX );
	}

	/**
	 * Returns the scripts that need to be registered.
	 *
	 * @return array Scripts that need to be registered.
	 */
	protected function scripts_to_be_registered() {
		$flat_version = $this->flatten_version( WPSEO_LOCAL_VERSION );

		return [
			[
				'name' => 'commons-bundle',
				'src'  => self::PREFIX . 'vendor-' . $flat_version,
			],
			[
				'name'    => 'frontend',
				'src'     => self::PREFIX . 'frontend-' . $flat_version,
				'deps'    => [ 'jquery', self::PREFIX . 'commons-bundle' ],
			],
			[
				'name'      => 'google-maps',
				'src'       => $this->get_google_maps_url(),
				'in_footer' => true,
			],
			[
				'name'      => 'seo-locations',
				'src'       => self::PREFIX . 'analysis-locations-' . $flat_version,
				'in_footer' => true,
			],
			[
				'name'      => 'seo-pages',
				'src'       => self::PREFIX . 'analysis-pages-' . $flat_version,
				'in_footer' => true,
			],
			[
				'name'      => 'global-script',
				'src'       => self::PREFIX . 'global-' . $flat_version,
				'deps'      => [ 'jquery', self::PREFIX . 'commons-bundle' ],
				'in_footer' => true,
			],
			[
				'name'      => 'geocoding-repository',
				'src'       => self::PREFIX . 'geocoding-repository-' . $flat_version,
				'deps'      => [ 'jquery', self::PREFIX . 'commons-bundle' ],
				'in_footer' => true,
			],
			[
				'name'      => 'locations',
				'src'       => self::PREFIX . 'locations-' . $flat_version,
				'deps'      => [ 'wp-polyfill' ],
				'in_footer' => true,
			],
			[
				'name'      => 'store-locator',
				'src'       => self::PREFIX . 'store-locator-' . $flat_version,
				'deps'      => [ 'wp-polyfill' ],
				'in_footer' => true,
			],
			[
				'name'    => 'checkout',
				'src'     => self::PREFIX . 'checkout-' . $flat_version,
				'deps'    => [ 'jquery' ],
			],
			[
				'name'    => 'shipping-settings',
				'src'     => self::PREFIX . 'shipping-settings-' . $flat_version,
				'deps'    => [ 'jquery' ],
			],
			[
				'name'    => 'settings',
				'src'     => self::PREFIX . 'settings-' . $flat_version,
				'deps'    => [ WPSEO_Admin_Asset_Manager::PREFIX . 'settings' ],
			],
			[
				'name'    => 'blocks',
				'src'     => self::PREFIX . 'blocks-' . $flat_version,
				'deps'    => [ 'wp-blocks', 'wp-i18n', 'wp-element' ],
			],
		];
	}

	/**
	 * Returns the styles that need to be registered.
	 *
	 * @todo Data format is not self-documenting. Needs explanation inline. R.
	 *
	 * @return array Styles that need to be registered.
	 */
	protected function styles_to_be_registered() {
		$flat_version = $this->flatten_version( WPSEO_LOCAL_VERSION );

		return [
			[
				'name' => 'admin-css',
				'src'  => 'admin-' . $flat_version,
				'rtl'  => false,
			],
		];
	}

	/**
	 * Get the Google Maps external library URL.
	 *
	 * @return string
	 */
	private function get_google_maps_url() {
		$google_maps_url = 'https://maps.google.com/maps/api/js';
		$api_repository  = new WPSEO_Local_Api_Keys_Repository();

		$api_key    = $api_repository->get_api_key( 'browser' );
		$query_args = [];
		if ( ! empty( $api_key ) ) {
			$query_args['key'] = $api_key;
		}

		// Load Maps API script.
		$locale = get_locale();
		$locale = explode( '_', $locale );

		$multi_country_locales = [
			'en',
			'de',
			'es',
			'it',
			'pt',
			'ro',
			'ru',
			'sv',
			'nl',
			'zh',
			'fr',
		];

		// Check if it might be a language spoken in more than one country.
		if ( isset( $locale[1] ) && in_array( $locale[0], $multi_country_locales, true ) ) {
			$language = $locale[0] . '-' . $locale[1];
		}

		if ( ! isset( $language ) ) {
			$language = ( isset( $locale[1] ) ? $locale[1] : $locale[0] );
		}

		if ( isset( $language ) ) {
			$query_args['language'] = esc_attr( strtolower( $language ) );
		}

		if ( ! empty( $query_args ) ) {
			$google_maps_url = add_query_arg( $query_args, $google_maps_url );
		}

		return $google_maps_url;
	}
}
