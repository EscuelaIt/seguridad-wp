<?php
/*
Plugin Name: Demo XSS Plugin
Description: Plugin para demostrar una vulnerabilidad XSS en WordPress.
Version: 1.0
Author: EIT
*/

add_shortcode('xss_demo_form', 'xss_demo_form');
function xss_demo_form() {
    ob_start();
    ?>
    <form method="post">
        <?php wp_nonce_field('xss_demo_form_action', 'xss_demo_form_nonce'); ?>
        <label for="message">Introduce un mensaje</label>
        <input type="text" name="message" id="message" required>
        <button type="submit" name="submit">Enviar</button>
    </form>
    <?php
    if (isset($_POST['submit'])) {
        xss_demo_save_message();
    }
    xss_demo_show_messages();
    return ob_get_clean();
}

// Desa el missatge sense sanititzar-lo
function xss_demo_save_message() {
    global $wpdb;

    // Comprovar nonce
    if (!isset($_POST['xss_demo_form_nonce']) || !wp_verify_nonce($_POST['xss_demo_form_nonce'], 'xss_demo_form_action')) {
        die('Acceso no autorizado');
    }

    if (! is_user_logged_in() ) {
        die('Acceso no autorizado - usuario no logueado');
    }

    // mensaje sin sanear
    // $message = $_POST['message'];

    // Sanear el mensaje
    $message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';

    // query amb insert
    $wpdb->insert("{$wpdb->prefix}xss_demo_messages", ['content' => $message]);  // Insert escapa les dades (segur)

    // query preparada -> segura
    // $wpdb->query(
    //     $wpdb->prepare(
    //         "INSERT INTO {$wpdb->prefix}xss_demo_messages (content) VALUES (%s)",
    //         $message
    //     )
    // );
   
    // query sin validar -> vulnerable a XSS
    // $wpdb->query("INSERT INTO {$wpdb->prefix}xss_demo_messages (content) VALUES ('$message')");
}

// Mostrar los mensajes guardados
function xss_demo_show_messages() {
  global $wpdb;
  $results = $wpdb->get_results("SELECT content FROM {$wpdb->prefix}xss_demo_messages");

  if ($results) {
      echo "<ul>";
      foreach ($results as $row) {
        // sin escapar -> vulnerable a XSS
        // echo "<li>{$row->content}</li>";

        // Escapado seguro para prevenir XSS
          echo "<li>" . esc_html($row->content) . "</li>"; 
      }
      echo "</ul>";
  }
}

// Crear la tabla per guardar los mensajes durante la activación del plugin
register_activation_hook(__FILE__, 'xss_demo_create_table');
function xss_demo_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'xss_demo_messages';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        content text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}



// Eliminar la tabla cuando de deisinstala el plugin
register_uninstall_hook(__FILE__, 'eit_uninstall_insecure');
function eit_uninstall_insecure() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'xss_demo_messages';
    
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
