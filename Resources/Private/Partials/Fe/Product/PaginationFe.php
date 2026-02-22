<div id="wsf_pagination_wrapper" class="wsf_pagination_wrapper">

    <div class="wsf_pagination_rows_selector">
        <label for="wsf_product_page_selector">Seite wählen</label>
        <select id="wsf_product_page_selector" class="wsf_pagination_select">
            <?php
            // Sicherheits-Check: Mindestens 1 Seite
            $totalPages = max(1, $totalPages);

            for ($i = 1; $i <= $totalPages; $i++):
                ?>
                <option value="<?php echo $i; ?>" <?php selected($currentPage, $i); ?>>
                    <?php echo $i; ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="wsf_pagination_nav">

        <span class="wsf_pagination_text">
            <?php echo esc_html($startEntry); ?>-<?php echo esc_html($endEntry); ?> von <?php echo esc_html($totalUsers); ?>
        </span>

        <button type="button" class="wsf_icon_btn wsf_user_pagination_btn"
                aria-label="Erste Seite"
                data-wsf-page="1"
            <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>>
            <svg viewBox="0 0 24 24"><path d="M18.41 16.59L13.82 12l4.59-4.59L17 6l-6 6 6 6zM6 6h2v12H6z"/></svg>
        </button>

        <button type="button" class="wsf_icon_btn wsf_user_pagination_btn"
                aria-label="Vorherige Seite"
                data-wsf-page="<?php echo max(1, $currentPage - 1); ?>"
            <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>>
            <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
        </button>

        <button type="button" class="wsf_icon_btn wsf_user_pagination_btn"
                aria-label="Nächste Seite"
                data-wsf-page="<?php echo min($totalPages, $currentPage + 1); ?>"
            <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>>
            <svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
        </button>

        <button type="button" class="wsf_icon_btn wsf_user_pagination_btn"
                aria-label="Letzte Seite"
                data-wsf-page="<?php echo $totalPages; ?>"
            <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>>
            <svg viewBox="0 0 24 24"><path d="M5.59 7.41L10.18 12l-4.59 4.59L7 18l6-6-6-6zM16 6h2v12h-2z"/></svg>
        </button>

    </div>
</div>
