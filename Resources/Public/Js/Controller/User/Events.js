ws.forms.controller.user.events = {
    init: function ($, $wsf_main) {

        $wsf_main.on('click', '#wsf_add_address_btn', function() {
            ws.forms.controller.user.functions.addAddressNew($,$wsf_main);
        });
        $wsf_main.on('click', '.wsf_remove_address', function() {
            ws.forms.controller.user.functions.removeAddress($,$(this));
        });

        $wsf_main.on('submit','#wsf_form_user_new,#wsf_form_user_edit', function(e) {
            e.preventDefault();
            debugger
            var $form = $(this);
            var controller = $form.attr('data-wsf-controller'); // 'user'
            var action = $form.attr('data-wsf-action'); // 'update'
            var userId     = $form.attr('data-wsf-id');
            if(!ws.forms.validation.exec($,$form)){
                return false;
            }

            if(userId){
                var submitUrl = wsf_rest.api_url + '/' + controller + '/' + action + '/' + userId;
            }else{
                var submitUrl = wsf_rest.api_url + '/' + controller + '/' + action;
            }

            ws.forms.functions.toggleSubmitButtonLoader($form.find('[type="submit"]'),true);

            var submitFunction = function(){
                var payload = ws.forms.controller.user.functions.createPayload($,$form);
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
                        debugger
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
            }

            if(userId){
                submitFunction();
            }else{
                // 2. AJAX Check: Existiert die Email schon?
                $.ajax({
                    url: wsf_rest.api_url + '/user/check-email',
                    method: 'GET',
                    data: { email: $form.find('#wsf_email').val().trim() },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', wsf_rest.nonce);
                    }
                }).done(function(checkResponse) {
                    if (checkResponse.exists) {
                        $wsf_main.find('#wsf_email').focus();
                        $wsf_main.find('#wsf_error_email_exists').removeClass('wsf_hide');
                        ws.forms.functions.toggleSubmitButtonLoader($form.find('[type="submit"]'), false);
                    } else {
                        submitFunction();
                    }
                }).fail(function() {
                    alert('Fehler beim Email-Check.');
                    ws.forms.functions.toggleSubmitButtonLoader($form.find('[type="submit"]'), false);
                });
            }

        });
}}