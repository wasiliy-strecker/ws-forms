ws.forms.controller.product.list = {
    init: function ($, $wsf_main) {
        this.events($, $wsf_main);
    },
    events: function ($, $wsf_main) {
        $wsf_main.on('click', '#wsf_user_search_btn', function() {
            ws.forms.controller.product.functions.performSearch($, $wsf_main, 1, 'search');
        });

        $wsf_main.on('keypress', '#wsf_user_search_input', function(e) {
            if (e.which === 13) {
                ws.forms.controller.product.functions.performSearch($, $wsf_main, 1, 'search');
            }
        });

        window.onpopstate = function(event) {
            if (event.state) {
                var s = event.state['wsf_search'] || '';
                var p = event.state['wsf_page'] || 1;
                $wsf_main.find('#wsf_user_search_input').val(s);
                ws.forms.controller.product.functions.performSearch($, $wsf_main, p, '', true);
            }
        };

        $wsf_main.on('click', '.wsf_user_pagination_btn', function() {
            ws.forms.controller.product.functions.performSearch($, $wsf_main, $(this).attr('data-wsf-page'));
        });
    }
};