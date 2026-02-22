ws.forms.controller.user.login = {
    init: function ($,$wsf_main) {
        this.events($,$wsf_main);
    },
    events: function ($,$wsf_main) {
        $wsf_main.on('submit', '#wsf_login_form', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $msg = $('#wsf_login_message');

            // Payload bauen
            var payload = {
                login: $form.find('input[name="login"]').val(),
                password: $form.find('input[name="password"]').val(),
                current_url: window.location.href // Wichtig für Redirect
            };

            $form.find('button').prop('disabled', true).text('Lade...');

            $.ajax({
                url: wsf_rest.api_url + '/user/login',
                method: 'POST',
                data: JSON.stringify(payload), // Als JSON senden
                contentType: 'application/json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wsf_rest.nonce);
                }
            })
                .done(function(response) {
                    $msg.html('<span style="color:green;">' + response.message + '</span>');
                    window.location.href = response.redirect;
                })
                .fail(function(jqXHR) {
                    var message = jqXHR.responseJSON?.message || 'Login fehlgeschlagen.';
                    $msg.html('<span style="color:red;">' + message + '</span>');
                    $form.find('button').prop('disabled', false).text('Einloggen');
                });
        });
    }
}