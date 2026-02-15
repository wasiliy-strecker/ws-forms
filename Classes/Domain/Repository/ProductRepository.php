<?php
namespace Ws\WsForms\Domain\Repository;

class ProductRepository {

    /**
     * @var string
     */
    protected $tableName;

    public function __construct() {
        global $wpdb;
        $this->tableName = $wpdb->prefix . 'wsf_products';
    }

    /**
     * Findet alle Produkte mit Pagination.
     */
    public function findAllProducts(int $limit = -1, int $offset = 0): array {
        global $wpdb;
        $sql = "SELECT * FROM {$this->tableName} ORDER BY created_at DESC";

        if ($limit > 0) {
            $sql .= $wpdb->prepare(" LIMIT %d OFFSET %d", $limit, $offset);
        }

        return $wpdb->get_results($sql);
    }

    /**
     * Zählt alle Produkte.
     */
    public function countAllProducts(): int {
        global $wpdb;
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->tableName}");
    }

    /**
     * Sucht nach Produkten (Titel oder SKU).
     */
    public function searchProducts(string $query, int $limit = -1, int $offset = 0): array {
        global $wpdb;
        $wildcard = '%' . $wpdb->esc_like($query) . '%';

        $sql = $wpdb->prepare(
            "SELECT * FROM {$this->tableName} WHERE title LIKE %s OR sku LIKE %s ORDER BY created_at DESC",
            $wildcard,
            $wildcard
        );

        if ($limit > 0) {
            $sql .= $wpdb->prepare(" LIMIT %d OFFSET %d", $limit, $offset);
        }

        return $wpdb->get_results($sql);
    }

    /**
     * Zählt die Suchergebnisse.
     */
    public function countSearchProducts(string $query): int {
        global $wpdb;
        $wildcard = '%' . $wpdb->esc_like($query) . '%';

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->tableName} WHERE title LIKE %s OR sku LIKE %s",
            $wildcard,
            $wildcard
        ));
    }

    /**
     * Holt ein Produkt anhand der ID.
     */
    public function getProductById(int $id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->tableName} WHERE id = %d", $id));
    }

    /**
     * Holt ein Produkt anhand der SKU (für Validierung).
     */
    public function getProductBySku(string $sku) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->tableName} WHERE sku = %s", $sku));
    }

    /**
     * Speichert ein neues Produkt.
     */
    public function addProduct(array $data) {
        global $wpdb;

        $result = $wpdb->insert(
            $this->tableName,
            [
                'title'    => sanitize_text_field($data['title']),
                'sku'      => strtoupper(sanitize_text_field($data['sku'])),
                'price'    => floatval($data['price']),
                'tax_rate' => floatval($data['tax_rate'] ?? 19.00),
                'currency' => sanitize_text_field($data['currency'] ?? 'EUR'),
                'status'   => sanitize_text_field($data['status'] ?? 'active'),
                'created_at' => current_time('mysql')
            ],
            ['%s', '%s', '%f', '%f', '%s', '%s']
        );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Fügt Produkt-Media hinzu.
     */
    public function addProductMedia(int $productId, array $mediaIds) {
        global $wpdb;
        $tableName = $wpdb->prefix . 'wsf_product_media';
        
        foreach ($mediaIds as $index => $wpId) {
            $wpdb->insert(
                $tableName,
                [
                    'product_id' => $productId,
                    'wp_id'      => intval($wpId),
                    'menu_order' => $index,
                    'created_at' => current_time('mysql')
                ],
                ['%d', '%d', '%d', '%s']
            );
        }
    }

    /**
     * Aktualisiert ein Produkt.
     */
    public function updateProduct(int $id, array $data) {
        global $wpdb;
        return $wpdb->update(
            $this->tableName,
            [
                'title'    => sanitize_text_field($data['title']),
                'sku'      => strtoupper(sanitize_text_field($data['sku'])),
                'price'    => floatval($data['price']),
                'tax_rate' => floatval($data['tax_rate']),
                'currency' => sanitize_text_field($data['currency']),
                'status'   => sanitize_text_field($data['status'])
            ],
            ['id' => $id],
            ['%s', '%s', '%f', '%f', '%s', '%s'],
            ['%d']
        );
    }
}