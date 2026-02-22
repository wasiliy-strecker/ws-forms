<div id="wsf_main" class="container wsf_mt_5 wsf_user_new wsf_m3_container">
    <a href="<?php echo $pageUrl; ?>" class="wsf_m3_btn wsf_mb_3" style="padding-left:0; color: var(--wsf-m3-primary);">
        <span class="dashicons dashicons-arrow-left-alt"></span> Back to List
    </a>

    <h1 class="wsf_m3_display_small wsf_mb_4"><?php echo esc_html($headline); ?></h1>

    <form id="wsf_form_user_new" class="wsf_form" method="post" data-wsf-controller="user" data-wsf-action="create">

        <div class="wsf_mb_4">
            <?php include (__DIR__."/../../Partials/User/FieldsFrontend.php"); ?>
        </div>

        <?php
        $addressId = '';
        $addressRowHide = 'wsf_hide';
        $addressRowId = 'wsf_address_row_new';
        $prefix = '___INDEX___';
        $key = '0';
        $address = new stdClass();
        // Das Template (versteckt) laden
        include (__DIR__."/../../Partials/Address/FieldsFrontend.php");
        ?>

        <div id="wsf_address_wrapper" class="wsf_mb_4">
            <h2 class="wsf_m3_display_small wsf_mb_3">Addresses</h2>
            <div id="wsf_address_container">
            </div>

            <button type="button" id="wsf_add_address_btn" class="wsf_m3_btn wsf_m3_btn_outlined wsf_mt_2">
                <span class="dashicons dashicons-plus"></span> Add new address
            </button>
        </div>

        <button type="submit" class="wsf_m3_btn wsf_m3_btn_filled">
            Create User
        </button>
    </form>
</div>