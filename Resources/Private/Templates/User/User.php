<div class="wsf_frontend_container wsf_login_wrapper">
    <?php if ($is_logged_in): ?>
        <div class="wsf_alert wsf_alert_success">
            <?php
            printf(
                __('Sie sind bereits als <strong>%s</strong> eingeloggt.', 'ws-forms'),
                esc_html($current_user->display_name)
            );
            ?>
            <div style="margin-top: 10px;">
                <a href="<?php echo wp_logout_url(get_permalink()); ?>" class="wsf_btn wsf_btn_outline wsf_btn_sm">
                    <?php _e('Abmelden', 'ws-forms'); ?>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="wsf_card">
            <div class="wsf_card_header">
                <h2 class="wsf_title"><?php _e('Anmelden', 'ws-forms'); ?></h2>
            </div>
            <div class="wsf_card_body">
                <form id="wsf_login_form" class="wsf_form">
                    <div class="wsf_form_group">
                        <label class="wsf_label"><?php _e('Benutzername oder E-Mail', 'ws-forms'); ?></label>
                        <input type="text" name="login" required class="wsf_input">
                    </div>
                    <div class="wsf_form_group">
                        <label class="wsf_label"><?php _e('Passwort', 'ws-forms'); ?></label>
                        <input type="password" name="password" required class="wsf_input">
                    </div>

                    <div id="wsf_login_message_container"></div>

                    <div class="wsf_form_actions">
                        <button type="submit" class="wsf_btn wsf_btn_primary wsf_w_100">
                            <?php _e('Einloggen', 'ws-forms'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>