ws.forms.controller.product.functions = {
    createPayload: function ($, $form) {
        var formData = $form.serializeArray();
        var payload = {
            product: {},
            current_url: window.location.origin + window.location.pathname
        };
        $.each(formData, function() {
            if (this.name.indexOf('product[') === 0) {
                var name = this.name.replace('product[', '').replace(']', '');
                payload.product[name] = this.value.trim();
            } else {
                payload[this.name] = this.value.trim();
            }
        });
        return payload;
    },

    performSearch: function ($, $wsf_main, page, action, isFromOnpopstate) {
        var $wsf_search_input = $wsf_main.find('#wsf_user_search_input'); // Selektor aus List.php
        var $wsf_search_btn = $wsf_main.find('#wsf_user_search_btn');

        if (!page) page = 1;
        var isAdmin = ($wsf_main.attr('data-wsf-is-admin') === 'true');

        if (isAdmin) {
            var $wsf_table_body = $wsf_main.find('#wsf_user_table_body').addClass('wsf_table_load');
        }

        var searchQuery = $wsf_search_input.val().trim();
        var urlParams = new URLSearchParams(window.location.search);

        if (searchQuery.length > 0) { urlParams.set('wsf_search', searchQuery); } else { urlParams.delete('wsf_search'); }
        if (page > 1) { urlParams.set('wsf_page', page); } else { urlParams.delete('wsf_page'); }

        var newUrl = window.location.pathname + '?' + urlParams.toString();

        if (!isFromOnpopstate) {
            history.pushState({ 'wsf_search': searchQuery, 'wsf_page': page }, '', newUrl);
        }

        if (action == 'search' && isAdmin) {
            ws.forms.functions.toggleSubmitButtonLoader($wsf_search_btn, true);
        }

        $.ajax({
            url: wsf_rest.api_url + '/product/list',
            method: 'GET',
            data: { 'wsf_search': searchQuery, 'wsf_page': page },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wsf_rest.nonce);
            }
        })
            .done(function(response) {
                if (isAdmin) {
                    $wsf_table_body.html(response.html).removeClass('wsf_table_load');
                } else {
                    $wsf_main.find('#wsf_m3_list').html(response.html);
                    $wsf_main.find('#wsf_pagination_wrapper').replaceWith($(response.pagination));
                }
                ws.forms.functions.toggleSubmitButtonLoader($wsf_search_btn, false);
            })
            .fail(function() {
                alert('Fehler beim Laden der Produkte.');
                if (isAdmin) $wsf_table_body.removeClass('wsf_table_load');
                ws.forms.functions.toggleSubmitButtonLoader($wsf_search_btn, false);
            });
    }
};