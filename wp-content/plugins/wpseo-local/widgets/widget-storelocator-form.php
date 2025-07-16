<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * @package WPSEO_Local\Store_Locator
 */

/**
 * Class WPSEO_Storelocator_Form.
 *
 * Handles all store locator functionality.
 */
class WPSEO_Storelocator_Form extends WP_Widget {

	/**
	 * WPSEO_Storelocator_Form constructor.
	 */
	public function __construct() {
		$widget_options = [
			'classname'   => 'WPSEO_Storelocator_Form',
			'description' => __( 'Shows form to search the nearest store. Will submit to the page which contains the store locator.', 'yoast-local-seo' ),
		];

		parent::__construct( false, __( 'WP SEO - Storelocator form', 'yoast-local-seo' ), $widget_options );
	}

	/**
	 * Displays the store locator form.
	 *
	 * @see WP_Widget::widget
	 *
	 * @param array $args     Array of options for this widget.
	 * @param array $instance Instance of the widget.
	 *
	 * @return string|void
	 */
	public function widget( $args, $instance ) {
		$title        = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );
		$search_label = ( ! empty( $instance['search_label'] ) ) ? $instance['search_label'] : apply_filters( 'yoast-local-seo-search-label', __( 'Enter your postal code, city and / or state', 'yoast-local-seo' ) );
		$radius       = ( ! empty( $instance['radius'] ) ) ? $instance['radius'] : 10;
		$page_id      = ( ! empty( $instance['page_id'] ) ) ? $instance['page_id'] : '';

		if ( empty( $page_id ) ) {
			return '';
		}

		if ( isset( $args['before_widget'] ) ) {
			echo $args['before_widget'];
		}

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$asset_manager = new WPSEO_Local_Admin_Assets();

		$asset_manager->enqueue_script( 'store-locator' );
		$asset_manager->enqueue_script( 'google-maps' );

		$default_country = isset( $this->options['default_country'] ) ? WPSEO_Local_Frontend::get_country( WPSEO_Options::get( 'default_country' ) ) : '';
		wp_localize_script( WPSEO_Local_Admin_Assets::PREFIX . 'store-locator', 'storeLocator', [ 'defaultCountry' => $default_country ] );

		$search_string = isset( $_REQUEST['wpseo-sl-search'] ) ? esc_attr( $_REQUEST['wpseo-sl-search'] ) : '';

		?>
		<form action="<?php echo get_permalink( $page_id ); ?>" method="post" id="wpseo-storelocator-form-widget" data-form="wpseo-local-store-locator">
			<fieldset>
				<p>
					<label for="wpseo-sl-search"><?php echo esc_html( $search_label ); ?></label>
					<input type="text" name="wpseo-sl-search" id="wpseo-sl-widget-search" value="<?php echo esc_attr( $search_string ); ?>">
				</p>
				<p class="sl-submit">
					<input type="hidden" name="wpseo-sl-radius" id="wpseo-sl-radius-widget" value="<?php echo esc_attr( $radius ); ?>">
					<input type="hidden" name="wpseo-sl-lat" id="wpseo-sl-lat-widget" value="">
					<input type="hidden" name="wpseo-sl-lng" id="wpseo-sl-lng-widget" value="">
					<input type="submit" value="<?php esc_attr_e( 'Search', 'yoast-local-seo' ); ?>">
				</p>
			</fieldset>
		</form>

		<?php

		if ( isset( $args['after_widget'] ) ) {
			echo $args['after_widget'];
		}

		return '';
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @see WP_Widget::update
	 *
	 * @param array $new_instance New option values for this widget.
	 * @param array $old_instance Old, current option values for this widget.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['title']        = esc_attr( $new_instance['title'] );
		$instance['search_label'] = esc_attr( $new_instance['search_label'] );
		$instance['radius']       = esc_attr( $new_instance['radius'] );
		$instance['page_id']      = esc_attr( $new_instance['page_id'] );

		return $instance;
	}

	/**
	 * Displays the form for the widget options.
	 *
	 * @see WP_Widget::form
	 *
	 * @param array $instance Array with all the (saved) option values.
	 *
	 * @return string
	 */
	public function form( $instance ) {
		$title        = ( ! empty( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : '';
		$search_label = ( ! empty( $instance['search_label'] ) ) ? esc_attr( $instance['search_label'] ) : '';
		$radius       = ( ! empty( $instance['radius'] ) ) ? esc_attr( $instance['radius'] ) : 10;
		$page_id      = ( ! empty( $instance['page_id'] ) ) ? esc_attr( $instance['page_id'] ) : '';

		$options = get_option( 'wpseo_local' )
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'yoast-local-seo' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'search_label' ); ?>"><?php esc_html_e( 'Search label:', 'yoast-local-seo' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'search_label' ); ?>" name="<?php echo $this->get_field_name( 'search_label' ); ?>" type="text" value="<?php echo $search_label; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'radius' ); ?>"><?php esc_html_e( 'Default radius:', 'yoast-local-seo' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'radius' ); ?>" name="<?php echo $this->get_field_name( 'radius' ); ?>" type="number" min="0" value="<?php echo $radius; ?>" /><br>
			<small>
				<?php /* translators: %s translates to the used unit system: km or mi in a <code> tag */ ?>
				<?php echo sprintf( esc_html__( 'Enter the radius in %s to search within.', 'yoast-local-seo' ), '<code>' . ( ( $options['unit_system'] === 'METRIC' ) ? 'km' : 'mi' ) . '</code>' ); ?>
				<?php esc_html_e( 'This field will be a hidden field and is only used for calculation.', 'yoast-local-seo' ); ?>
			</small>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'page_id' ); ?>"><?php esc_html_e( 'Select the page where your store locator shortcode is added. This is the page where the form is submitted to.', 'yoast-local-seo' ); ?>:</label><br>
			<?php
			$args = [
				'name'             => $this->get_field_name( 'page_id' ),
				'id'               => $this->get_field_id( 'page_id' ),
				'class'            => 'widefat',
				'selected'         => $page_id,
				'show_option_none' => __( 'Select a page', 'yoast-local-seo' ),
			];
			wp_dropdown_pages( $args );
			?>
		</p>
		<?php

		return '';
	}
}
