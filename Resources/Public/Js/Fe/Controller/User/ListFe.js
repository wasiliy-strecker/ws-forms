ws.forms.controller.user.list = {
    init: function ($,$wsf_main) {
        this.events($,$wsf_main);
        if($wsf_main.hasClass('wsf_user_list_frontend')){
         //   ws.forms.controller.user.list.showLoaders($,$wsf_main);
        }
    },
    events: function ($,$wsf_main) {
        debugger
        $wsf_main.on('click', '#wsf_user_search_btn',function() {
            ws.forms.controller.user.functions.performSearch($,$wsf_main,$wsf_main.attr('data-wsf-current-page'),'search');
        });
        $wsf_main.on('keypress', '#wsf_user_search_input', function(e) {
            if (e.which === 13) {
                ws.forms.controller.user.functions.performSearch($,$wsf_main,$wsf_main.attr('data-wsf-current-page'),'search');
            }
        });
        window.onpopstate = function(event) {
            if (event.state && (event.state['wsf_search'] !== undefined || event.state['wsf_page'] !== undefined)) {
                var s = '';
                var p = 1;
                if(event.state['wsf_search'] !== undefined){
                    s = event.state['wsf_search'];
                }
                if(event.state['wsf_page'] !== undefined){
                    p = event.state['wsf_page'];
                }
                $wsf_main.find('#wsf_user_search_input').val(s);
                // Hier AJAX ohne pushState aufrufen, um die alten Ergebnisse zu laden
                ws.forms.controller.user.functions.performSearch($,$wsf_main,p,'',true);
            }else{
                if(ws.forms.functions.isUrlEmpty()){
                    ws.forms.controller.user.functions.performSearch($,$wsf_main,1,'',true);
                }
            }
        };

        $wsf_main.on('change', '#wsf_user_page_selector', function() {
            ws.forms.controller.user.functions.performSearch($,$wsf_main,$(this).val());
        });

        $wsf_main.on('click', '.wsf_user_pagination_btn', function() {
            debugger
            ws.forms.controller.user.functions.performSearch($,$wsf_main,$(this).attr('data-wsf-page'));
        });

    },
    showLoaders: function ($,$wsf_main) {
        $wsf_main.find('.wsf_skeleton_box_circle, .wsf_skeleton_box_text').each(function () {
            var $el = $(this);
            var isCircle = $el.hasClass('wsf_skeleton_box_circle');
            $el.replaceWith('<div class="wsf_skeleton ' + (isCircle ? 'wsf_skeleton_circle' : 'wsf_skeleton_text') + '" ' +
                'style="width: ' + $el.width() + 'px; height: ' + $el.height() + 'px; display: ' + $el.css('display') + ';">' +
                '</div>');
        });
        $wsf_main.find('.wsf_skeleton_box_remove').remove();
    }
}