'use strict';

import Ajax from "../../../wacara/assets/js/class/ajax.js";
import Helper from "../../../wacara/assets/js/class/helper.js";

(function ($) {
    new class {
        constructor() {
            this.eventConfirmation();
        }

        eventConfirmation() {
            const instance = this;
            $('.wcr-form.wcr-waiting-payment-registrant-form').validate({
                focusInvalid: true,
                submitHandler: function (form, e) {
                    e.preventDefault();
                    // Define variables.
                    const submit_button = $(form).find('.wcr-form-submit'),
                        btn_original_text = submit_button.html(),
                        inputs = $(form).serializeArray();

                    // Disable button.
                    submit_button.html('Loading...').prop('disabled', true);

                    instance.doConfirm(inputs)
                        .done(function (data) {
                            Helper.doNormalizeError(data, submit_button, btn_original_text);
                        })
                        .fail(function (x) {
                            // TODO: Validate error ajax.
                        });
                }
            })
        }

        doConfirm(input) {
            return new Ajax('confirm', true, input);
        }

    };
})(jQuery);