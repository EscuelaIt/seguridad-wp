<?php

// renombrar wp-content
define('WP_CONTENT_DIR', dirname(__FILE__) . '/contenido');
define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/contenido' );

// deshabilitar edición en temes clásicos
define( 'DISALLOW_FILE_EDIT', true );

// ocultar log de errores
if ( ! defined( 'WP_DEBUG' ) ) {
  define( 'WP_DEBUG', true );
}
define ('WP_DEBUG_LOG', true);
define ('WP_DEBUG_DISPLAY', false);