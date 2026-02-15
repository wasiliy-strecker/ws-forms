<div class="wsf_m3_text_field">
    <input name="user[wsf_first_name]"
           type="text"
           id="wsf_first_name"
           value="<?php echo esc_attr($user->wsf_first_name); ?>"
           class="wsf_form_control wsf_error_required"
           placeholder=" "
           aria-describedby="wsf_description_first_name wsf_error_required_first_name">
    <label for="wsf_first_name">First Name *</label>
</div>

<p class="wsf_m3_supporting_text" id="wsf_description_first_name">
    Please enter the legal first name of the user.
</p>

<div class="wsf_error_message wsf_error_required wsf_hide" id="wsf_error_required_first_name">
    <span class="dashicons dashicons-warning"></span>
    <span class="wsf_error_text">This field is required.</span>
</div>