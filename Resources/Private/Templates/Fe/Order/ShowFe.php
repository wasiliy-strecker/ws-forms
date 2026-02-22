<div class="wsf_m3_container wsf_mt_5" id="wsf_order_show_container">
    <div class="wsf_m3_card">
        <div class="wsf_p_4">
            <?php if (!empty($isStripeProcessing)): ?>
                <div id="wsf_stripe_processing" data-payment-intent="<?php echo esc_attr($paymentIntent); ?>">
                    <h1 class="wsf_m3_display_small"><?php echo esc_html($headline); ?></h1>
                    <div class="wsf_skeleton_loader" style="height: 100px; width: 100%; margin: 20px 0;"></div>
                    <p>Bitte warten Sie, während wir Ihre Zahlung verifizieren.</p>
                </div>
            <?php else: ?>
                <h1 class="wsf_m3_display_small wsf_text_success">
                    <span class="dashicons dashicons-yes-alt" style="font-size: 32px; width: 32px; height: 32px;"></span>
                    Vielen Dank für Ihre Bestellung!
                </h1>
                <p class="wsf_m3_body_large">
                    Ihre Bestellung <strong><?php echo esc_html($order->order_number); ?></strong> wurde erfolgreich entgegen genommen.
                </p>

                <hr class="wsf_my_4">

                <h2 class="wsf_m3_headline_small">Bestellübersicht</h2>
                <ul class="wsf_m3_list">
                    <?php foreach ($items as $item): ?>
                        <li class="wsf_m3_list_item">
                            <div class="wsf_m3_content">
                                <div class="wsf_m3_headline"><?php echo esc_html($item->product_title); ?></div>
                                <div class="wsf_m3_supporting_text">
                                    SKU: <?php echo esc_html($item->sku); ?> | 
                                    Menge: <?php echo $item->units; ?> | 
                                    Preis: <?php echo number_format($item->total_price_brutto, 2, ',', '.'); ?> <?php echo esc_html($order->currency); ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="wsf_mt_4 wsf_text_right">
                    <div class="wsf_m3_body_large"><strong>Gesamtbetrag (Brutto): <?php echo number_format($order->total_price_brutto, 2, ',', '.'); ?> <?php echo esc_html($order->currency); ?></strong></div>
                    <div class="wsf_m3_body_medium">Enthaltene MwSt.: <?php 
                        $totalTax = $order->total_price_brutto - $order->total_price_netto;
                        echo number_format($totalTax, 2, ',', '.'); 
                    ?> <?php echo esc_html($order->currency); ?></div>
                </div>

                <div class="wsf_mt_5">
                    <a href="<?php echo home_url('/produkte/'); ?>" class="wsf_m3_btn wsf_m3_btn_filled">
                        Zurück zum Shop
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
jQuery(function($) {
    var $processing = $('#wsf_stripe_processing');
    if ($processing.length > 0) {
        var piId = $processing.attr('data-payment-intent');
        ws.forms.controller.order.functions.getFromStripeIndexedDb(piId).then(function(orderData) {
            if (orderData) {
                ws.forms.controller.order.events.processOrderBackend($, orderData, 'Stripe');
            } else {
                alert('Bestellungsdaten konnten nicht geladen werden.');
            }
        });
    }
});
</script>
