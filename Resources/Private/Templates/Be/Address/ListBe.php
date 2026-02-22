<?php
/** @var array $addresses */
/** @var string $headline */
?>
<div class="wrap">
    <h1><?php echo esc_html($headline); ?></h1>

    <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
        <thead>
        <tr>
            <th style="width: 80px;">PLZ</th>
            <th>Stadt</th>
            <th>Straße</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($addresses as $address): ?>
            <tr>
                <td><?php echo esc_html($address['zip']); ?></td>
                <td><strong><?php echo esc_html($address['city']); ?></strong></td>
                <td><?php echo esc_html($address['street']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>