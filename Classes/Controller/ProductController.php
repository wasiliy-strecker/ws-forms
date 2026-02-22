<?php
namespace Ws\WsForms\Controller;

use Ws\WsForms\Domain\Repository\ProductRepository;

class ProductController extends BaseController {

    /**
     * @var \Ws\WsForms\Domain\Model\Product
     */
    protected $productModel = null;

    /**
     * @var \Ws\WsForms\ControllerHelper\ProductHelper
     */
    protected $productHelper = null;

    public function __construct() {
        parent::__construct();
        $this->productModel = new \Ws\WsForms\Domain\Model\Product();
        $this->productHelper = new \Ws\WsForms\ControllerHelper\ProductHelper();
    }

    public function listBeAction($request = null) {
        $params = $request instanceof \WP_REST_Request ? $this->getParams($request) : $_GET;
        $data = $this->productHelper->getPreparedProductListData($params);
        foreach ($data as $key => $value) {
            $this->assign($key, $value);
        }

        if ($request instanceof \WP_REST_Request) {
            return new \WP_REST_Response([
                'html' => $this->renderPartial('Product/TableRows'),
                'pagination' => ''
            ], 200);
        }
        $this->assign('headline', 'Produktverwaltung');
        return $this->renderView('Product/List');
    }

    public function listFeAction($request = null) {
        $params = $request instanceof \WP_REST_Request ? $this->getParams($request) : $_GET;
        $data = $this->productHelper->getPreparedProductListData($params);
        foreach ($data as $key => $value) {
            $this->assign($key, $value);
        }

        if ($request instanceof \WP_REST_Request) {
            return new \WP_REST_Response([
                'html' => $this->renderPartial('Product/ListRows'),
                'pagination' => $this->renderPartial('Product/Pagination')
            ], 200);
        }
        $this->assign('headline', 'Produkte');
        return $this->renderView('Product/List');
    }

    public function newBeAction(): string {
        $product = new \stdClass();
        $product->id = 0;
        $product->title = '';
        $product->sku = '';
        $product->price = '0.00';
        $product->tax_rate = '19.00';
        $product->status = 'active';

        $this->assign('product', $product);
        $this->assign('headline', 'Neues Produkt anlegen');

        return $this->renderView('Product/New');
    }

    public function newFeAction(): string {
        return $this->newBeAction(); // Usually FE new product form might be different, but for now same.
    }

    public function createBeAction(\WP_REST_Request $request): \WP_REST_Response {
        return $this->createAction($request);
    }

    public function createFeAction(\WP_REST_Request $request): \WP_REST_Response {
        return $this->createAction($request);
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
            : add_query_arg(['wsf_action' => 'list', 'message' => 'created'], home_url('/produkte/'));

        return new \WP_REST_Response([
            'message' => 'Produkt erfolgreich angelegt!',
            'redirect' => $redirectUrl
        ], 200);
    }

    public function editBeAction(): string {
        return $this->editAction();
    }

    public function editFeAction(): string {
        return $this->editAction();
    }

    /**
     * Zeigt das Bearbeitungs-Formular
     */
    public function editAction(): string {
        $productId = get_query_var('wsf_id') ?: (isset($_GET['wsf_id']) ? $_GET['wsf_id'] : (isset($_GET['id']) ? $_GET['id'] : 0));
        $productId = intval($productId);
        $repository = new ProductRepository();
        $product = $repository->getProductById($productId);

        if (!$product) {
            wp_die(__('Produkt nicht gefunden.', 'ws-forms'));
        }

        $this->assign('isEdit', true);
        $this->assign('headline', 'Produkt bearbeiten');
        $this->assign('product', $product);

        return $this->renderView('Product/Edit');
    }

    public function updateBeAction(\WP_REST_Request $request): \WP_REST_Response {
        return $this->updateAction($request);
    }

    public function updateFeAction(\WP_REST_Request $request): \WP_REST_Response {
        return $this->updateAction($request);
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