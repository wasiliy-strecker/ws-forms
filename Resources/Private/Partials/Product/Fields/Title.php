<div class="wsf_m3_text_field">
    <input name="product[title]" type="text" id="wsf_product_title"
           value="<?php echo esc_attr($product->title); ?>"
           class="wsf_form_control wsf_error_required" placeholder=" "
           aria-describedby="wsf_description_title wsf_error_required_title">
    <label for="wsf_product_title">Product Title *</label>
</div>
<p class="wsf_m3_supporting_text" id="wsf_description_title">
    Please enter the product title.
</p>
<div class="wsf_error_message wsf_error_required wsf_hide" id="wsf_error_required_title">
    <span class="dashicons dashicons-warning"></span>
    <span class="wsf_error_text">This field is required.</span>
</div>
