<div class="wsf_m3_text_field">
    <input name="product[sku]" type="text" id="wsf_product_sku"
           value="<?php echo esc_attr($product->sku); ?>"
            <?php echo !empty($isEdit) ? 'readonly' : ''; ?>
           class="wsf_form_control wsf_error_required" placeholder=" "
           aria-describedby="wsf_description_sku wsf_error_required_sku">
    <label for="wsf_product_sku">SKU (Artikelnummer) *</label>
</div>

<p class="wsf_m3_supporting_text" id="wsf_description_sku">
    Eindeutiger Bezeichner für Stripe/PayPal.
</p>

<div class="wsf_error_message wsf_error_required wsf_hide" id="wsf_error_required_sku">
    <span class="dashicons dashicons-warning"></span>
    <span class="wsf_error_text">This field is required.</span>
</div>
