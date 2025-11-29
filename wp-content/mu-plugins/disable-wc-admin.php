<?php
/**
 * Plugin Name: Disable WooCommerce Admin
 * Description: Desativa a interface WooCommerce Admin para reduzir sobrecarga no painel.
 */

add_filter(
    'woocommerce_admin_disabled',
    '__return_true'
);
