<?php if (!empty($users)): ?>
    <?php foreach ($users as $user): ?>
        <tr id="wsf_user_row_<?php echo $user->ID; ?>">
            <td><?php echo $user->ID; ?></td>
            <td><strong><?php echo esc_html($user->user_email); ?></strong></td>
            <td><?php echo esc_html($user->wsf_first_name); ?></td>
            <td><?php echo esc_html($user->wsf_last_name); ?></td>
            <td><?php echo esc_html($user->address_count); ?></td>
            <td>
                <a href="?page=ws_forms_users&action=edit&id=<?php echo $user->ID; ?>" class="button button-small">
                    Bearbeiten
                </a>
                <button type="button"
                        class="button button-small button-link-delete wsf_hide"
                        data-wsf-id="<?php echo $user->ID; ?>" >
                    Löschen
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="6" style="text-align: center; padding: 20px;">
            Keine Benutzer gefunden. <a href="?page=ws_forms_users&action=new">Jetzt den ersten Benutzer anlegen</a>.
        </td>
    </tr>
<?php endif; ?>