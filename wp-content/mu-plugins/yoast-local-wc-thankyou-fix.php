<?php
/**
 * Plugin Name: Yoast Local WC Thankyou Fix
 * Description: Preenche métodos de envio na sessão em /order-received para evitar avisos do wpseo-local.
 */

add_action(
    'template_redirect',
    function () {
        if ( ! function_exists( 'is_order_received_page' ) || ! is_order_received_page() ) {
            return;
        }

        if ( ! function_exists( 'WC' ) || ! WC()->session ) {
            return;
        }

        // Se já há métodos escolhidos, não faz nada.
        $chosen = WC()->session->get( 'chosen_shipping_methods' );
        if ( is_array( $chosen ) && ! empty( $chosen ) ) {
            return;
        }

        // Preenche a sessão a partir do pedido (página de obrigado).
        $order_id = absint( get_query_var( 'order-received' ) );
        if ( ! $order_id ) {
            return;
        }

        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return;
        }

        $methods = [];
        foreach ( $order->get_shipping_methods() as $item ) {
            $id  = $item->get_method_id(); // ex.: flat_rate
            $iid = $item->get_instance_id(); // ex.: 3
            $methods[] = $iid ? "{$id}:{$iid}" : "{$id}";
        }

        if ( ! empty( $methods ) ) {
            WC()->session->set( 'chosen_shipping_methods', $methods );
        }
    }
);
