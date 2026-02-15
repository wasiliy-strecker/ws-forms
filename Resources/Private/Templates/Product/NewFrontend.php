<div class="wsf_frontend_form">
    <h2><?php echo esc_html($headline); ?></h2>
    <p><a href="<?php echo home_url('/produkte/'); ?>">« Zurück zur Liste</a></p>

    <form id="wsf_form_product_new"
          class="wsf_form" method="post"
          data-wsf-controller="product"
          data-wsf-action="create">
        <?php include(__DIR__ . "/../../Partials/Product/Fields.php"); ?>
        <button type="submit" class="wsf_button">Produkt erstellen</button>
    </form>
</div>
