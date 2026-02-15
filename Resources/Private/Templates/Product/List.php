<div id="wsf_main" class="wsf_product_list" data-wsf-is-admin="true">
    <h1 class="wp-heading-inline"><?php _e('Produkte', 'ws-forms'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=ws_forms_products&action=new'); ?>"
       class="page-title-action <?php echo empty($products) ? 'ws_hide' : ''; ?>">
        Neues Produkt anlegen
    </a>
    <hr class="wp-header-end">

    <?php if (!empty($products)) : ?>
        <div id="wsf_search_box">
            <input type="text" id="wsf_user_search_input" class="regular-text" placeholder="Produkte suchen (Titel oder SKU)..." value="<?php echo esc_attr($_GET['s'] ?? ''); ?>">
            <button type="button" id="wsf_user_search_btn" class="button button-primary">
                <span class="dashicons dashicons-search" style="vertical-align: middle; margin-top: 3px;"></span>
                Suchen
            </button>
        </div>
    <?php endif; ?>

    <?php if (isset($message)) : ?>
        <div id="wsf_user_message" class="updated notice notice-success is-dismissible">
            <p>
                <?php
                if ($message === 'created') _e('Produkt erfolgreich angelegt!', 'ws-forms');
                if ($message === 'updated') _e('Produkt erfolgreich bearbeitet!', 'ws-forms');
                ?>
            </p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Ausblenden</span></button>
        </div>
    <?php endif; ?>

    <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
        <thead>
        <tr>
            <th class="manage-column">ID</th>
            <th class="manage-column">Titel</th>
            <th class="manage-column">SKU</th>
            <th class="manage-column">Preis</th>
            <th class="manage-column">Status</th>
            <th class="manage-column">Aktionen</th>
        </tr>
        </thead>
        <tbody id="wsf_user_table_body">
        <?php include (__DIR__."/../../Partials/Product/TableRows.php"); ?>
        </tbody>
    </table>
</div>