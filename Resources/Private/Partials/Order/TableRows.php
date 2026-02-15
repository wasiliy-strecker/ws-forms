<?php foreach ($orders as $item): ?>
    <tr>
        <td><?php echo $item->id; ?></td>
        <td><strong><?php echo esc_html($item->order_number); ?></strong></td>
        <td><?php echo $item->user_id; ?></td>
        <td><?php echo number_format($item->total_price_brutto, 2, ',', '.'); ?> <?php echo esc_html($item->currency); ?></td>
        <td>
            <span class="wsf_badge wsf_badge_<?php echo esc_attr($item->status); ?>">
                <?php echo ucfirst($item->status); ?>
            </span>
        </td>
        <td><?php echo $item->created_at; ?></td>
        <td>
            <a href="<?php echo admin_url('admin.php?page=ws_forms_orders&action=edit&id='.$item->id); ?>" class="button button-small">Details</a>
        </td>
    </tr>
<?php endforeach; ?>
<?php if (empty($orders)): ?>
    <tr><td colspan="7">Keine Bestellungen gefunden.</td></tr>
<?php endif; ?>
