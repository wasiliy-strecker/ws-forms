<div id="wsf_main" class="wsf_order_list" data-wsf-is-admin="true">
    <h1 class="wp-heading-inline"><?php _e('Bestellungen', 'ws-forms'); ?></h1>
    <hr class="wp-header-end">

    <?php if (!empty($orders)) : ?>
        <div id="wsf_search_box">
            <input type="text" id="wsf_user_search_input" class="regular-text" placeholder="Bestellnummer suchen..." value="<?php echo esc_attr($_GET['wsf_search'] ?? ''); ?>">
            <button type="button" id="wsf_user_search_btn" class="button button-primary">
                <span class="dashicons dashicons-search" style="vertical-align: middle; margin-top: 3px;"></span>
                Suchen
            </button>
        </div>
    <?php endif; ?>

    <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
        <thead>
        <tr>
            <th class="manage-column">ID</th>
            <th class="manage-column">Bestellnummer</th>
            <th class="manage-column">Kunde (ID)</th>
            <th class="manage-column">Gesamt (Brutto)</th>
            <th class="manage-column">Status</th>
            <th class="manage-column">Datum</th>
            <th class="manage-column">Aktionen</th>
        </tr>
        </thead>
        <tbody id="wsf_user_table_body">
        <?php include (__DIR__."/../../Partials/Order/TableRows.php"); ?>
        </tbody>
    </table>
</div>
