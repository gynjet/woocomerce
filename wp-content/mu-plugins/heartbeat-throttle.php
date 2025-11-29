<?php
/**
 * Plugin Name: Heartbeat Throttle
 * Description: Aumenta o intervalo do Heartbeat no admin para reduzir consumo de recursos.
 */

add_filter(
    'heartbeat_settings',
    function ( $settings ) {
        // Intervalo de 60s no painel para aliviar carga em hospedagens compartilhadas.
        $settings['interval'] = 60;

        return $settings;
    },
    10,
    1
);
