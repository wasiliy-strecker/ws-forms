<?php
/** @var array $forms */
/** @var string $title */
?>
<div class="ws_form_wrapper">
    <h2><?php echo esc_html($title); ?></h2>
    <ul>
        <?php foreach ($forms as $form): ?>
            <li><?php echo esc_html($form->getTitle()); ?></li>
        <?php endforeach; ?>
    </ul>
</div>