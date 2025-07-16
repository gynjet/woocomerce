<?php
// Registrar um novo location de menu para o rodapé
add_action( 'after_setup_theme', function() {
    register_nav_menu( 'footer-menu', 'Menu do Rodapé' );
});

