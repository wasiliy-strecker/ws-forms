<div id="wsf_main" class="wsf_order_edit">
    <p><a href="<?php echo admin_url('admin.php?page=ws_forms_orders'); ?>">« Zurück zur Liste</a></p>
    <h1><?php echo esc_html($headline); ?></h1>

    <div class="wsf_m3_card" style="padding: 20px; background: #fff;">
        <h2>Bestellung: <?php echo esc_html($order->order_number); ?></h2>
        <p><strong>Status:</strong> <?php echo esc_html($order->status); ?></p>
        <p><strong>Kunde ID:</strong> <?php echo esc_html($order->user_id); ?></p>
        <p><strong>Gesamt Brutto:</strong> <?php echo number_format($order->total_price_brutto, 2, ',', '.'); ?> <?php echo esc_html($order->currency); ?></p>
        <p><strong>Gesamt Netto:</strong> <?php echo number_format($order->total_price_netto, 2, ',', '.'); ?> <?php echo esc_html($order->currency); ?></p>
        <p><strong>Zahlungsart:</strong> <?php echo esc_html($order->payment_method); ?></p>
        <p><strong>Datum:</strong> <?php echo esc_html($order->created_at); ?></p>
    </div>
</div>
