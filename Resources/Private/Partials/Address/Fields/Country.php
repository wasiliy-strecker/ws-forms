<div class="wsf_m3_text_field">
    <select name="<?php echo "{$prefix}[{$key}][country]"; ?>"
            id="<?php echo "{$prefix}_{$key}_country"; ?>"
            class="wsf_form_control">
        <option value="DE" <?php selected($address->country ?? 'DE', 'DE'); ?>>Deutschland</option>
        <option value="AT" <?php selected($address->country ?? '', 'AT'); ?>>Österreich</option>
        <option value="CH" <?php selected($address->country ?? '', 'CH'); ?>>Schweiz</option>
    </select>
    <label for="<?php echo "{$prefix}_{$key}_country"; ?>">Land</label>
</div>