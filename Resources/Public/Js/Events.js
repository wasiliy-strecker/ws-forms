ws.forms.events = {
    init: function($,$wsf_main) {
        if($wsf_main.find('.wsf_form')) {
            $wsf_main.on('input','.wsf_form input', function() {
                var $this = $(this);
                clearTimeout(window._wsf_form_input);
                window._wsf_form_input = setTimeout(function () {
                    var $form = $this.closest('.wsf_form');
                    $form.find('.wsf_error_email_exists').addClass('wsf_hide');
                    if($form.hasClass('wsf_error_submitted')){
                        ws.forms.validation.exec($,$form);
                    }
                }, 300);
            });
        }
        if($wsf_main.find('.notice-dismiss')) {
            $wsf_main.on('click','.notice-dismiss', function() {
                $(this).closest('.notice').fadeOut();
            });
        }
    }
}