<?php

// ocultar versión WP
function eit_remove_version() {
  return '';
  } add_filter('the_generator', 'eit_remove_version');


  remove_action('wp_head', 'wp_generator');

// ocultar versión WP en CSS/JS

  function eit_remove_wp_version_from_scripts( $src ) {
    if ( strpos( $src, 'ver=' ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}
add_filter( 'style_loader_src', 'eit_remove_wp_version_from_scripts' );
add_filter( 'script_loader_src', 'eit_remove_wp_version_from_scripts' );