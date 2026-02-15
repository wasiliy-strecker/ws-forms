<?php
/**
 * Plugin Name: Wasiliy Strecker Forms (WS Forms)
 * Description: Wasiliy Strecker Forms Model View Controller. Experimental WordPress plugin with various features for testing purposes. The architecture generally follows a TYPO3 extension structure.
 * Author: Wasiliy Strecker
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

// 1. Autoloader (Muss als Erstes kommen)
spl_autoload_register(function ($class) {
    $prefix = 'Ws\\WsForms\\';
    $base_dir = __DIR__ . '/Classes/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

/**
 * 2. Initialisierung
 * Wir rufen register_services auf 'plugins_loaded' auf.
 * Das ist der Standard für Plugins, um Hooks anzumelden.
 */
add_action('plugins_loaded', function() {
    // Hier werden nur add_action() und add_shortcode() definiert.
    // Noch keine Controller-Instanzen erstellen!
    \Ws\WsForms\Init::register_services();
});

/**
 * 3. Aktivierung
 * Da wir einen Autoloader haben, brauchen wir das manuelle require_once nicht mehr,
 * solange der Klassenname korrekt aufgelöst wird.
 */
register_activation_hook(__FILE__, [ \Ws\WsForms\Init::class, 'activate' ]);