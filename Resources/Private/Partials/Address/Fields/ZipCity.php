<div class="wsf_m3_row">
    <div style="flex: 1;">
        <div class="wsf_m3_text_field">
            <input name="<?php echo "{$prefix}[{$key}][zip]"; ?>"
                   type="text"
                   id="<?php echo "{$prefix}_{$key}_zip"; ?>"
                   value="<?php echo esc_attr($address->zip ?? ''); ?>"
                   class="wsf_form_control wsf_error_required"
                   placeholder=" ">
            <label for="<?php echo "{$prefix}_{$key}_zip"; ?>">PLZ *</label>
        </div>
    </div>

    <div style="flex: 2;">
        <div class="wsf_m3_text_field">
            <input name="<?php echo "{$prefix}[{$key}][city]"; ?>"
                   type="text"
                   id="<?php echo "{$prefix}_{$key}_city"; ?>"
                   value="<?php echo esc_attr($address->city ?? ''); ?>"
                   class="wsf_form_control wsf_error_required wsf_form_error"
                   placeholder=" ">
            <label for="<?php echo "{$prefix}_{$key}_city"; ?>">Stadt *</label>
        </div>
    </div>
</div>

<div class="wsf_error_message wsf_error_required wsf_hide">
    <span class="dashicons dashicons-warning"></span>
    <span class="wsf_error_text">PLZ und Stadt sind erforderlich.</span>
</div>