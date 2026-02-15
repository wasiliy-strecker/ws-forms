<div id="wsf_main" class="wrap wsf_user_list">
    <h1 class="wp-heading-inline"><?php _e('Users', 'ws-forms'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=ws_forms_users&action=new'); ?>"
       class="page-title-action <?php echo empty($users) ? 'ws_hide' : ''; ?>">
        Neuer Benutzer
    </a><!--Example Button-->
    <hr class="wp-header-end">
    <?php if (!empty($users)) { ?>
        <div id="wsf_search_box">
            <input type="text"
                   id="wsf_user_search_input"
                   class="regular-text"
                   placeholder="Search users..."
                   value="<?php echo esc_attr($_GET['s'] ?? ''); ?>">
            <button type="button" id="wsf_user_search_btn" class="button button-primary">
                <span class="dashicons dashicons-search" style="vertical-align: middle; margin-top: 3px;"></span>
                Search
            </button>
        </div>
    <?php } ?>
    <?php if (isset($message)) { ?>
        <div id="wsf_user_message" class="updated notice notice-success is-dismissible">
            <p>
                <?php if ($message === 'created') { ?>
                    <?php _e('Benutzer erfolgreich angelegt!', 'ws-forms'); ?>
                <?php } ?>
                <?php if ($message === 'updated') { ?>
                    <?php _e('Benutzer erfolgreich bearbeitet!', 'ws-forms'); ?>
                <?php } ?>
            </p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">Diese Meldung ausblenden.</span>
            </button>
        </div>
    <?php } ?>
    <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
        <thead>
        <tr>
            <th class="manage-column">ID</th>
            <th class="manage-column">E-Mail / Login</th>
            <th class="manage-column">Vorname</th>
            <th class="manage-column">Nachname</th>
            <th class="manage-column">Adressen</th>
            <th class="manage-column">Aktionen</th>
        </tr>
        </thead>
        <tbody id="wsf_user_table_body">
            <?php include (__DIR__."/../../Partials/User/TableRows.php"); ?>
        </tbody>
    </table>
</div>