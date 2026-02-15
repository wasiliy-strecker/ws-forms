<div id="wsf_main" class="container wsf_product_list wsf_product_list_frontend wsf_mt_5 wsf_m3_container"
     data-wsf-is-admin="<?php echo $isAdmin ? 'true' : 'false'; ?>"
     data-wsf-current-page="<?php echo $currentPage; ?>"
>
    <div class="wsf_d_flex wsf_justify_content_between wsf_align_items_center wsf_mb_4">
        <h1 class="wsf_m3_display_small"><?php echo esc_html($headline); ?></h1>
        <div class="wsf_d_flex wsf_align_items_center">
            <button type="button" id="wsf_cart_btn" class="wsf_m3_btn wsf_m3_btn_tonal wsf_me_2" aria-label="Warenkorb anzeigen">
                <span class="dashicons dashicons-cart" style="margin-right: 8px;"></span>
                Warenkorb (<span id="wsf_cart_count">0</span>)
            </button>
            <a href="?action=new" class="wsf_m3_btn wsf_m3_btn_filled">
                <span class="dashicons dashicons-plus" style="margin-right: 8px;"></span>
                Neues Produkt anlegen
            </a>
        </div>
    </div>

    <?php if (isset($message)): ?>
        <div id="wsf_frontend_message" class="wsf_alert wsf_alert_success wsf_alert_dismissible" role="alert">
            <div class="wsf_alert_content">
                <?php
                if ($message === 'created') _e('Produkt erfolgreich angelegt!', 'ws-forms');
                if ($message === 'updated') _e('Produkt erfolgreich bearbeitet!', 'ws-forms');
                ?>
            </div>
            <button type="button" class="wsf_alert_close" onclick="this.parentElement.style.display='none';" aria-label="Close">
                &times;
            </button>
        </div>
    <?php endif; ?>

    <div class="wsf_m3_search_bar">
        <span class="dashicons dashicons-search wsf_m3_search_leading_icon" aria-hidden="true"></span>
        <input id="wsf_user_search_input" type="text"
               name="s"
               class="wsf_m3_search_input"
               value="<?php echo esc_attr($_GET['wsf_search'] ?? ''); ?>"
               placeholder="Produkte suchen..."
               aria-label="Produkte suchen">
        <button id="wsf_user_search_btn" type="submit" class="wsf_m3_search_btn" aria-label="Suche starten">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
        </button>
        <button type="button" class="wsf_m3_search_btn wsf_hide" id="wsf_search_clear" aria-label="Suche löschen">
            <span class="dashicons dashicons-no"></span>
        </button>
    </div>

    <div id="wsf_pagination_container">
        <?php include (__DIR__."/../../Partials/Product/PaginationFrontend.php"); ?>
    </div>

    <div class="wsf_m3_card">
        <ul id="wsf_m3_list" class="wsf_m3_list" itemscope itemtype="https://schema.org/ItemList">
            <meta itemprop="name" content="Produktliste">
            <meta itemprop="description" content="Liste aller Produkte">
            <?php include (__DIR__."/../../Partials/Product/ListRowsFrontend.php"); ?>
        </ul>
    </div>

    <!-- Warenkorb Modal -->
    <div id="wsf_cart_modal" class="wsf_m3_modal">
        <div class="wsf_m3_modal_content">
            <div class="wsf_m3_modal_header">
                <h2 class="wsf_m3_headline_small">Warenkorb</h2>
                <button type="button" class="wsf_m3_modal_close" id="wsf_cart_modal_close">&times;</button>
            </div>
            <div class="wsf_m3_modal_body">
                <!-- Kundenformular -->
                <div id="wsf_customer_form" class="wsf_mb_4">
                    <h3 class="wsf_m3_headline_small wsf_mb_2">Versandinformationen</h3>
                    <div class="wsf_row">
                        <div class="wsf_col_6">
                            <input type="text" id="wsf_customer_first_name" class="wsf_m3_input" placeholder="Vorname *">
                        </div>
                        <div class="wsf_col_6">
                            <input type="text" id="wsf_customer_last_name" class="wsf_m3_input" placeholder="Nachname *">
                        </div>
                    </div>
                    <div class="wsf_row wsf_mt_2">
                        <div class="wsf_col_12">
                            <input type="text" id="wsf_customer_company" class="wsf_m3_input" placeholder="Firma">
                        </div>
                    </div>
                    <div class="wsf_row wsf_mt_2">
                        <div class="wsf_col_12">
                            <input type="text" id="wsf_customer_address_1" class="wsf_m3_input" placeholder="Adresse Zeile 1 *">
                        </div>
                    </div>
                    <div class="wsf_row wsf_mt_2">
                        <div class="wsf_col_12">
                            <input type="text" id="wsf_customer_address_2" class="wsf_m3_input" placeholder="Adresse Zeile 2">
                        </div>
                    </div>
                    <div class="wsf_row wsf_mt_2">
                        <div class="wsf_col_4">
                            <input type="text" id="wsf_customer_postal_code" class="wsf_m3_input" placeholder="PLZ *">
                        </div>
                        <div class="wsf_col_8">
                            <input type="text" id="wsf_customer_city" class="wsf_m3_input" placeholder="Stadt *">
                        </div>
                    </div>
                    <div class="wsf_row wsf_mt_2">
                        <div class="wsf_col_6">
                            <select id="wsf_customer_country" class="wsf_m3_input">
                                <option value="DE">Deutschland</option>
                                <option value="AT">Österreich</option>
                                <option value="CH">Schweiz</option>
                            </select>
                        </div>
                        <div class="wsf_col_6">
                            <input type="text" id="wsf_customer_vat_number" class="wsf_m3_input" placeholder="USt-IdNr.">
                        </div>
                    </div>
                </div>

                <ul id="wsf_cart_items_list" class="wsf_m3_list">
                    <!-- Wird via JS befüllt -->
                </ul>
                <div id="wsf_cart_total" class="wsf_mt_3 wsf_text_right wsf_m3_headline_small">
                    Gesamt: <span id="wsf_cart_total_amount">0.00</span> EUR
                </div>
            </div>
            <div class="wsf_m3_modal_footer">
                <div id="paypal-button-container"></div>
                
                <div id="wsf_stripe_container" class="wsf_mt_4">
                    <form id="wsfEcommerceStripePaymentForm">
                        <div id="wsfEcommerceStripeAuthenticationElement"></div>
                        <div id="wsfEcommerceStripePaymentElement"></div>
                        <button role="submit" id="wsfEcommerceStripePaymentButton" class="wsf_m3_btn wsf_m3_btn_filled wsf_w_100 wsf_mt_3">
                            Jetzt bezahlen (Stripe)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
