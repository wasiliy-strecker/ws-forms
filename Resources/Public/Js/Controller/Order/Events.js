ws.forms.controller.order.events = {
    cart: [],
    paypalButtons: null,
    stripeEmail: '',
    orderData: {},
    clientId: 'xxx',
    stripeClientId: 'StripeSandboxClientId', // Platzhalter für Sandbox Client ID
    init: function ($, $wsf_main) {
        var self = this;
        this.loadCart();
        this.updateCartUI($);
        this.loadPayPalScript();
        // Add to cart
        $wsf_main.on('click', '.wsf_add_to_cart_btn', function() {
            var $btn = $(this);
            var item = {
                id: $btn.attr('data-wsf-id'),
                title: $btn.attr('data-wsf-title'),
                price: parseFloat($btn.attr('data-wsf-price')),
                sku: $btn.attr('data-wsf-sku'),
                units: 1
            };
            self.addToCart(item);
        });

        // Open cart modal
        $wsf_main.on('click', '#wsf_cart_btn', function() {
            self.openCartModal($);
        });

        // Close cart modal
        $wsf_main.on('click', '#wsf_cart_modal_close', function() {
            $('#wsf_cart_modal').removeClass('wsf_show');
        });

        // Close modal when clicking outside
        $(window).on('click', function(event) {
            if ($(event.target).is('#wsf_cart_modal')) {
                $('#wsf_cart_modal').removeClass('wsf_show');
            }
        });
    },
    addToCart: function(item) {
        var found = false;
        for (var i = 0; i < this.cart.length; i++) {
            if (this.cart[i].id === item.id) {
                this.cart[i].units += 1;
                found = true;
                break;
            }
        }
        if (!found) {
            this.cart.push(item);
        }
        this.saveCart();
        this.updateCartUI($);
        alert(item.title + ' wurde zum Warenkorb hinzugefügt.');
    },

    saveCart: function() {
        localStorage.setItem('wsf_cart', JSON.stringify(this.cart));
    },

    loadCart: function() {
        var saved = localStorage.getItem('wsf_cart');
        if (saved) {
            this.cart = JSON.parse(saved);
        }
    },

    updateCartUI: function($) {
        var count = 0;
        this.cart.forEach(function(item) {
            count += item.units;
        });
        $('#wsf_cart_count').text(count);
    },

    openCartModal: function($) {
        var self = this;
        var $list = $('#wsf_cart_items_list');
        $list.empty();
        var total = 0;

        if (this.cart.length === 0) {
            $list.append('<li class="wsf_m3_list_item">Warenkorb ist leer</li>');
            $('#wsf_stripe_container').hide();
        } else {
            $('#wsf_stripe_container').show();
            this.cart.forEach(function(item) {
                var itemTotal = item.price * item.units;
                total += itemTotal;
                $list.append(
                    '<li class="wsf_m3_list_item">' +
                    '<div class="wsf_m3_content">' +
                    '<div class="wsf_m3_headline">' + item.title + '</div>' +
                    '<div class="wsf_m3_supporting_text">' + item.units + ' x ' + item.price.toFixed(2) + ' EUR = ' + itemTotal.toFixed(2) + ' EUR</div>' +
                    '</div>' +
                    '</li>'
                );
            });
        }
        $('#wsf_cart_total_amount').text(total.toFixed(2));
        $('#wsf_cart_modal').addClass('wsf_show');
        this.renderPayPalButtons($);

        // Stripe Intent abrufen
        if (this.cart.length > 0) {
            this.getStripeIntent($);
        }
    },
    getStripeIntent: function($) {
        var self = this;

        // Formulardaten sammeln für orderData
        this.orderData = {
            firstName: $('#wsf_customer_first_name').val(),
            lastName: $('#wsf_customer_last_name').val(),
            company: $('#wsf_customer_company').val(),
            addressLine1: $('#wsf_customer_address_1').val(),
            addressLine2: $('#wsf_customer_address_2').val(),
            city: $('#wsf_customer_city').val(),
            postalCode: $('#wsf_customer_postal_code').val(),
            country: $('#wsf_customer_country').val(),
            vatNumber: $('#wsf_customer_vat_number').val(),
            cart: this.cart
        };

        $.ajax({
            url: wsf_rest.api_url + '/order/get-stripe-intent',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wsf_rest.nonce);
            },
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify({ cart: this.cart })
        })
        .done(function(response) {
            console.log('Stripe Intent created', response);
            self.stripePiId = response.stripepiid;
            self.stripePiKey = response.stripepikey;

            self.orderData.stripepiid = response.stripepiid;
            self.orderData.stripepikey = response.stripepikey;

            // In IndexedDB speichern
            ws.forms.controller.order.functions.addToStripeIndexedDb(self.stripePiId, self.orderData);

            // Stripe laden
            self.initStripe($);
        })
        .fail(function(jqXHR) {
            console.error('Stripe Intent failed', jqXHR.responseJSON);
        });
    },
    initStripe: function($) {
        var self = this;
        var stripe = Stripe(this.stripeClientId);
        var loader = 'always';
        var elements = stripe.elements({ clientSecret: self.stripePiKey, loader: loader });

        // Payment Element
        var paymentElement = elements.create('payment');
        paymentElement.mount('#wsfEcommerceStripePaymentElement');

        // Link Authentication Element (Email)
        const linkAuthenticationElement = elements.create("linkAuthentication", {
            defaultValues: {
                email: self.stripeEmail,
            }
        });
        linkAuthenticationElement.mount('#wsfEcommerceStripeAuthenticationElement');

        linkAuthenticationElement.on('change', (event) => {
            self.stripeEmail = event.value.email;
            self.orderData.StripeEmail = event.value.email;
            ws.forms.controller.order.functions.putToStripeIndexedDb(self.stripePiId, self.orderData);
        });

        var form = document.getElementById('wsfEcommerceStripePaymentForm');
        let submitted = false;

        $(form).off('submit').on('submit', async (e) => {
            e.preventDefault();

            if (submitted) { return; }
            submitted = true;
            $(form).find('button').prop('disabled', true);

            // Letzte Formulardaten sichern vor dem Confirm
            self.orderData.firstName = $('#wsf_customer_first_name').val();
            self.orderData.lastName = $('#wsf_customer_last_name').val();
            self.orderData.company = $('#wsf_customer_company').val();
            self.orderData.addressLine1 = $('#wsf_customer_address_1').val();
            self.orderData.addressLine2 = $('#wsf_customer_address_2').val();
            self.orderData.city = $('#wsf_customer_city').val();
            self.orderData.postalCode = $('#wsf_customer_postal_code').val();
            self.orderData.country = $('#wsf_customer_country').val();
            self.orderData.vatNumber = $('#wsf_customer_vat_number').val();
            await ws.forms.controller.order.functions.putToStripeIndexedDb(self.stripePiId, self.orderData);

            stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: window.location.origin + '/order',
                },
                redirect: 'if_required',
            })
            .then(function(result) {
                if (result.error) {
                    alert('Stripe Error: ' + result.error.message);
                    $(form).find('button').prop('disabled', false);
                    submitted = false;
                } else {
                    if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
                        self.processOrderBackend($, self.orderData, 'Stripe');
                    }
                }
            });
        });
    },
    loadPayPalScript: function() {
        if (window.paypal) return;
        var script = document.createElement('script');
        script.src = "https://www.paypal.com/sdk/js?client-id=" + this.clientId + "&currency=EUR";
        document.head.appendChild(script);
    },
    renderPayPalButtons: function($) {
        var self = this;
        if (this.cart.length === 0) {
            $('#paypal-button-container').empty();
            return;
        }

        if (this.paypalButtons) {
            this.paypalButtons.close();
        }

        $('#paypal-button-container').empty();

        this.paypalButtons = paypal.Buttons({
            style: {
                color: 'gold'
            },
            createOrder: (data, actions) => {
                var total = 0;
                var items = this.cart.map(function(item) {
                    total += item.price * item.units;
                    return {
                        name: item.title,
                        sku: item.sku,
                        unit_amount: {
                            currency_code: 'EUR',
                            value: item.price.toFixed(2)
                        },
                        quantity: item.units
                    };
                });

                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            currency_code: 'EUR',
                            value: total.toFixed(2),
                            breakdown: {
                                item_total: {
                                    currency_code: 'EUR',
                                    value: total.toFixed(2)
                                }
                            }
                        },
                        items: items
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(paypalOrderData) {
                    console.log('Capture result', paypalOrderData);
                    
                    // Formulardaten sammeln
                    self.orderData.firstName = $('#wsf_customer_first_name').val();
                    self.orderData.lastName = $('#wsf_customer_last_name').val();
                    self.orderData.company = $('#wsf_customer_company').val();
                    self.orderData.addressLine1 = $('#wsf_customer_address_1').val();
                    self.orderData.addressLine2 = $('#wsf_customer_address_2').val();
                    self.orderData.city = $('#wsf_customer_city').val();
                    self.orderData.postalCode = $('#wsf_customer_postal_code').val();
                    self.orderData.country = $('#wsf_customer_country').val();
                    self.orderData.vatNumber = $('#wsf_customer_vat_number').val();
                    self.orderData.paypal_order_id = paypalOrderData.id;
                    self.orderData.paypal_email = paypalOrderData.payer.email_address;
                    
                    self.processOrderBackend($, self.orderData, 'PayPal');
                });
            },
            onCancel: function (data) {
                console.log('PayPal Canceled');
            },
            onError: function(err) {
                console.error('PayPal Error', err);
            }
        });
        this.paypalButtons.render('#paypal-button-container');
    },
    processOrderBackend: function($, orderData, method) {
        var self = this;
        var payload = {
            order_data: orderData,
            payment_method: method,
            cart: this.cart
        };
        $.ajax({
            url: wsf_rest.api_url + '/order/create',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wsf_rest.nonce);
            },
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify(payload)
        })
        .done(function(response) {
            // Warenkorb leeren
            self.cart = [];
            self.saveCart();
            self.updateCartUI($);
            $('#wsf_cart_modal').removeClass('wsf_show');

            // Cleanup Stripe IndexedDB if necessary
            if (method === 'Stripe' && orderData.stripepiid) {
                ws.forms.controller.order.functions.deleteFromStripeIndexedDb(orderData.stripepiid);
            }

            // Redirect to order page
            window.location.href = window.location.origin + '/order/?order_id=' + response.order_id;
        })
        .fail(function(jqXHR) {
            var msg = 'Fehler beim Abschließen der Bestellung im Backend.';
            if (jqXHR.responseJSON && jqXHR.responseJSON.message) msg = jqXHR.responseJSON.message;
            alert(msg);
        });
    }
};
