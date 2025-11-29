<?php
/*
Plugin Name: GYNJET Shop Tweaks
*/
add_filter('woocommerce_product_subcategories', function($html){
  if ( is_product_category(array('tintas-epson','tintas-hp','tintas-canon','kits-de-tinta','fluidos-e-solucoes')) ) return '';
  return $html;
},10);
add_filter('woocommerce_product_subcategories_args', function($args){
  if ( is_product_category('tintas') ) $args['hide_empty']=0;
  return $args;
},10);
