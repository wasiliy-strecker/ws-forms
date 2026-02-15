<table class="form-table" role="presentation">
    <tr>
        <th scope="wsf_row"><label for="wsf_product_title">Produkt Name *</label></th>
        <td class="wsf_input_box">
            <?php include(__DIR__.'/Fields/Title.php'); ?>
        </td>
    </tr>
    <tr>
        <th scope="wsf_row"><label for="wsf_product_sku">SKU *</label></th>
        <td class="wsf_input_box">
            <?php include(__DIR__.'/Fields/Sku.php'); ?>
        </td>
    </tr>
    <tr>
        <th scope="wsf_row"><label for="wsf_product_price">Preis *</label></th>
        <td class="wsf_input_box">
            <?php include(__DIR__.'/Fields/Price.php'); ?>
        </td>
    </tr>
    <tr>
        <th scope="wsf_row"><label for="wsf_product_tax">MwSt.</label></th>
        <td class="wsf_input_box">
            <?php include(__DIR__.'/Fields/Tax.php'); ?>
        </td>
    </tr>
    <tr>
        <th scope="wsf_row"><label>Produkt Bilder</label></th>
        <td>
            <div id="wsf_product_media_container" class="wsf_media_preview_container" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px;">
                <!-- Previews go here -->
            </div>
            <button type="button" id="wsf_add_media_btn" class="button">Media hinzufügen</button>
            <input type="hidden" name="product[media_ids]" id="wsf_product_media_ids" value="">
        </td>
    </tr>
</table>