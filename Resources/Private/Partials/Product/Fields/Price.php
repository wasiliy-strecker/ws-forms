<div class="wsf_m3_text_field">
    <input name="product[price]" type="number" step="0.01" id="wsf_product_price"
           value="<?php echo esc_attr($product->price); ?>"
           class="wsf_form_control wsf_error_required" placeholder=" "
           aria-describedby="wsf_description_price wsf_error_required_price">
    <label for="wsf_product_price">Preis (€) *</label>
</div>

<p class="wsf_m3_supporting_text" id="wsf_description_price">
    Please enter the product price.
</p>

<div class="wsf_error_message wsf_error_required wsf_hide" id="wsf_error_required_price">
    <span class="dashicons dashicons-warning"></span>
    <span class="wsf_error_text">This field is required.</span>
</div>
