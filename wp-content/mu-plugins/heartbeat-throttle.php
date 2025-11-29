<?php
add_filter('heartbeat_settings', function($s){ $s['interval']=60; return $s; }); // 60s no admin
