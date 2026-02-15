<div class="wsf_m3_text_field">
    <input name="user[wsf_last_name]"
           type="text"
           id="wsf_last_name"
           value="<?php echo esc_attr($user->wsf_last_name); ?>"
           class="wsf_form_control wsf_error_required"
           placeholder=" "
           aria-describedby="wsf_description_last_name wsf_error_required_last_name">
    <label for="wsf_last_name">Last Name *</label>
</div>

<p class="wsf_m3_supporting_text" id="wsf_description_last_name">
    Please enter the legal last name of the user.
</p>

<div class="wsf_error_message wsf_error_required wsf_hide" id="wsf_error_required_last_name">
    <span class="dashicons dashicons-warning"></span>
    <span class="wsf_error_text">This field is required.</span>
</div>