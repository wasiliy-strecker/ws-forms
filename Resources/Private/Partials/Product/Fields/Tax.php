<div class="wsf_m3_text_field">
    <select name="product[tax_rate]" id="wsf_product_tax" class="wsf_form_control wsf_error_required"
            aria-describedby="wsf_description_tax wsf_error_required_tax">
        <option value="19.00" <?php selected($product->tax_rate, '19.00'); ?>>19%</option>
        <option value="7.00" <?php selected($product->tax_rate, '7.00'); ?>>7%</option>
        <option value="0.00" <?php selected($product->tax_rate, '0.00'); ?>>0%</option>
    </select>
    <label for="wsf_product_tax">MwSt.</label>
</div>
<p class="wsf_m3_supporting_text" id="wsf_description_tax">
    Please select the tax rate.
</p>
<div class="wsf_error_message wsf_error_required wsf_hide" id="wsf_error_required_tax">
    <span class="dashicons dashicons-warning"></span>
    <span class="wsf_error_text">This field is required.</span>
</div>

