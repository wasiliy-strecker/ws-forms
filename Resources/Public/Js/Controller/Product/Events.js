ws.forms.controller.product.events = {
    init: function ($, $wsf_main) {

        // Media Library Integration
        var frame;
        $wsf_main.on('click', '#wsf_add_media_btn', function(e) {
            e.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: 'Produkt Bilder auswählen',
                button: {
                    text: 'Bilder hinzufügen'
                },
                multiple: true
            });

            frame.on('select', function() {
                var selections = frame.state().get('selection');
                var mediaIds = [];
                var $container = $('#wsf_product_media_container');
                $container.empty();

                selections.map(function(attachment) {
                    attachment = attachment.toJSON();
                    mediaIds.push(attachment.id);

                    var imgUrl = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                    $container.append(
                        '<div class="wsf_media_preview_item" style="position: relative; width: 80px; height: 80px; border: 1px solid #ddd;">' +
                        '<img src="' + imgUrl + '" style="width: 100%; height: 100%; object-fit: cover;">' +
                        '</div>'
                    );
                });

                $('#wsf_product_media_ids').val(mediaIds.join(','));
            });

            frame.open();
        });

        // AI Analyse Event
        $wsf_main.on('click', '#wsf_ai_analyze_btn', function(e) {
            e.preventDefault();
            var prompt = $('#wsf_ai_prompt').val().trim();
            if (prompt.length === 0) {
                alert('Bitte gib eine Beschreibung ein.');
                return;
            }

            var $btn = $(this);
            var $status = $('#wsf_ai_status');
            ws.forms.functions.toggleSubmitButtonLoader($btn, true);
            $status.text('AI analysiert...');

            $.ajax({
                url: wsf_rest.api_url + '/product/ai-analyze',
                method: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wsf_rest.nonce);
                },
                contentType: 'application/json; charset=utf-8',
                data: JSON.stringify({ prompt: prompt })
            })
                .done(function(response) {
                    if (response.product) {
                        var p = response.product;
                        if (p.title) $('#wsf_product_title').val(p.title).trigger('change');
                        if (p.sku) $('#wsf_product_sku').val(p.sku).trigger('change');
                        if (p.price) $('#wsf_product_price').val(p.price).trigger('change');
                        if (p.tax_rate) $('#wsf_product_tax').val(p.tax_rate).trigger('change');
                        // Status falls vorhanden
                        $status.text('Vorschlag übernommen!');
                    } else {
                        $status.text('Fehler: Ungültige Antwort von AI.');
                    }
                })
                .fail(function(jqXHR) {
                    var msg = 'Fehler bei der AI Analyse.';
                    if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                        msg = jqXHR.responseJSON.message;
                    }
                    $status.text(msg);
                    alert(msg);
                })
                .always(function() {
                    ws.forms.functions.toggleSubmitButtonLoader($btn, false);
                });
        });

        // Formular-Submit für New und Edit
        $wsf_main.on('submit', '#wsf_form_product_new,#wsf_form_product_edit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var controller = $form.attr('data-wsf-controller'); // 'product'
            var action = $form.attr('data-wsf-action'); // 'create' / 'update'
            var productId = $form.attr('data-wsf-id');

            if (!ws.forms.validation.exec($, $form)) {
                return false;
            }

            var submitUrl = wsf_rest.api_url + '/' + controller + '/' + action;
            if (productId) {
                submitUrl += '/' + productId;
            }

            ws.forms.functions.toggleSubmitButtonLoader($form.find('[type="submit"]'), true);

            var submitFunction = function() {
                var payload = ws.forms.controller.product.functions.createPayload($, $form);
                $.ajax({
                    url: submitUrl,
                    method: 'POST',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', wsf_rest.nonce);
                    },
                    contentType: 'application/json; charset=utf-8',
                    data: JSON.stringify(payload)
                })
                    .done(function(response) {
                        window.location.href = response.redirect;
                    })
                    .fail(function(jqXHR) {
                        var message = 'Ein Fehler ist aufgetreten.';
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            message = jqXHR.responseJSON.message;
                        }
                        alert('Fehler: ' + message);
                        ws.forms.functions.toggleSubmitButtonLoader($form.find('[type="submit"]'), false);
                    });
            };

            // Bei Update direkt senden, bei New erst SKU prüfen
            if (true || productId) {
                submitFunction();
            } else {
                // Check: Existiert die SKU schon?
                $.ajax({
                    url: wsf_rest.api_url + '/product/check-sku', // Du müsstest diese Action noch im Controller anlegen
                    method: 'GET',
                    data: { sku: $form.find('#wsf_product_sku').val().trim() },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', wsf_rest.nonce);
                    }
                }).done(function(checkResponse) {
                    if (checkResponse.exists) {
                        $form.find('#wsf_product_sku').focus();
                        alert('Diese SKU wird bereits verwendet.');
                        ws.forms.functions.toggleSubmitButtonLoader($form.find('[type="submit"]'), false);
                    } else {
                        submitFunction();
                    }
                }).fail(function() {
                    submitFunction(); // Fallback, falls Check-Endpoint noch nicht existiert
                });
            }
        });
    }
};