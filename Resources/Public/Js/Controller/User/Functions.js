ws.forms.controller.user.functions = {
    createPayload: function ($, $form) {
        var formData = $form.serializeArray();
        var payload = {
            user: {},
            newAddress: {},
            removedAddresses: [], // Initialisierung für die gelöschten IDs
            current_url: window.location.origin+window.location.pathname // Aktuelle URL hinzufügen
        };
        $.each(formData, function() {
            if (this.name.indexOf('user[') === 0) {
                var name = this.name.replace('user[', '').replace(']', '');
                payload.user[name] = this.value.trim();
            } else if (this.name.indexOf('newAddress[') === 0) {
                var cleanName = this.name.replace('newAddress[', '').replace(/\]$/, '');
                var parts = cleanName.split('][');
                if (parts.length === 2) {
                    var index = parts[0];
                    var field = parts[1];
                    if (!payload.newAddress[index]) {
                        payload.newAddress[index] = {};
                    }
                    payload.newAddress[index][field] = this.value.trim();
                }
            } else if (this.name === 'removedAddresses[]') {
                // IDs für zu löschende Adressen sammeln
                payload.removedAddresses.push(this.value.trim());
            } else {
                if (this.name.indexOf('___INDEX___') === -1) {
                    payload[this.name] = this.value.trim();
                }
            }
        });
        return payload;
    },
  addAddressNew: function ($,$wsf_main){
        debugger
      var $wsf_address_container = $wsf_main.find('#wsf_address_container');
      var $wsf_address_row = $wsf_main.find('#wsf_address_row_new').clone().removeAttr('id').addClass('wsf_address_row_new');
      var newIndex = $wsf_address_container.find('.wsf_address_row_new').length;
      $wsf_address_row.find('input, select').each(function() {
          var name = $(this).attr('name');
          if(name) {
              $(this).attr('name', name.replace('___INDEX___','newAddress').replace(/\[(\d+)\]/g, '[' + newIndex + ']'));
          }
      });
      $wsf_main.find('#wsf_address_container').append($wsf_address_row);
      $wsf_address_row.removeClass('wsf_hide');
  },
  removeAddress: function ($,$wsf_remove_address){
      var $wsf_address_container = $wsf_remove_address.closest('#wsf_address_container');
      var $wsf_address_row = $wsf_remove_address.closest('.wsf_address_row');
      var $form = $wsf_remove_address.closest('form');

      if($wsf_address_row.hasClass('wsf_address_row_new')){
          $wsf_address_row.remove();
          $wsf_address_container.find('.wsf_address_row_new').each(function(newIndex) {
              $(this).find('input, select').each(function() {
                  var name = $(this).attr('name');
                  if(name) {
                      $(this).attr('name', name.replace(/\[(\d+)\]/g, '[' + newIndex + ']'));
                  }
              });
          });
      }else{
          $form.append('<input type="hidden" name="removedAddresses[]" value="'+$wsf_address_row.attr('data-wsf-id')+'" >');
          $wsf_address_row.remove();
      }

  },
    performSearch: function ($,$wsf_main,page,action,isFromOnpopstate){
        debugger
        // debugger; // Nur zum Testen drin lassen
        var $wsf_user_search_input = $wsf_main.find('#wsf_user_search_input');
        var $wsf_user_search_btn = $wsf_main.find('#wsf_user_search_btn');
        $wsf_user_search_btn.addClass('wsf_disabled');

        // Fallback für page
        if (!page) {
            page = 1;
        }

        // Admin Check sicher auslesen (String zu Boolean)
        var isAdmin = ($wsf_main.attr('data-wsf-is-admin') === 'true');

        if (isAdmin) {
            var $wsf_user_table_body = $wsf_main.find('#wsf_user_table_body').addClass('wsf_table_load');
        } else {
            ws.forms.controller.user.list.showLoaders($, $wsf_main);
        }

        var searchQuery = $wsf_user_search_input.val().trim();

        // --- URL LOGIK START (Verbessert) ---

        // 1. Aktuelle Parameter aus der URL holen (behält ?page=ws_forms... bei)
        var urlParams = new URLSearchParams(window.location.search);

        // 2. Search Parameter setzen oder löschen
        if (searchQuery.length > 0) {
            urlParams.set('wsf_search', searchQuery);
        } else {
            urlParams.delete('wsf_search'); // Entfernen, wenn Suche leer ist
        }

        // 3. Page Parameter setzen
        // Logik: Wir speichern 'p' nur, wenn wir nicht auf Seite 1 sind (cleaner URL),
        // ODER du willst es immer erzwingen, dann einfach urlParams.set('p', page);
        if (page > 1) {
            urlParams.set('wsf_page', page);
        } else {
            urlParams.delete('wsf_page'); // Seite 1 muss meist nicht in die URL
        }

        // 4. Neue URL bauen (Path + Query String)
        var newUrl = window.location.pathname + '?' + urlParams.toString();

        // --- URL LOGIK ENDE ---
        if(!isFromOnpopstate){
            // Push State aktualisieren
            history.pushState({
                'wsf_search': searchQuery,
                'wsf_page': page
            }, '', newUrl);
        }

        if (action == 'search' && isAdmin) {
            ws.forms.functions.toggleSubmitButtonLoader($wsf_user_search_btn, true);
        }

        $.ajax({
            url: wsf_rest.api_url + '/user/list',
            method: 'GET',
            // Hier sicherstellen, dass auch page gesendet wird, wenn sie 1 ist (fürs Backend)
            data: { 'wsf_search' : searchQuery, 'wsf_page' : page },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wsf_rest.nonce);
            }
        })
        .done(function(response) {
            if(isAdmin){
                $wsf_user_table_body.html(response.html).removeClass('wsf_table_load');
            }else{
                $wsf_main.find('#wsf_m3_list').html(response.html);
                $wsf_main.find('#wsf_pagination_wrapper').replaceWith($(response.pagination));
            }
            if(action=='search' && isAdmin){
                ws.forms.functions.toggleSubmitButtonLoader($wsf_user_search_btn, false);
            }
            $wsf_user_search_btn.removeClass('wsf_disabled');
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            var message = 'Ein Fehler ist aufgetreten.';
            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                message = jqXHR.responseJSON.message;
            }
            alert('Fehler: ' + message);
            $wsf_user_table_body.removeClass('wsf_table_load');
            if(action=='search' && isAdmin){
                ws.forms.functions.toggleSubmitButtonLoader($wsf_user_search_btn, false);
            }
            $wsf_user_search_btn.removeClass('wsf_disabled');
        });
  }
}