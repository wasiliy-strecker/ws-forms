<div id="wsf_main" class="wsf_form_product_new">
    <p><a href="<?php echo admin_url('admin.php?page=ws_forms_products'); ?>">« Zurück zur Liste</a></p>
    <h1><?php echo esc_html($headline); ?></h1>

    <form id="wsf_form_product_new"
          class="wsf_form" method="post"
          data-wsf-controller="product"
          data-wsf-action="create">
        <?php include(__DIR__ . "/../../Partials/Product/Fields.php"); ?>
        <?php submit_button('Produkt erstellen'); ?>
    </form>
</div>
