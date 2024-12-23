<?php

/*
Plugin Name: Insecure User Profil SQL injection
Description: Mostrar nombres de usuario de forma insegura.
Version: 1.0
Author: EIT
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode('insecure_get', 'mostrar_nombre_inseguro');

function mostrar_nombre_inseguro() {
    global $wpdb;

    // Obtener el parámetro 'user' de la URL sin sanear
    $user_id = $_GET['user'];

    // Obtener el parámetro 'user' de la URL saneado
    $user_id = isset($_GET['user']) ? intval($_GET['user']) : 0;

    // Consulta SQL insegura
    // $query = "SELECT user_nicename FROM {$wpdb->prefix}users WHERE ID = $user_id";
    
    // Consulta SQL segura con preparación
    $query = $wpdb->prepare("SELECT user_email FROM {$wpdb->prefix}users WHERE ID = %d", $user_id);

    $username = $wpdb->get_var($query);

    if ($username) {
        return "Estás viendo el perfil de ". esc_html($username);
    } else {
        return "Usuario no encontrado";
    }
}
?>


