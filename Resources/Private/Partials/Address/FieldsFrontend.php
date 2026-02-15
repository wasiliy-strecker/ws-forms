<div id="<?php echo $addressRowId; ?>" class="wsf_address_row wsf_m3_card wsf_mb_3 <?php echo $addressRowHide; ?>" data-wsf-id="<?php echo $addressId; ?>">
    <div class="wsf_card_header wsf_d_flex wsf_justify_content_between wsf_align_items_center" style="padding: 16px; border-bottom: 1px solid var(--wsf-m3-outline-variant);">
        <h3 class="wsf_m3_headline" style="margin:0;">Address</h3>
        <button type="button" class="btn-close wsf_remove_address" aria-label="Remove"></button>
    </div>
    <div class="wsf_card_body" style="padding: 16px;">
        <div class="wsf_mb_3">
            <?php include (__DIR__.'/Fields/Street.php')?>
        </div>
        <div class="wsf_mb_3">
            <?php include (__DIR__.'/Fields/ZipCity.php')?>
        </div>
        <div>
            <?php include (__DIR__.'/Fields/Country.php')?>
        </div>
    </div>
</div>