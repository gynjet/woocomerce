<?php
/**
 * Yoast SEO: Local for WooCommerce plugin file.
 *
 * @package YoastSEO_Local_WooCommerce
 */

/**
 * Class: Yoast_WCSEO_Local_Transport.
 */
class Yoast_WCSEO_Local_Transport {

	public function init() {
		add_action( 'admin_menu', [ $this, 'register_submenu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );
	}

	public function admin_styles() {

		if ( get_current_screen()->id === 'woocommerce_page_yoast_wcseo_local_transport' ) {
			wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', [], WC_VERSION );
			wp_enqueue_style( 'woocommerce_admin_styles' );
		}
	}

	public function register_submenu() {
		add_submenu_page(
			'woocommerce',
			__( 'Transport', 'yoast-local-seo' ),
			__( 'Transport', 'yoast-local-seo' ),
			'manage_options',
			'yoast_wcseo_local_transport',
			[ $this, 'menu_callback' ]
		);
	}

	public function menu_callback() {
		echo '<h3>' . esc_html__( 'Transport', 'yoast-local-seo' ) . '</h3>';
		/* translators: transport-page-description-text = a container for placing an explanatory text for the Transport page, it elaborates on what the page is actually for */
		echo '<p>' . esc_html__( 'transport-page-description-text', 'yoast-local-seo' ) . '</p>';

		$list = new Yoast_WCSEO_Local_Transport_List();
		$list->prepare_items();
		$list->items = $this->get_transport_items();
		usort( $list->items, [ $list, 'usort_reorder' ] );
		$list->display();
	}

	public function get_transport_items() {
		global $wpdb;

		$query = "
			SELECT p.*
			FROM wp_woocommerce_order_itemmeta woim
			LEFT JOIN wp_woocommerce_order_items woi ON woi.order_item_id = woim.order_item_id
			LEFT JOIN wp_posts p ON p.ID = woi.order_id
			WHERE ( p.post_status = 'wc-processing' OR p.post_status = 'wc-transporting' OR p.post_status = 'wc-ready-for-pickup' )
			AND woim.meta_key = 'method_id'
			AND woim.meta_value LIKE 'yoast_wcseo_local_pickup%';
		";

		// phpcs:ignore WordPress.WP.PreparedSQL.NotPrepared
		return $wpdb->get_results( $query ); // @codingStandardsIgnoreLine
	}
}
