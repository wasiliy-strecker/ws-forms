<div id="wsf_main" class="wsf_form_product_new">
    <p><a href="<?php echo admin_url('admin.php?page=ws_forms_products'); ?>">« Zurück zur Liste</a></p>
    <h1><?php echo esc_html($headline); ?></h1>

    <div class="wsf_ai_chat_container" style="background: #f0f0f1; padding: 20px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ccc;">
        <h3>AI Assistent</h3>
        <p>Beschreibe das Produkt, das du anlegen möchtest (z.B. "Ein rotes T-Shirt für 25 Euro").</p>
        <div style="display: flex; gap: 10px;">
            <input type="text" id="wsf_ai_prompt" style="flex-grow: 1;" placeholder="Produkt beschreiben...">
            <button type="button" id="wsf_ai_analyze_btn" class="button button-secondary">Analysieren</button>
        </div>
        <div id="wsf_ai_status" style="margin-top: 10px; font-style: italic; color: #666;"></div>
    </div>

    <form id="wsf_form_product_new"
          class="wsf_form" method="post"
          data-wsf-controller="product"
          data-wsf-action="create">
        <?php include(__DIR__ . "/../../Partials/Product/Fields.php"); ?>
        <?php submit_button('Produkt erstellen'); ?>
    </form>
</div>
