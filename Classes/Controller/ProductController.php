<?php
namespace Ws\WsForms\Controller;

use Ws\WsForms\Domain\Repository\ProductRepository;

class ProductController extends BaseController {

    /**
     * @var \Ws\WsForms\Domain\Model\Product
     */
    protected $productModel = null;

    public function __construct() {
        parent::__construct();
        // Falls du ein Model für Default-Werte wie getLimitToShow() hast:
        $this->productModel = new \Ws\WsForms\Domain\Model\Product();
    }

    /**
     * Listet alle Produkte auf (Backend & Frontend)
     */
    public function listAction($request = null) {
        $params = $request instanceof \WP_REST_Request ? $this->getParams($request) : $_GET;
        $searchQuery = !empty($params['wsf_search']) ? sanitize_text_field($params['wsf_search']) : '';

        // Pagination
        $currentPage = isset($params['wsf_page']) ? max(1, intval($params['wsf_page'])) : 1;
        $limit = $this->productModel->getLimitToShow();
        $offset = ($currentPage - 1) * $limit;

        $repository = new ProductRepository();

        if (!empty($searchQuery)) {
            $products = $repository->searchProducts($searchQuery, $limit, $offset);
            $totalProducts = $repository->countSearchProducts($searchQuery);
        } else {
            $products = $repository->findAllProducts($limit, $offset);
            $totalProducts = $repository->countAllProducts();
        }

        // Berechnung für View
        $totalPages = ceil($totalProducts / $limit);
        $startEntry = ($totalProducts > 0) ? $offset + 1 : 0;
        $endEntry   = min($offset + $limit, $totalProducts);

        $this->assign('products', $products);
        $this->assign('headline', 'Produktverwaltung');
        $this->assign('message', $params['message'] ?? null);

        // Pagination Variablen
        $this->assign('currentPage', $currentPage);
        $this->assign('totalPages', $totalPages);
        $this->assign('totalUsers', $totalProducts); // Konsistent zum User-Template-Namen oder totalProducts
        $this->assign('startEntry', $startEntry);
        $this->assign('endEntry', $endEntry);
        $this->assign('limit', $limit);
        $this->assign('isAdmin', $this->base->getIsAdmin());

        if ($request instanceof \WP_REST_Request) {
            $viewPath = $this->base->getIsAdmin() ? '/Product/../../Partials/Product/TableRows' : '/Product/../../Partials/Product/ListRowsFrontend';
            return new \WP_REST_Response([
                'html' => $this->renderView($viewPath),
                'pagination' => !$this->base->getIsAdmin() ? $this->renderView('/Product/../../Partials/Product/PaginationFrontend') : ''
            ], 200);
        }
        return $this->renderView($this->base->getIsAdmin() ? 'Product/List' : 'Product/ListFrontend');
    }

    /**
     * Zeigt das Formular für ein neues Produkt
     */
    public function newAction(): string {
        $product = new \stdClass();
        $product->id = 0;
        $product->title = '';
        $product->sku = '';
        $product->price = '0.00';
        $product->tax_rate = '19.00';
        $product->status = 'active';

        $this->assign('product', $product);
        $this->assign('headline', 'Neues Produkt anlegen');

        return $this->renderView($this->base->getIsAdmin() ? 'Product/New' : 'Product/NewFrontend');
    }

    /**
     * Speichert ein neues Produkt
     */
    public function createAction(\WP_REST_Request $request): \WP_REST_Response {
        $params = $this->getParams($request);
        $productData = $params['product'] ?? [];

        if (empty($productData['title']) || empty($productData['sku'])) {
            return new \WP_REST_Response(['message' => 'Titel und SKU sind erforderlich!'], 400);
        }

        $repository = new ProductRepository();

        // Check ob SKU existiert
        if ($repository->getProductBySku($productData['sku'])) {
            return new \WP_REST_Response(['message' => 'Diese SKU existiert bereits!'], 400);
        }

        $productId = $repository->addProduct($productData);

        if (!$productId) {
            return new \WP_REST_Response(['message' => 'Fehler beim Erstellen des Produkts.'], 500);
        }

        // Media IDs verarbeiten
        if (!empty($productData['media_ids'])) {
            $mediaIds = explode(',', $productData['media_ids']);
            $repository->addProductMedia($productId, $mediaIds);
        }

        $redirectUrl = $this->base->getIsAdmin()
            ? admin_url('admin.php?page=ws_forms_products&message=created')
            : add_query_arg(['action' => 'list', 'message' => 'created'], home_url('/produkte/'));

        return new \WP_REST_Response([
            'message' => 'Produkt erfolgreich angelegt!',
            'redirect' => $redirectUrl
        ], 200);
    }

    /**
     * Zeigt das Bearbeitungs-Formular
     */
    public function editAction(): string {
        $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $repository = new ProductRepository();
        $product = $repository->getProductById($productId);

        if (!$product) {
            wp_die(__('Produkt nicht gefunden.', 'ws-forms'));
        }

        $this->assign('isEdit', true);
        $this->assign('headline', 'Produkt bearbeiten');
        $this->assign('product', $product);

        return $this->renderView($this->base->getIsAdmin() ? 'Product/Edit' : 'Product/EditFrontend');
    }

    /**
     * Aktualisiert ein bestehendes Produkt
     */
    public function updateAction(\WP_REST_Request $request): \WP_REST_Response {
        $params = $this->getParams($request);
        $productId = intval($params['id'] ?? 0);
        $productData = $params['product'] ?? [];

        if (!$productId) {
            return new \WP_REST_Response(['message' => 'Ungültige Produkt-ID.'], 400);
        }

        $repository = new ProductRepository();
        $updated = $repository->updateProduct($productId, $productData);

        if ($updated === false) {
            return new \WP_REST_Response(['message' => 'Fehler beim Aktualisieren.'], 500);
        }

        return new \WP_REST_Response([
            'message' => 'Produkt erfolgreich aktualisiert!',
            'redirect' => admin_url('admin.php?page=ws_forms_products&message=updated')
        ], 200);
    }

    /**
     * Prüft via AJAX, ob eine SKU bereits vergeben ist.
     */
    public function checkSkuAction(\WP_REST_Request $request): \WP_REST_Response
    {
        $sku = $request->get_param('sku');

        if (empty($sku)) {
            return new \WP_REST_Response(['valid' => false, 'message' => 'SKU fehlt.'], 400);
        }

        $repository = new \Ws\WsForms\Domain\Repository\ProductRepository();
        $exists = $repository->getProductBySku($sku);

        return new \WP_REST_Response([
            'exists'  => (bool)$exists,
            'message' => $exists ? 'Diese SKU ist bereits vergeben.' : 'SKU ist verfügbar.'
        ], 200);
    }

    /**
     * Analysiert einen Benutzer-Prompt via OpenAI und gibt Produktdaten zurück.
     */
    public function aiAnalyzeAction(\WP_REST_Request $request): \WP_REST_Response {
        $params = $this->getParams($request);
        $prompt = $params['prompt'] ?? '';

        if (empty($prompt)) {
            return new \WP_REST_Response(['message' => 'Prompt fehlt.'], 400);
        }

        $optionRepository = new \Ws\WsForms\Domain\Repository\OptionRepository();
        $options = $optionRepository->get();
        $apiKey = $options->openaiApiKey;

        if (empty($apiKey)) {
            return new \WP_REST_Response(['message' => 'OpenAI API Key ist nicht konfiguriert.'], 400);
        }

        $systemPrompt = "Du bist ein Assistent, der Benutzereingaben in strukturierte Produktdaten für einen Online-Shop umwandelt. 
        Du MUSST ein valides JSON-Objekt zurückgeben, das genau die folgenden Felder enthält:
        - title (String): Der Name des Produkts.
        - sku (String): Eine eindeutige Artikelnummer (wenn nicht vom Benutzer genannt, generiere eine passende kurze SKU).
        - price (String): Der numerische Preis (nutze einen Punkt als Dezimaltrenner, z.B. '19.99').
        - tax_rate (String): Der Steuersatz als Zahl (Standard '19.00').
        - status (String): Immer 'active'.

        WICHTIG: Gib NUR das JSON-Objekt zurück, keinen weiteren Text.";

        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ],
            'timeout' => 30,
            'body'    => json_encode([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.0,
            ]),
        ]);

        if (is_wp_error($response)) {
            return new \WP_REST_Response(['message' => 'Fehler bei der Kommunikation mit OpenAI: ' . $response->get_error_message()], 500);
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $content = $body['choices'][0]['message']['content'] ?? '';

        if (empty($content)) {
            return new \WP_REST_Response(['message' => 'Keine Antwort von der AI erhalten.'], 500);
        }

        $productData = json_decode($content, true);

        if (!$productData) {
            return new \WP_REST_Response(['message' => 'Die AI hat kein gültiges JSON zurückgegeben.', 'raw' => $content], 500);
        }

        return new \WP_REST_Response([
            'product' => $productData,
            'message' => 'Vorschlag generiert.'
        ], 200);
    }
}