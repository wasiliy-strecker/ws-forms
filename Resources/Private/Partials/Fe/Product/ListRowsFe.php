<?php if (!empty($products)): ?>
    <?php foreach ($products as $index => $item): ?>
        <li class="wsf_m3_list_item" itemprop="itemListElement" itemscope itemtype="https://schema.org/Product">
            <meta itemprop="position" content="<?php echo $index + 1; ?>">

            <div class="wsf_m3_leading">
                <div class="wsf_m3_avatar wsf_skeleton_box_circle">
                    <span class="wsf_m3_avatar_text">
                        <?php echo strtoupper(substr($item->title, 0, 1)); ?>
                    </span>
                </div>
            </div>

            <div class="wsf_m3_content">
                <div class="wsf_m3_headline wsf_skeleton_box_text">
                    <span itemprop="name"><?php echo esc_html($item->title); ?></span>
                </div>
                <div class="wsf_m3_supporting_text wsf_skeleton_box_text">
                    <span itemprop="sku">SKU: <?php echo esc_html($item->sku); ?></span> |
                    <span itemprop="price" content="<?php echo $item->price; ?>">
                        <?php echo number_format($item->price, 2, ',', '.'); ?> <?php echo esc_html($item->currency); ?>
                    </span>
                </div>
                <meta itemprop="identifier" content="<?php echo $item->id; ?>">
            </div>

            <div class="wsf_m3_trailing">
                <button type="button"
                        class="wsf_m3_icon_btn wsf_skeleton_box_circle wsf_add_to_cart_btn"
                        data-wsf-id="<?php echo $item->id; ?>"
                        data-wsf-title="<?php echo esc_attr($item->title); ?>"
                        data-wsf-price="<?php echo $item->price; ?>"
                        data-wsf-sku="<?php echo esc_attr($item->sku); ?>"
                        aria-label="In den Warenkorb">
                    <span class="dashicons dashicons-cart"></span>
                </button>
            </div>
        </li>
    <?php endforeach; ?>
<?php else: ?>
    <li class="wsf_m3_list_item wsf_justify_content_center">
        <div class="wsf_m3_supporting_text">
            Keine Produkte gefunden.
        </div>
    </li>
<?php endif; ?>
