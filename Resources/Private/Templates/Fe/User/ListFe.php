<div id="wsf_main" class="container wsf_user_list wsf_user_list_frontend wsf_mt_5 wsf_m3_container"
     data-wsf-is-admin="<?php echo $isAdmin ? 'true' : 'false'; ?>"
     data-wsf-current-page="<?php echo $currentPage; ?>"
>
    <div class="wsf_d_flex wsf_justify_content_between wsf_align_items_center wsf_mb_4">
        <h1 class="wsf_m3_display_small">User List</h1>
        <a href="?action=new" class="wsf_m3_btn wsf_m3_btn_filled">
            <span class="dashicons dashicons-plus" style="margin-right: 8px;"></span>
            New User
        </a>
    </div>

    <?php if (isset($message)): ?>
        <div id="wsf_frontend_message" class="wsf_alert wsf_alert_success wsf_alert_dismissible" role="alert">
            <div class="wsf_alert_content">
                <?php
                if ($message === 'created') {
                    _e('Benutzer erfolgreich angelegt!', 'ws-forms');
                } elseif ($message === 'updated') {
                    _e('Benutzer erfolgreich bearbeitet!', 'ws-forms');
                }
                ?>
            </div>
            <button type="button" class="wsf_alert_close" onclick="this.parentElement.style.display='none';" aria-label="Close">
                &times;
            </button>
        </div>
    <?php endif; ?>

    <div class="wsf_m3_search_bar">
        <span class="dashicons dashicons-search wsf_m3_search_leading_icon" aria-hidden="true"></span>
        <input id="wsf_user_search_input" type="text"
               name="s"
               class="wsf_m3_search_input"
               value="<?php echo esc_attr($searchQuery ?? ''); ?>"
               placeholder="Search users..."
               aria-label="Search users">
        <button id="wsf_user_search_btn" type="submit" class="wsf_m3_search_btn" aria-label="Start search">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
        </button>
        <button type="button" class="wsf_m3_search_btn wsf_hide" id="wsf_search_clear" aria-label="Clear search">
            <span class="dashicons dashicons-no"></span>
        </button>
    </div>

    <?php include (__DIR__."/../../Partials/User/PaginationFrontend.php"); ?>

    <div class="wsf_m3_card">
        <ul id="wsf_m3_list" class="wsf_m3_list" itemscope itemtype="https://schema.org/ItemList">
            <meta itemprop="name" content="Mitgliederverzeichnis">
            <meta itemprop="description" content="Liste aller registrierten Benutzer im System">
            <?php include (__DIR__."/../../Partials/User/ListRowsFrontend.php"); ?>
        </ul>
    </div>
</div>

