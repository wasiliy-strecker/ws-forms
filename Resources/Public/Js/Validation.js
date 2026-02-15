ws.forms.validation = {
    exec: function($,$form) {
        debugger
        var hasError = false;
        if(this.checkRequired($,$form)){
            hasError = true;
        }
        if(this.checkEmail($,$form)){
            hasError = true;
        }
        if(hasError){
            $form.addClass('wsf_error_submitted');
        }
        return hasError ? false : true;
    },
    checkRequired: function($,$form) {
        var hasError = false;
        debugger
        $form.find('input.wsf_error_required:visible').each(function(){
            var $wsf_input_box = $(this).closest('.wsf_input_box');
            var hasErrorWithin = false;
            $wsf_input_box.find('input.wsf_error_required').each(function(){// in case multiple required in a row with same message
                if($(this).val().trim()===''){
                    hasErrorWithin = true;
                    hasError = true;
                }
            });
            if(hasErrorWithin){
                $wsf_input_box.find('.wsf_error_message.wsf_error_required').removeClass('wsf_hide');
            }else{
                $wsf_input_box.find('.wsf_error_message.wsf_error_required').addClass('wsf_hide');
            }
        });
        return hasError;
    },
    checkEmail: function($,$form) {
        var hasError = false;
        $form.find('input.wsf_error_email:visible').each(function(){
            var emailRegEx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var inputValue = $(this).val().trim();
            if (inputValue!=='' && !emailRegEx.test(inputValue)) {
                hasError = true;
                $(this).closest('.wsf_input_box').find('.wsf_error_message.wsf_error_email').removeClass('wsf_hide');
            }else{
                $(this).closest('.wsf_input_box').find('.wsf_error_message.wsf_error_email').addClass('wsf_hide');
            }
        });
        return hasError;
    }
}