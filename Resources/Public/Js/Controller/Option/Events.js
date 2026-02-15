ws.forms.controller.option.events = {
    init: function ($, $wsf_main) {
        $wsf_main.on('submit','#wsf_form_option_edit', function(e) {
            e.preventDefault();
            debugger
            var $form = $(this);
            var controller = $form.attr('data-wsf-controller'); // 'user'
            var action = $form.attr('data-wsf-action'); // 'update'

            if(!ws.forms.validation.exec($,$form)){
                return false;
            }

            var submitUrl = wsf_rest.api_url + '/' + controller + '/' + action;

            ws.forms.functions.toggleSubmitButtonLoader($form.find('[type="submit"]'),true);

            var payload = {
                wsf_option: {
                    backend_role : $form.find('#wsf_option_user_backend_role').val(),
                    frontend_role : $form.find('#wsf_option_user_frontend_role').val(),
                }
            };
            debugger
            $.ajax({
                url: submitUrl,
                method: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wsf_rest.nonce);
                },
                // Zwingt die Übertragung als sauberes JSON
                contentType: 'application/json; charset=utf-8',
                data: JSON.stringify(payload)
            })
                .done(function(response) {
                    window.location.href = response.redirect;
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    debugger
                    var message = 'Ein Fehler ist aufgetreten.';
                    if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                        message = jqXHR.responseJSON.message;
                    }
                    alert('Fehler: ' + message);
                    ws.forms.functions.toggleSubmitButtonLoader($form.find('[type="submit"]'), true)
                });

        });
}}