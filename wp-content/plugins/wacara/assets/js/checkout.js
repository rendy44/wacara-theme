'use strict';
import Ajax from "./class/ajax.js";
import Helper from "./class/helper.js";

(function ($) {

    /**
     * Default checkout class.
     */
    new class {
        checkoutForm = $('.wcr-form.wcr-hold-registrant-form');

        /**
         * Default checkout constructor.
         */
        constructor() {
            this.eventCheckout();
        }

        /**
         * Event when checkout form being submitted.
         */
        eventCheckout() {
            const instance = this;
            this.checkoutForm.validate({
                focusInvalid: true,
                submitHandler: function (form, e) {
                    e.preventDefault();
                    // Define variables.
                    const submit_button = $(form).find('.wcr-form-submit'),
                        btn_original_text = submit_button.html(),
                        inputs = $(form).serializeArray();

                    // Disable button.
                    submit_button.html('Loading...').prop('disabled', true);

                    instance.doCheckout(inputs)
                        .done(function (data) {
                            Helper.doNormalizeError(data, submit_button, btn_original_text);
                        })
                        .fail(function (x) {
                            // TODO: Validate error ajax.
                        });
                }
            });
        }

        /**
         * Perform checkout action.
         *
         * @param inputs
         * @return {Ajax}
         */
        doCheckout(inputs){
            return new Ajax('checkout', true, inputs);
        }
    }
})(jQuery);