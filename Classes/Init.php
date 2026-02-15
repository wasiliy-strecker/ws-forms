<?php
namespace Ws\WsForms;

use Ws\WsForms\Controller\UserController;
use Ws\WsForms\Controller\AddressController;

class Init {

    /**
     * @var \Ws\WsForms\Domain\Model\Base
     */
    protected $base = null;

    public function __construct() {
        $this->base = new \Ws\WsForms\Domain\Model\Base();
    }


    public static function register_services() {
        $instance = new self();

        // Sprachdateien beim 'init' Hook laden
        add_action('init', [$instance, 'loadTextDomain']);

        // REST API IMMER registrieren (außerhalb von is_admin)
        add_action('rest_api_init', [$instance, 'registerRestRoutes']);

        if (is_admin()) {
            add_action('admin_enqueue_scripts', [$instance, 'enqueueBackendAssets']);
            add_action('admin_menu', [$instance, 'addAdminMenu']);
        }else{
            add_action('wp_enqueue_scripts', [$instance, 'enqueueFrontendAssets']);
            // Shortcode: Wir übergeben nur den Namen der Runner-Methode
            add_shortcode('ws_forms', [self::class, 'runWsFormsShortcode']);
            // Der spezifische Login-Shortcode
            add_shortcode('ws_login', [self::class, 'runWsLoginShortcode']);
            // Shortcode: Wir übergeben nur den Namen der Runner-Methode
            add_shortcode('ws_products', [self::class, 'runWsProductsShortcode']);
            // Shortcode für Bestellungen
            add_shortcode('ws_order', [self::class, 'runWsOrderShortcode']);
        }

    }

    /**
     * Lädt die Übersetzungen aus dem /languages Ordner
     */
    public function loadTextDomain() {
        load_plugin_textdomain(
            'ws-forms',
            false,
            // Pfad relativ zum Plugin-Stammverzeichnis
            dirname(plugin_basename(dirname(__FILE__, 2))) . '/languages/'
        );
    }

    /**
     * Der Shortcode-Runner
     */
    public static function runWsFormsShortcode($atts = []) {
        $userController = new \Ws\WsForms\Controller\UserController();
        ob_start();
        echo $userController->initAction($atts);
        return ob_get_clean();
    }

    /**
     * Der Shortcode-Runner
     */
    public static function runWsProductsShortcode($atts = []) {
        $productController = new \Ws\WsForms\Controller\ProductController();
        ob_start();
        echo $productController->initAction($atts);
        return ob_get_clean();
    }

    /**
     * Runner für [ws_order]
     */
    public static function runWsOrderShortcode($atts = []) {
        $orderController = new \Ws\WsForms\Controller\OrderController();
        ob_start();
        // Wir rufen showAction manuell auf, da initAction standardmäßig listAction macht
        if (isset($_GET['order_id'])) {
            // Wir simulieren einen WP_REST_Request oder übergeben die ID anders
            // Da showAction im Controller eine WP_REST_Request erwartet, passen wir sie ggf. an
            // oder nutzen eine Hilfsmethode.
            echo $orderController->showAction();
        } else {
            echo $orderController->listAction();
        }
        return ob_get_clean();
    }

    /**
     * Runner für [ws_login]
     */
    public static function runWsLoginShortcode($atts = []) {
        $userController = new \Ws\WsForms\Controller\UserController();
        ob_start();
        echo $userController->loginAction(['type' => 'login']);
        return ob_get_clean();
    }

    public static function run_controller($atts = []) {
        // Der Controller wird gestartet, wenn der Shortcode im Beitrag gefunden wird
        $controller = new \Ws\WsForms\Controller\UserController();

        // Wichtig: Shortcodes müssen den Inhalt per 'return' zurückgeben, nicht per 'echo'!
        return $controller->initAction();
    }

    protected function enqueueScript($handle, $path, $deps = []) {
        // Falls diese Datei in Classes/ liegt, brauchen wir dirname()
        $plugin_url = plugin_dir_url(dirname(__FILE__));

        wp_enqueue_script(
            $handle,
            $plugin_url . 'Resources/Public/Js/' . $path,
            $deps,
            '1.0.0',
            true
        );
    }

    /**
     * Zentrale Registrierung aller JavaScript-Abhängigkeiten
     */
    private function registerCommonScripts() {

        $this->enqueueScript('wsf_init_js', 'Init.js', ['jquery']);
        $this->enqueueScript('wsf_controller_user_new_js', 'Controller/User/New.js', ['wsf_init_js']);
        $this->enqueueScript('wsf_controller_user_edit_js', 'Controller/User/Edit.js', ['wsf_init_js']);
        $this->enqueueScript('wsf_controller_user_list_js', 'Controller/User/List.js', ['wsf_init_js']);
        $this->enqueueScript('wsf_controller_user_login_js', 'Controller/User/Login.js', ['wsf_init_js']);
        $this->enqueueScript('wsf_controller_user_functions_js', 'Controller/User/Functions.js', ['wsf_init_js']);
        $this->enqueueScript('wsf_controller_user_events_js', 'Controller/User/Events.js', ['wsf_init_js']);

        $this->enqueueScript('wsf_controller_option_edit_js', 'Controller/Option/Edit.js', ['wsf_init_js']);
        $this->enqueueScript('wsf_controller_option_events_js', 'Controller/Option/Events.js', ['wsf_init_js']);

        $this->enqueueScript('wsf_controller_product_new_js', 'Controller/Product/New.js', ['wsf_init_js']);
        $this->enqueueScript('wsf_controller_product_edit_js', 'Controller/Product/Edit.js', ['wsf_init_js']);
        $this->enqueueScript('wsf_controller_product_events_js', 'Controller/Product/Events.js', ['wsf_init_js']);
        $this->enqueueScript('wsf_controller_product_functions_js', 'Controller/Product/Functions.js', ['wsf_init_js']);

        $this->enqueueScript('wsf_controller_order_events_js', 'Controller/Order/Events.js', ['wsf_init_js']);
        $this->enqueueScript('wsf_controller_order_functions_js', 'Controller/Order/Functions.js', ['wsf_init_js']);

        $this->enqueueScript('wsf_events_js', 'Events.js', ['wsf_init_js']);
        $this->enqueueScript('wsf_functions_js', 'Functions.js', ['wsf_init_js']);
        $this->enqueueScript('wsf_validation_js', 'Validation.js', ['wsf_init_js']);

        wp_localize_script('wsf_init_js', 'wsf_rest', [
            'api_url' => esc_url_raw(rest_url('ws-forms/v1')),
            'nonce'   => wp_create_nonce('wp_rest')
        ]);


    }

    /**
     * Enqueue Assets für das WordPress Backend
     */
    public function enqueueBackendAssets($hook) {
        // Nur laden, wenn wir uns auf einer Plugin-Seite befinden
        if (strpos($hook, 'ws_forms') === false) {
            return;
        }

        // Gemeinsame JS-Logik laden
        $this->registerCommonScripts();

        // WordPress Media Library laden
        wp_enqueue_media();

        // Backend-spezifisches CSS
        wp_enqueue_style(
            'wsf_main_backend_style',
            plugin_dir_url(dirname(__FILE__)) . 'Resources/Public/Css/Backend/Style.css',
            [],
            '1.0.0'
        );
    }

    /**
     * Enqueue Assets für das Frontend
     */
    public function enqueueFrontendAssets() {
        global $post;

        // Sicherheits-Check: Nur wenn wir uns in einem echten Post/Page befinden
        if (!is_a($post, 'WP_Post')) {
            return;
        }

        // Prüfen, ob einer unserer Shortcodes im Inhalt vorkommt
        $hasShortcode = has_shortcode($post->post_content, 'ws_forms') ||
            has_shortcode($post->post_content, 'ws_products') ||
            has_shortcode($post->post_content, 'ws_login');

        if (!$hasShortcode) {
            return;
        }

        // 1. Dashicons für deine Icons im Frontend laden
        wp_enqueue_style('dashicons');

        // Stripe SDK laden
        wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', [], null, true);

        $this->registerCommonScripts();

        wp_enqueue_style(
            'wsf_main_frontend_style',
            plugin_dir_url(dirname(__FILE__)) . 'Resources/Public/Css/Frontend/Style.css',
            [],
            '1.0.0'
        );
    }

    /**
     * Zentrale Berechtigungsprüfung (CamelCase)
     */
    public function checkPermission() {
        if($this->base->getIsAdmin()){
            return current_user_can('manage_options');
        }else{
            return true;
        }
    }

    public function registerRestRoutes() {

        $modules = ['user', 'address', 'product', 'order'];

        foreach ($modules as $module) {
            $controllerClass = '\\Ws\\WsForms\\Controller\\' . ucfirst($module) . 'Controller';

            if (!class_exists($controllerClass)) {
                continue;
            }

            $controller = new $controllerClass();

            // --- NEU: LOGIN ROUTE (Spezifisch für User-Modul) ---
            if ($module === 'user') {
                register_rest_route('ws-forms/v1', '/user/login', [
                    'methods'  => 'POST',
                    'callback' => [$controller, 'loginCreateAction'],
                    // WICHTIG: Erlaubt den Zugriff für nicht-eingeloggte User
                    'permission_callback' => '__return_true',
                ]);
            }

            register_rest_route('ws-forms/v1', '/' . $module . '/list', [
                'methods'  => 'GET',
                'callback' => [$controller, 'listAction'],
                'permission_callback' => [$this, 'checkPermission'],
            ]);

            // 1. CREATE -> /ws-forms/v1/user/create
            register_rest_route('ws-forms/v1', '/' . $module . '/create', [
                'methods'  => 'POST',
                'callback' => [$controller, 'createAction'],
                'permission_callback' => [$this, 'checkPermission'],
            ]);

            // 2. UPDATE -> /ws-forms/v1/user/update/123
            register_rest_route('ws-forms/v1', '/' . $module . '/update/(?P<id>\d+)', [
                'methods'  => 'POST',
                'callback' => [$controller, 'updateAction'],
                'permission_callback' => [$this, 'checkPermission'],
                'args' => [
                    'id' => ['validate_callback' => function($param) { return is_numeric($param); }]
                ]
            ]);

            // 3. DELETE -> /ws-forms/v1/user/delete/123
            register_rest_route('ws-forms/v1', '/' . $module . '/delete/(?P<id>\d+)', [
                'methods'  => 'DELETE',
                'callback' => [$controller, 'deleteAction'],
                'permission_callback' => [$this, 'checkPermission'],
                'args' => [
                    'id' => ['validate_callback' => function($param) { return is_numeric($param); }]
                ]
            ]);

            if($module === 'user'){
                // Route für Email-Check -> /ws-forms/v1/user/check-email
                register_rest_route('ws-forms/v1', '/' . $module . '/check-email', [
                    'methods'  => 'GET',
                    'callback' => [$controller, 'checkEmailAction'],
                    'permission_callback' => [$this, 'checkPermission'],
                ]);
            }
            if($module === 'product'){
                // Route für Email-Check -> /ws-forms/v1/product/check-sku
                register_rest_route('ws-forms/v1', '/' . $module . '/check-sku', [
                    'methods'  => 'GET',
                    'callback' => [$controller, 'checkSkuAction'],
                    'permission_callback' => [$this, 'checkPermission'],
                ]);

                // Route für AI-Analyse
                register_rest_route('ws-forms/v1', '/' . $module . '/ai-analyze', [
                    'methods'  => 'POST',
                    'callback' => [$controller, 'aiAnalyzeAction'],
                    'permission_callback' => [$this, 'checkPermission'],
                ]);
            }
            if($module === 'order'){
                register_rest_route('ws-forms/v1', '/' . $module . '/get-stripe-intent', [
                    'methods'  => 'POST',
                    'callback' => [$controller, 'getStripeIntentAction'],
                    'permission_callback' => [$this, 'checkPermission'],
                ]);
                register_rest_route('ws-forms/v1', '/' . $module . '/show/(?P<id>\d+)', [
                    'methods'  => 'GET',
                    'callback' => [$controller, 'showAction'],
                    'permission_callback' => [$this, 'checkPermission'],
                    'args' => [
                        'id' => ['validate_callback' => function($param) { return is_numeric($param); }]
                    ]
                ]);
            }
        }

        $optionControllerClass = '\\Ws\\WsForms\\Controller\\OptionController';

        $optionController = new $optionControllerClass();

        register_rest_route('ws-forms/v1', '/option/update', [
            'methods'  => 'POST',
            'callback' => [$optionController, 'updateAction'],
            'permission_callback' => [$this, 'checkPermission'],
            'args' => [
                'id' => ['validate_callback' => function($param) { return is_numeric($param); }]
            ]
        ]);

    }


    public function addAdminMenu() {
        // 1. Der Haupt-Eintrag (Container)
        // Wir nutzen hier eine statische Methode 'runUserController' als Callback
        add_menu_page(
            'WsForms',
            'WsForms',
            'manage_options',
            'ws_forms_main',
            [self::class, 'runUserController'],
            'dashicons-feedback',
            20
        );

        // 2. Unterpunkt: Users (zeigt auf den gleichen Controller-Runner)
        add_submenu_page(
            'ws_forms_main',
            'Users',
            'Users',
            'manage_options',
            'ws_forms_users',
            [self::class, 'runUserController']
        );

        // 3. Unterpunkt: Addresses (zeigt auf einen anderen Runner)
        add_submenu_page(
            'ws_forms_main',
            'Addresses',
            'Addresses',
            'manage_options',
            'ws_forms_addresses',
            [self::class, 'runAddressController']
        );

        // 3. Unterpunkt: Addresses (zeigt auf einen anderen Runner)
        add_submenu_page(
            'ws_forms_main',
            'Products',
            'Products',
            'manage_options',
            'ws_forms_products',
            [self::class, 'runProductController']
        );

        add_submenu_page(
            'ws_forms_main',
            'Orders',
            'Orders',
            'manage_options',
            'ws_forms_orders',
            [self::class, 'runOrderController']
        );

        // 4. Unterpunkt: Options
        add_submenu_page(
            'ws_forms_main',
            'Options',
            'Options',
            'manage_options',
            'ws_forms_options',
            [self::class, 'runOptionController']
        );

        // Optional: Den automatisch duplizierten ersten Menüpunkt "WsForms" entfernen
       remove_submenu_page('ws_forms_main', 'ws_forms_main');
    }

    /**
     * Diese Runner-Methoden stellen sicher, dass die Controller
     * erst JETZT (beim Klick) instanziiert werden.
     */
    public static function runUserController() {
        $controller = new \Ws\WsForms\Controller\UserController();
        echo $controller->initAction();
    }

    public static function runAddressController() {
        $controller = new \Ws\WsForms\Controller\AddressController();
        echo $controller->initAction();
    }

    public static function runProductController() {
        $controller = new \Ws\WsForms\Controller\ProductController();
        echo $controller->initAction();
    }

    public static function runOrderController() {
        $controller = new \Ws\WsForms\Controller\OrderController();
        echo $controller->initAction();
    }

    public static function runOptionController() {
        $controller = new \Ws\WsForms\Controller\OptionController();
        echo $controller->initAction();
    }

	static public function activate() {
		global $wpdb;
		$table_name_addresses = $wpdb->prefix . 'wsf_addresses';
		$table_name_appointments = $wpdb->prefix . 'wsf_appointments';
        $table_name_products = $wpdb->prefix . 'wsf_products';
        $table_name_product_media = $wpdb->prefix . 'wsf_product_media';
        $table_name_orders = $wpdb->prefix . 'wsf_orders';
		$charset_collate = $wpdb->get_charset_collate();

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		// 1. Tabelle für Adressen
		// Beachte: Zwei Leerzeichen nach PRIMARY KEY und keine Backticks
		$sql_addresses = "CREATE TABLE $table_name_addresses (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        address_type varchar(50) DEFAULT 'default' NOT NULL,
        street varchar(255) NOT NULL,
        zip varchar(20) NOT NULL,
        city varchar(100) NOT NULL,
        country varchar(100) DEFAULT 'DE' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY user_id (user_id)
    ) $charset_collate;";

		dbDelta($sql_addresses);

		// 2. Tabelle für Termine
		// Korrektur: Backticks entfernt, Engine/Charset über Variable gesteuert
		$sql_appointments = "CREATE TABLE $table_name_appointments (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id bigint(20) UNSIGNED NOT NULL,
        title varchar(255) NOT NULL,
        description text DEFAULT NULL,
        start_date datetime NOT NULL,
        end_date datetime NOT NULL,
        status varchar(50) DEFAULT 'scheduled',
        apple_wallet_token varchar(255) DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY user_id (user_id)
    ) $charset_collate;";

		dbDelta($sql_appointments);

        $sql_products = "CREATE TABLE $table_name_products (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      title varchar(255) NOT NULL,
      sku varchar(100) NOT NULL,
      price decimal(10,2) NOT NULL,
      tax_rate decimal(5,2) DEFAULT '19.00' NOT NULL,
      currency varchar(3) DEFAULT 'EUR' NOT NULL,
      status varchar(50) DEFAULT 'active' NOT NULL,
      media int(11) DEFAULT 0 NOT NULL, 
      created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
      updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
      PRIMARY KEY  (id),
      UNIQUE KEY sku (sku),
      KEY status (status),
      KEY created_at (created_at),
      KEY title (title)
  ) $charset_collate;";

        dbDelta($sql_products);

        $sql_product_media = "CREATE TABLE $table_name_product_media (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    product_id bigint(20) NOT NULL,
    wp_id bigint(20) NOT NULL,
    menu_order int(11) DEFAULT 0 NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY  (id),
    KEY product_id (product_id),
    KEY wp_id (wp_id) 
) $charset_collate;";

        dbDelta($sql_product_media);

        $sql_orders = "CREATE TABLE $table_name_orders (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        order_number varchar(100) NOT NULL,
        user_id bigint(20) NOT NULL,
        total_price_brutto decimal(10,2) NOT NULL,
        total_price_netto decimal(10,2) NOT NULL,
        currency varchar(3) DEFAULT 'EUR' NOT NULL,
        status varchar(50) DEFAULT 'pending' NOT NULL,
        payment_method varchar(100) DEFAULT '' NOT NULL,
        billing_address_id bigint(20) DEFAULT 0 NOT NULL,
        shipping_address_id bigint(20) DEFAULT 0 NOT NULL,
        country varchar(10) DEFAULT '' NOT NULL,
        first_name varchar(255) DEFAULT '' NOT NULL,
        last_name varchar(255) DEFAULT '' NOT NULL,
        company varchar(255) DEFAULT '' NOT NULL,
        address_line_1 varchar(255) DEFAULT '' NOT NULL,
        address_line_2 varchar(255) DEFAULT '' NOT NULL,
        city varchar(255) DEFAULT '' NOT NULL,
        postal_code varchar(20) DEFAULT '' NOT NULL,
        vat_number varchar(100) DEFAULT '' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY order_number (order_number),
        KEY user_id (user_id),
        KEY status (status)
    ) $charset_collate;";

        dbDelta($sql_orders);

        $sql_order_items = "CREATE TABLE " . $wpdb->prefix . "wsf_order_items (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        order_id bigint(20) NOT NULL,
        order_number varchar(100) NOT NULL,
        product_id bigint(20) NOT NULL,
        product_title varchar(255) NOT NULL,
        sku varchar(100) NOT NULL,
        price_brutto decimal(10,2) NOT NULL,
        price_netto decimal(10,2) NOT NULL,
        tax decimal(10,2) NOT NULL,
        units int(11) DEFAULT 1 NOT NULL,
        total_price_brutto decimal(10,2) NOT NULL,
        total_price_netto decimal(10,2) NOT NULL,
        PRIMARY KEY  (id),
        KEY order_id (order_id),
        KEY order_number (order_number),
        KEY product_id (product_id)
    ) $charset_collate;";

        dbDelta($sql_order_items);

	}

}