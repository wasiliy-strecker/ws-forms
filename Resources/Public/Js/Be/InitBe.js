ws = {};
ws.forms = {
    controller: {
        user: {},
        option: {},
        product: {},
        order: {},
    },
    events: {},
    validation: {},
};

jQuery(function($) {
    var $wsf_main = $('#wsf_main');
    ws.forms.events.init($,$wsf_main);
    debugger
    if($wsf_main.hasClass('wsf_user_new')) {
        ws.forms.controller.user.new.init($,$wsf_main);// just example for now
        ws.forms.controller.user.events.init($,$wsf_main);
    }
    if($wsf_main.hasClass('wsf_user_edit')) {
        ws.forms.controller.user.edit.init($,$wsf_main);// just example for now
        ws.forms.controller.user.events.init($,$wsf_main);
    }
    if($wsf_main.hasClass('wsf_user_list')) {
        ws.forms.controller.user.list.init($,$wsf_main);
    }

    if($wsf_main.hasClass('wsf_option_edit')) {
        ws.forms.controller.option.edit.init($,$wsf_main);// just example for now
        ws.forms.controller.option.events.init($,$wsf_main);
    }

    if($wsf_main.hasClass('wsf_option_edit')) {
        ws.forms.controller.option.edit.init($,$wsf_main);// just example for now
        ws.forms.controller.option.events.init($,$wsf_main);
    }

    if($wsf_main.hasClass('wsf_form_product_new')) {
        ws.forms.controller.product.events.init($,$wsf_main);
    }

    if($wsf_main.hasClass('wsf_product_list_frontend')) {
        ws.forms.controller.order.events.init($,$wsf_main);
    }

});