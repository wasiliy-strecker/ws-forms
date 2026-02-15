<div class="wsf_m3_text_field">
    <input name="<?php echo "{$prefix}[{$key}][street]"; ?>"
           type="text"
           id="<?php echo "{$prefix}_{$key}_street"; ?>"
           value="<?php echo esc_attr($address->street ?? ''); ?>"
           class="wsf_form_control wsf_error_required"
           placeholder=" ">
    <label for="<?php echo "{$prefix}_{$key}_street"; ?>">Straße & Hausnummer *</label>
</div>
<div class="wsf_error_message wsf_error_required wsf_hide">
    <span class="dashicons dashicons-warning"></span>
    <span class="wsf_error_text">Dieses Feld ist erforderlich.</span>
</div>