<div id="wsf_main" class="wsf_product_edit">
    <p><a href="<?php echo admin_url('admin.php?page=ws_forms_products'); ?>">« Zurück zur Liste</a></p>
    <h1><?php echo esc_html($headline); ?></h1>

    <form id="wsf_form_product_<?php echo !empty($isEdit) ? 'edit' : 'new'; ?>"
          class="wsf_form" method="post"
          data-wsf-controller="product"
          data-wsf-action="<?php echo !empty($isEdit) ? 'update' : 'create'; ?>"
        <?php echo !empty($isEdit) ? 'data-wsf-id="'.esc_attr($product->id).'"' : ''; ?>>

        <?php include (__DIR__."/../../Partials/Product/Fields.php"); ?>
        <?php submit_button(!empty($isEdit) ? 'Produkt aktualisieren' : 'Produkt erstellen'); ?>
    </form>
</div>