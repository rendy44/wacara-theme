'use strict';

import WcStripe from "./class/stripe.js";
import Helper from "../../wacara/assets/js/class/helper.js";
import Action from "../../wacara/assets/js/class/action.js";

(function ($) {
    new class {
        stripeElement = $('#card');
        stripeObj = null;

        constructor() {
            this.renderStripeEvent();
            this.stripeCheckoutEevent();
        }

        renderStripeEvent() {
            if (this.stripeElement.length) {
                this.stripeObj = new WcStripe(obj.stripe_key);
            }
        }

        /**
         * Event when payment button clicked.
         */
        stripeCheckoutEevent() {
            const instance = this;
            $('.wcr-form.wcr-hold-registrant-form.wcr-custom-checkout-stripe-payment').validate({
                focusInvalid: true,
                submitHandler: function (form, e) {
                    e.preventDefault();
                    // Define variables.
                    const submit_button = $(form).find('.wcr-form-submit'),
                        btn_original_text = submit_button.html(),
                        inputs = $(form).serializeArray();
                    const user_info = {
                        name: $(form).find('input[name=name]:hidden').val(),
                        email: $(form).find('input[name=email]:hidden').val(),
                    };

                    // Disable button.
                    submit_button.html('Loading...').prop('disabled', true);

                    // Perform payment with stripe
                    instance.stripeObj.create_source(user_info)
                        .then(function (result) {
                            if (result.error) {
                                // Normalize the button.
                                submit_button.prop('disabled', false).html(btn_original_text);
                                // Show alert.
                                Swal.fire({
                                    html: result.error.message,
                                    type: 'error',
                                })
                            } else {
                                // Append source id into input.
                                inputs.push({
                                    name: 'stripe_source_id',
                                    value: result.source.id
                                });

                                // Perform payment.
                                Action.doCheckout(inputs)
                                    .done(function (data) {
                                        Helper.doNormalizeError(data, submit_button, btn_original_text);
                                    })
                                    .fail(function (x) {
                                        // TODO: Validate error ajax.
                                    })
                            }
                        });
                }
            })
        }
    };
})(jQuery);