// be used for one type of general item
ws.forms.functions = {
    toggleSubmitButtonLoader: function($btn, show) {
        if (show) {
            if ($btn.find('.wsf_btn_spinner').length === 0) {
                $btn.addClass('wsf_btn_loading');
                $btn.wrap('<div class="wsf_btn_spinner_wrap"/>').parent().append('<span class="wsf_btn_spinner"></span>');
            }
        } else {
            $btn.removeClass('wsf_btn_loading');
            $btn.parent().find('.wsf_btn_spinner').remove();
            if ($btn.parent().hasClass('wsf_btn_spinner_wrap')) {
                $btn.unwrap();
            }
        }
    },
    isUrlEmpty: function() {
        var hasNoParameters = window.location.search === "" || window.location.search === "?";
        var hasNoHash = window.location.hash === "" || window.location.hash === "#";

        return hasNoParameters && hasNoHash;
    }
}