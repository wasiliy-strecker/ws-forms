<div class="wsf_m3_text_field">
    <input name="user[wsf_email]"
           type="text"
           id="wsf_email"
           value="<?php echo esc_attr($user->user_email); ?>"
        <?php echo !empty($isEdit) ? 'disabled' : '';?>
           class="wsf_form_control wsf_error_required wsf_error_email"
           placeholder=" "
           aria-describedby="wsf_description_email wsf_error_required_email wsf_error_email wsf_error_email_exists">
    <label for="wsf_email">Email *</label>
</div>

<p class="wsf_m3_supporting_text" id="wsf_description_email">
    Please enter the email of the user.
</p>

<div class="wsf_error_message wsf_error_required wsf_hide" id="wsf_error_required_email">
    <span class="dashicons dashicons-warning"></span> <span class="wsf_error_text">This field is required.</span>
</div>
<div class="wsf_error_message wsf_error_email wsf_hide" id="wsf_error_email">
    <span class="dashicons dashicons-warning"></span> <span class="wsf_error_text">This email is invalid.</span>
</div>
<div class="wsf_error_message wsf_error_email_exists wsf_hide" id="wsf_error_email_exists">
    <span class="dashicons dashicons-warning"></span> <span class="wsf_error_text">This email exists already.</span>
</div>