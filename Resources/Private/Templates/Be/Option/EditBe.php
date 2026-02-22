<div id="wsf_main" class="wrap wsf_option_edit">

    <p>
        <a href="<?php echo admin_url('admin.php?page=ws_forms_main'); ?>">« Back to Dashboard</a>
    </p>

    <h1><?php echo esc_html($headline); ?></h1>

    <?php if (isset($message) && $message === 'updated'): ?>
        <div id="message" class="updated notice notice-success is-dismissible">
            <p>
                <?php if ($message === 'updated') { ?>
                    <?php _e('Einstellungen erfolgreich gespeichert.', 'ws-forms'); ?>
                <?php } ?>
            </p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">Diese Meldung ausblenden.</span>
            </button>
        </div>
    <?php endif; ?>

    <form id="wsf_form_option_edit" class="wsf_form"
          method="post"
          data-wsf-controller="option" data-wsf-action="update"
    >

        <?php wp_nonce_field('wsf_save_option', 'wsf_nonce'); ?>

        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="wsf_row">
                    <label for="wsf_option_user_backend_role">Standard Backend Rolle</label>
                </th>
                <td class="wsf_input_box">
                    <div class="wsf_m3_text_field">
                        <select name="wsf_option[backend_role]" id="wsf_option_user_backend_role" class="wsf_form_control">
                            <?php foreach ($roles as $roleKey => $roleName): ?>
                                <option value="<?php echo esc_attr($roleKey); ?>"
                                    <?php selected($option->userBackendRole, $roleKey); ?>>
                                    <?php echo esc_html($roleName); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="wsf_option_user_backend_role">Standard Backend Rolle</label>
                    </div>

                    <p class="wsf_m3_supporting_text">
                        Rolle für neue Admin-Benutzer, die über das Plugin erstellt werden.
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="wsf_row">
                    <label for="wsf_option_user_frontend_role">Standard Frontend Rolle</label>
                </th>
                <td class="wsf_input_box">
                    <div class="wsf_m3_text_field">
                        <select name="wsf_option[frontend_role]" id="wsf_option_user_frontend_role" class="wsf_form_control">
                            <?php foreach ($roles as $roleKey => $roleName): ?>
                                <option value="<?php echo esc_attr($roleKey); ?>"
                                    <?php selected($option->userFrontendRole, $roleKey); ?>>
                                    <?php echo esc_html($roleName); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="wsf_option_user_frontend_role">Standard Frontend Rolle</label>
                    </div>

                    <p class="wsf_m3_supporting_text">
                        Standardrolle für Registrierungen über das Frontend-Formular.
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="wsf_row">
                    <label for="wsf_option_openai_api_key">OpenAI API Key</label>
                </th>
                <td class="wsf_input_box">
                    <div class="wsf_m3_text_field">
                        <input type="password" name="wsf_option[openai_api_key]" id="wsf_option_openai_api_key" 
                               value="<?php echo esc_attr($option->openaiApiKey); ?>" class="wsf_form_control">
                        <label for="wsf_option_openai_api_key">OpenAI API Key</label>
                    </div>
                    <p class="wsf_m3_supporting_text">
                        API Key für die Kommunikation mit ChatGPT (wird für AI Produkt-Erstellung genutzt).
                    </p>
                </td>
            </tr>
            </tbody>
        </table>

        <p class="submit">
            <button type="submit" name="submit" id="submit" class="button button-primary">
                <span class="dashicons dashicons-saved" style="vertical-align: middle; margin-right: 5px;"></span>
                <?php _e('Save Changes', 'ws-forms'); ?>
            </button>
        </p>

    </form>
</div>