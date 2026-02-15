<div id="wsf_main" class="wrap wsf_user_edit">
    <p>
        <a href="<?php echo admin_url('admin.php?page=ws_forms_users'); ?>">« Back to List</a>
    </p>
    <h1><?php echo esc_html($headline); ?></h1>
    <form id="wsf_form_user_edit" class="wsf_form" method="post" data-wsf-controller="user" data-wsf-action="update" data-wsf-id="<?php echo esc_attr($user->ID); ?>" >
        <?php include (__DIR__."/../../Partials/User/Fields.php"); ?>

	    <?php
	    $addressId = '';
	    $addressRowHide = 'wsf_hide';
	    $addressRowId = 'wsf_address_row_new';
	    $prefix = '___INDEX___';
	    $key = '0';
	    $address = new stdClass();
	    include (__DIR__."/../../Partials/Address/Fields.php");
	    ?>

        <div id="wsf_address_wrapper">
            <h2>Adressen</h2>
            <div id="wsf_address_container">
                <?php
                    foreach ($addresses as $address) {
	                    $addressRowHide = '';
                        $addressRowId = '';
                        $prefix       = 'address';
                        $key          = $address->id;
	                    $addressId    = $address->id;
                        include( __DIR__ . "/../../Partials/Address/Fields.php" );
                    }
                ?>
            </div>
            <button type="button" id="wsf_add_address_btn" class="button button-secondary">
                <span class="dashicons dashicons-plus-alt"></span>
                Add new address
            </button>
        </div>


        <?php submit_button('Update User'); ?>
    </form>
</div>