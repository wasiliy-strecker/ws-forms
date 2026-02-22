<div id="wsf_main" class="wsf_frontend_container wsf_user_login">
    <?php if (isset($is_logged_in) && $is_logged_in): ?>

        <div class="wsf_alert wsf_alert_success">
            <?php
            printf(
                __('Sie sind bereits als <strong>%s</strong> eingeloggt.', 'ws-forms'),
                esc_html($current_user->display_name)
            );
            ?>
            <div style="margin-top: 10px;">
                <a href="<?php echo wp_logout_url(get_permalink()); ?>" class="btn btn-outline-secondary btn-sm">
                    <?php _e('Abmelden', 'ws-forms'); ?>
                </a>
            </div>
        </div>

    <?php else: ?>

        <div class="wsf_card">
            <div class="wsf_card_header">
                <?php _e('Anmelden', 'ws-forms'); ?>
            </div>

            <div class="wsf_card_body">
                <form id="wsf_login_form" class="wsf_form">

                    <div class="wsf_row wsf_mb_3">
                        <div class="wsf_col_md_12 wsf_input_box">
                            <label class="form-label"><?php _e('Benutzername oder E-Mail', 'ws-forms'); ?></label>
                            <input type="text" name="login" required class="wsf_form_control">

                            <div class="wsf_error_message wsf_hide">
                                <span class="dashicons dashicons-warning"></span>
                                <span class="wsf_error_text"><?php _e('Dieses Feld ist erforderlich.', 'ws-forms'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="wsf_row wsf_mb_3">
                        <div class="wsf_col_md_12 wsf_input_box">
                            <label class="form-label"><?php _e('Passwort', 'ws-forms'); ?></label>
                            <input type="password" name="password" required class="wsf_form_control">

                            <div class="wsf_error_message wsf_hide">
                                <span class="dashicons dashicons-warning"></span>
                                <span class="wsf_error_text"><?php _e('Dieses Feld ist erforderlich.', 'ws-forms'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div id="wsf_login_message_container" class="wsf_mb_3"></div>

                    <button type="submit" class="btn btn-primary wsf_w_100">
                        <?php _e('Einloggen', 'ws-forms'); ?>
                    </button>

                </form>
            </div>
        </div>

    <?php endif; ?>
</div>