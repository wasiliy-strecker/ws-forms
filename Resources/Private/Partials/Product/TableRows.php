<?php foreach ($products as $item): ?>
    <tr>
        <td><?php echo $item->id; ?></td>
        <td><strong><?php echo esc_html($item->title); ?></strong></td>
        <td><code><?php echo esc_html($item->sku); ?></code></td>
        <td><?php echo number_format($item->price, 2, ',', '.'); ?> <?php echo esc_html($item->currency); ?></td>
        <td>
            <span class="wsf_badge wsf_badge_<?php echo esc_attr($item->status); ?>">
                <?php echo ucfirst($item->status); ?>
            </span>
        </td>
        <td>
            <a href="<?php echo admin_url('admin.php?page=ws_forms_products&action=edit&id='.$item->id); ?>" class="button button-small">Bearbeiten</a>
        </td>
    </tr>
<?php endforeach; ?>
<?php if (empty($products)): ?>
    <tr><td colspan="6">Keine Produkte gefunden.</td></tr>
<?php endif; ?>