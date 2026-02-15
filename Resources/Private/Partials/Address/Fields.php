<div id="<?php echo $addressRowId; ?>" class="wsf_address_row postbox <?php echo $addressRowHide; ?>"
     data-wsf-id="<?php echo $addressId; ?>"
     style="padding: 15px; margin-bottom: 20px; position: relative;">
    <button type="button"
            class="button wsf_remove_address button-link-delete">
        <span class="dashicons dashicons-no-alt" style="vertical-align: middle;"></span>
        Remove
    </button>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="wsf_row"><label>Straße & Hausnummer *</label></th>
            <td class="wsf_input_box">
                <?php include (__DIR__.'/Fields/Street.php')?>
            </td>
        </tr>
        <tr>
            <th scope="wsf_row"><label>PLZ / Stadt *</label></th>
            <td class="wsf_input_box">
                <?php include (__DIR__.'/Fields/ZipCity.php')?>
            </td>
        </tr>
        <tr>
            <th scope="wsf_row"><label>Land</label></th>
            <td class="wsf_input_box">
                <?php include (__DIR__.'/Fields/Country.php')?>
            </td>
        </tr>
    </table>
</div>
