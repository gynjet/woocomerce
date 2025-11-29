<?php
/**
 * Plugin Name: GYNJET Shop Tweaks
 * Description: Ajustes leves para categorias da loja sem alterar lógica de negócio.
 */

add_filter(
    'woocommerce_product_subcategories',
    function ( $html ) {
        // Evita exibir subcategorias em categorias de tinta específicas.
        if (
            is_product_category(
                array( 'tintas-epson', 'tintas-hp', 'tintas-canon', 'kits-de-tinta', 'fluidos-e-solucoes' )
            )
        ) {
            return '';
        }

        return $html;
    },
    10
);

add_filter(
    'woocommerce_product_subcategories_args',
    function ( $args ) {
        // Exibe subcategorias vazias dentro de "tintas" para facilitar navegação.
        if ( is_product_category( 'tintas' ) ) {
            $args['hide_empty'] = 0;
        }

        return $args;
    },
    10
);
