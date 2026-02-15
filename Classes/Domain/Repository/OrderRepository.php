<?php
namespace Ws\WsForms\Domain\Repository;

class OrderRepository {

    /**
     * @var string
     */
    protected $tableName;

    public function __construct() {
        global $wpdb;
        $this->tableName = $wpdb->prefix . 'wsf_orders';
    }

    /**
     * Findet alle Bestellungen mit Pagination.
     */
    public function findAllOrders(int $limit = -1, int $offset = 0): array {
        global $wpdb;
        $sql = "SELECT * FROM {$this->tableName} ORDER BY created_at DESC";

        if ($limit > 0) {
            $sql .= $wpdb->prepare(" LIMIT %d OFFSET %d", $limit, $offset);
        }

        return $wpdb->get_results($sql);
    }

    /**
     * Zählt alle Bestellungen.
     */
    public function countAllOrders(): int {
        global $wpdb;
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->tableName}");
    }

    /**
     * Sucht nach Bestellungen (Order Number).
     */
    public function searchOrders(string $query, int $limit = -1, int $offset = 0): array {
        global $wpdb;
        $wildcard = '%' . $wpdb->esc_like($query) . '%';

        $sql = $wpdb->prepare(
            "SELECT * FROM {$this->tableName} WHERE order_number LIKE %s ORDER BY created_at DESC",
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
    public function countSearchOrders(string $query): int {
        global $wpdb;
        $wildcard = '%' . $wpdb->esc_like($query) . '%';

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->tableName} WHERE order_number LIKE %s",
            $wildcard
        ));
    }

    /**
     * Holt eine Bestellung anhand der ID.
     */
    public function getOrderById(int $id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->tableName} WHERE id = %d", $id));
    }

    /**
     * Speichert eine neue Bestellung.
     */
    public function addOrder(array $data) {
        global $wpdb;
        $result = $wpdb->insert(
            $this->tableName,
            [
                'order_number'        => $data['order_number'],
                'user_id'             => intval($data['user_id'] ?? get_current_user_id()),
                'total_price_brutto'  => floatval($data['total_price_brutto']),
                'total_price_netto'   => floatval($data['total_price_netto']),
                'currency'            => $data['currency'] ?? 'EUR',
                'status'              => $data['status'] ?? 'completed',
                'payment_method'      => $data['payment_method'] ?? 'PayPal',
                'billing_address_id'  => intval($data['billing_address_id'] ?? 0),
                'shipping_address_id' => intval($data['shipping_address_id'] ?? 0),
                'country'             => sanitize_text_field($data['country'] ?? ''),
                'first_name'          => sanitize_text_field($data['first_name'] ?? ''),
                'last_name'           => sanitize_text_field($data['last_name'] ?? ''),
                'company'             => sanitize_text_field($data['company'] ?? ''),
                'address_line_1'      => sanitize_text_field($data['address_line_1'] ?? ''),
                'address_line_2'      => sanitize_text_field($data['address_line_2'] ?? ''),
                'city'                => sanitize_text_field($data['city'] ?? ''),
                'postal_code'         => sanitize_text_field($data['postal_code'] ?? ''),
                'vat_number'          => sanitize_text_field($data['vat_number'] ?? ''),
            ],
            ['%s', '%d', '%f', '%f', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );
        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Speichert ein Bestell-Item.
     */
    public function addOrderItem(array $data) {
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'wsf_order_items',
            [
                'order_id'           => intval($data['order_id']),
                'order_number'       => $data['order_number'],
                'product_id'         => intval($data['product_id']),
                'product_title'      => $data['product_title'],
                'sku'                => $data['sku'],
                'price_brutto'       => floatval($data['price_brutto']),
                'price_netto'        => floatval($data['price_netto']),
                'tax'                => floatval($data['tax']),
                'units'              => intval($data['units']),
                'total_price_brutto' => floatval($data['total_price_brutto']),
                'total_price_netto'  => floatval($data['total_price_netto']),
            ],
            ['%d', '%s', '%d', '%s', '%s', '%f', '%f', '%f', '%d', '%f', '%f']
        );
    }

    /**
     * Holt alle Items einer Bestellung.
     */
    public function getOrderItemsByOrderId(int $orderId) {
        global $wpdb;
        $tableName = $wpdb->prefix . 'wsf_order_items';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $tableName WHERE order_id = %d", $orderId));
    }
}
