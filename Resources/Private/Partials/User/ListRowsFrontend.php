<?php if (!empty($users)): ?>
    <?php foreach ($users as $index => $user): ?>

        <li class="wsf_m3_list_item" itemprop="itemListElement" itemscope itemtype="https://schema.org/Person">
            <meta itemprop="position" content="<?php echo $index + 1; ?>">

            <div class="wsf_m3_leading">
                <div class="wsf_m3_avatar wsf_skeleton_box_circle">
                                <span class="wsf_m3_avatar_text">
                                    <?php echo strtoupper(substr($user->wsf_first_name, 0, 1)); ?>
                                </span>
                </div>
            </div>

            <div class="wsf_m3_content">
                <div class="wsf_m3_headline wsf_skeleton_box_text">
                    <span itemprop="givenName"><?php echo esc_html($user->wsf_first_name); ?></span>
                    <span itemprop="familyName"><?php echo esc_html($user->wsf_last_name); ?></span>
                    <meta itemprop="name" content="<?php echo esc_attr($user->wsf_first_name . ' ' . $user->wsf_last_name); ?>">
                </div>
                <div class="wsf_m3_supporting_text wsf_skeleton_box_text">
                    <span itemprop="email"><?php echo esc_html($user->user_email); ?></span>
                </div>
                <meta itemprop="identifier" content="<?php echo $user->ID; ?>">
            </div>

            <div class="wsf_m3_trailing ">
                <?php if(intval($user->address_count) > 0): ?>
                    <span class="wsf_m3_badge wsf_skeleton_box_remove" title="<?php _e('Adressen', 'ws-forms'); ?>">
                                    <?php echo intval($user->address_count); ?>
                                </span>
                <?php endif; ?>
                <a href="?action=edit&id=<?php echo $user->ID; ?>"
                   class="wsf_m3_icon_btn wsf_skeleton_box_circle"
                   aria-label="<?php _e('Benutzer bearbeiten', 'ws-forms'); ?>">
                    <span class="dashicons dashicons-edit"></span>
                </a>
            </div>
        </li>
    <?php endforeach; ?>
<?php else: ?>
    <li class="wsf_m3_list_item wsf_justify_content_center">
        <div class="wsf_m3_supporting_text">
            <?php _e('Keine Benutzer gefunden.', 'ws-forms'); ?>
        </div>
    </li>
<?php endif; ?>
