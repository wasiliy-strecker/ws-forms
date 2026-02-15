<?php
namespace Ws\WsForms\Controller;

use Ws\WsForms\Domain\Repository\OrderRepository;

class OrderController extends BaseController {

    /**
     * @var \Ws\WsForms\Domain\Model\Order
     */
    protected $orderModel = null;

    public function __construct() {
        parent::__construct();
        $this->orderModel = new \Ws\WsForms\Domain\Model\Order();
    }

    /**
     * Listet alle Bestellungen auf (Backend)
     */
    public function listAction($request = null) {
        $params = $request instanceof \WP_REST_Request ? $this->getParams($request) : $_GET;
        $searchQuery = !empty($params['wsf_search']) ? sanitize_text_field($params['wsf_search']) : '';

        // Pagination
        $currentPage = isset($params['wsf_page']) ? max(1, intval($params['wsf_page'])) : 1;
        $limit = $this->orderModel->getLimitToShow();
        $offset = ($currentPage - 1) * $limit;

        $repository = new OrderRepository();

        if (!empty($searchQuery)) {
            $orders = $repository->searchOrders($searchQuery, $limit, $offset);
            $totalOrders = $repository->countSearchOrders($searchQuery);
        } else {
            $orders = $repository->findAllOrders($limit, $offset);
            $totalOrders = $repository->countAllOrders();
        }

        // Berechnung für View
        $totalPages = ceil($totalOrders / $limit);
        $startEntry = ($totalOrders > 0) ? $offset + 1 : 0;
        $endEntry   = min($offset + $limit, $totalOrders);

        $this->assign('orders', $orders);
        $this->assign('headline', 'Bestellungsverwaltung');
        $this->assign('message', $params['message'] ?? null);

        // Pagination Variablen
        $this->assign('currentPage', $currentPage);
        $this->assign('totalPages', $totalPages);
        $this->assign('totalItems', $totalOrders);
        $this->assign('startEntry', $startEntry);
        $this->assign('endEntry', $endEntry);
        $this->assign('limit', $limit);
        $this->assign('isAdmin', $this->base->getIsAdmin());

        if ($request instanceof \WP_REST_Request) {
            $viewPath = '/Order/../../Partials/Order/TableRows';
            return new \WP_REST_Response([
                'html' => $this->renderView($viewPath)
            ], 200);
        }
        return $this->renderView('Order/List');
    }

    /**
     * Nicht implementiert für Bestellungen (via Backend)
     */
    public function newAction(): string {
        return "Not implemented";
    }

    /**
     * Details einer Bestellung anzeigen
     */
    public function editAction(): string {
        $orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $repository = new OrderRepository();
        $order = $repository->getOrderById($orderId);

        if (!$order) {
            wp_die(__('Bestellung nicht gefunden.', 'ws-forms'));
        }

        $this->assign('headline', 'Bestellung Details');
        $this->assign('order', $order);

        return $this->renderView('Order/Edit');
    }

    /**
     * Erstellt einen Stripe Payment Intent
     */
    public function getStripeIntentAction(\WP_REST_Request $request): \WP_REST_Response {
        $params = $this->getParams($request);
        $cart = $params['cart'] ?? [];

        if (empty($cart)) {
            return new \WP_REST_Response(['message' => 'Warenkorb ist leer.'], 400);
        }

        // Berechnung der Werte
        $totalValue = 0;
        foreach ($cart as $item) {
            $totalValue += $item['price'] * $item['units'];
        }

        // Stripe Secret Key (Sollte idealerweise aus den Optionen kommen)
        // Ich nutze hier einen Platzhalter oder suche nach einer Option
        $secret = get_option('wsf_stripe_secret_key', 'sk_test_placeholder');

        $currency = 'eur';
        // Betrag in Cents für Stripe
        $amount = round($totalValue * 100);

        $metaData = [
            'currency_code' => strtoupper($currency),
            'total_value' => $totalValue,
            'items_total_value' => $totalValue,
            'shipping' => 0 // Beispielhaft
        ];

        $stripeParams = [
            'currency' => $currency,
            'amount' => $amount,
            'metadata' => $metaData
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($stripeParams));
        curl_setopt($ch, CURLOPT_USERPWD, $secret . ':' . '');

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $result = json_decode($response, true);

        if (curl_errno($ch)) {
            $error = 'Error:' . curl_error($ch);
            curl_close($ch);
            return new \WP_REST_Response(['message' => $error], 500);
        }

        curl_close($ch);

        if (!empty($result['error']['message'])) {
            return new \WP_REST_Response(['message' => 'Stripe Error: ' . $result['error']['message']], 400);
        }

        return new \WP_REST_Response([
            'stripepiid' => $result['id'],
            'stripepikey' => $result['client_secret']
        ], 200);
    }

    /**
     * Erstellt eine neue Bestellung nach PayPal Checkout
     */
    public function createAction(\WP_REST_Request $request): \WP_REST_Response {
        $params = $this->getParams($request);
        $orderData = $params['order_data'] ?? [];
        $paymentMethod = $params['payment_method'] ?? 'PayPal';
        $cart = $params['cart'] ?? [];

        if (empty($cart)) {
            return new \WP_REST_Response(['message' => 'Warenkorb ist leer.'], 400);
        }

        $totalBrutto = 0;
        foreach($cart as $item) {
            $totalBrutto += floatval($item['price']) * intval($item['units']);
        }

        // PayPal Verifizierung
        if ($paymentMethod === 'PayPal') {
            $paypalOrderId = $orderData['paypal_order_id'] ?? '';
            if (empty($paypalOrderId)) {
                return new \WP_REST_Response(['message' => 'PayPal Order ID fehlt.'], 400);
            }

            // 1. PayPal Token holen
            $clientId = 'xxx';
            $secret = 'xxxx';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.paypal.com/v1/oauth2/token');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            $result = curl_exec($ch);
            $tokenData = json_decode($result);
            curl_close($ch);

            if (!isset($tokenData->access_token)) {
                return new \WP_REST_Response(['message' => 'PayPal Authentifizierung fehlgeschlagen.'], 500);
            }

            $accessToken = $tokenData->access_token;

            // 2. PayPal Order prüfen
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/" . $paypalOrderId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer " . $accessToken
            ]);
            $orderResponse = curl_exec($ch);
            $paypalOrder = json_decode($orderResponse);
            curl_close($ch);

            if (!$paypalOrder || $paypalOrder->status !== 'COMPLETED') {
                return new \WP_REST_Response(['message' => 'PayPal Bestellung nicht abgeschlossen.'], 400);
            }
            $customerEmail = $orderData['paypal_email'] ?? '';
        } else {
            // Stripe Verifizierung
            $stripePiId = $orderData['stripepiid'] ?? '';
            if (empty($stripePiId)) {
                return new \WP_REST_Response(['message' => 'Stripe PaymentIntent ID fehlt.'], 400);
            }

            $secret = get_option('wsf_stripe_secret_key', 'StripeSandboxSecretKey');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents/' . $stripePiId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, $secret . ':' . '');
            $result = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if (isset($result['error'])) {
                return new \WP_REST_Response(['message' => 'Stripe Fehler: ' . $result['error']['message']], 400);
            }

            if ($result['status'] !== 'succeeded') {
                return new \WP_REST_Response(['message' => 'Stripe Zahlung nicht erfolgreich (Status: ' . $result['status'] . ').'], 400);
            }

            $customerEmail = $orderData['StripeEmail'] ?? '';
        }

        // 3. Bestellung in DB anlegen
        $repository = new OrderRepository();
        $orderNumber = 'WSF-' . strtoupper(uniqid());

        $totalNetto = $totalBrutto / 1.19;

        $orderId = $repository->addOrder([
            'order_number' => $orderNumber,
            'total_price_brutto' => $totalBrutto,
            'total_price_netto' => $totalNetto,
            'status' => 'completed',
            'payment_method' => $paymentMethod,
            'country' => $orderData['country'] ?? '',
            'first_name' => $orderData['firstName'] ?? '',
            'last_name' => $orderData['lastName'] ?? '',
            'company' => $orderData['company'] ?? '',
            'address_line_1' => $orderData['addressLine1'] ?? '',
            'address_line_2' => $orderData['addressLine2'] ?? '',
            'city' => $orderData['city'] ?? '',
            'postal_code' => $orderData['postalCode'] ?? '',
            'vat_number' => $orderData['vatNumber'] ?? '',
        ]);

        if (!$orderId) {
            return new \WP_REST_Response(['message' => 'Fehler beim Speichern der Bestellung.'], 500);
        }

        // 4. Order Items anlegen
        foreach ($cart as $item) {
            $priceBrutto = floatval($item['price']);
            $priceNetto = $priceBrutto / 1.19;
            $tax = $priceBrutto - $priceNetto;
            $units = intval($item['units']);

            $repository->addOrderItem([
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'product_id' => $item['id'],
                'product_title' => $item['title'],
                'sku' => $item['sku'],
                'price_brutto' => $priceBrutto,
                'price_netto' => $priceNetto,
                'tax' => $tax,
                'units' => $units,
                'total_price_brutto' => $priceBrutto * $units,
                'total_price_netto' => $priceNetto * $units
            ]);
        }

        // 5. Email Bestätigung senden
        if (!empty($customerEmail)) {
            $subject = 'Bestellbestätigung ' . $orderNumber;
            $message = "Vielen Dank für Ihre Bestellung!\n\n";
            $message .= "Bestellnummer: " . $orderNumber . "\n";
            $message .= "Gesamtbetrag: " . number_format($totalBrutto, 2, ',', '.') . " EUR\n\n";
            $message .= "Wir werden Ihre Bestellung schnellstmöglich bearbeiten.\n";
            wp_mail($customerEmail, $subject, $message);
        }

        return new \WP_REST_Response([
            'message' => 'Bestellung erfolgreich erstellt.',
            'order_id' => $orderId
        ], 200);
    }

    /**
     * Rendert die Bestellbestätigung
     */
    public function showAction($request = null): \WP_REST_Response|string {
        if ($request instanceof \WP_REST_Request) {
            $orderId = intval($request->get_param('id'));
        } else {
            $orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
            
            // Stripe Redirect handling
            if ($orderId === 0 && isset($_GET['payment_intent'])) {
                $this->assign('headline', 'Bestellung wird verarbeitet...');
                $this->assign('isStripeProcessing', true);
                $this->assign('paymentIntent', sanitize_text_field($_GET['payment_intent']));
                return $this->renderView('Order/Show');
            }
        }

        $repository = new OrderRepository();
        $order = $repository->getOrderById($orderId);

        if (!$order) {
            if ($request instanceof \WP_REST_Request) {
                return new \WP_REST_Response(['message' => 'Bestellung nicht gefunden.'], 404);
            }
            return __('Bestellung nicht gefunden.', 'ws-forms');
        }

        $items = $repository->getOrderItemsByOrderId($orderId);

        $this->assign('order', $order);
        $this->assign('items', $items);
        $this->assign('headline', 'Bestellbestätigung');

        $view = $this->renderView('Order/Show');

        if ($request instanceof \WP_REST_Request) {
            return new \WP_REST_Response([
                'html' => $view
            ], 200);
        }

        return $view;
    }
}
