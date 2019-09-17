'use strict';

import Ajax from './class/ajax.js';
import WcStripe from "./class/stripe.js";

const $ = jQuery;

/**
 * Instance the class.
 */
new class {
    /**
     * Class constructor.
     */
    constructor() {
        this.register_event();
        this.render_stripe();
        this.payment_event();
    }

    /**
     * Event when register button being clicked.
     */
    register_event() {
        const instance = this;
        $('.btn-do-register').click(function (e) {
            e.preventDefault();
            const event_id = $(this).data('event'),
                pricing_id = $(this).data('pricing'),
                original_caption = $(this).html();
            // Disable the button.
            $(this).prop('disabled', true).html('Loading...');
            // Perform the registration.
            instance.do_register(event_id, pricing_id)
                .done(function (data) {
                    // Validate the registration result.
                    if (data.success) {
                        location.href = data.callback;
                    } else {
                        // Normalize the button.
                        $(this).prop('disabled', false).html(original_caption);
                    }
                })
                .fail(function (data) {
                });
        })
    }

    /**
     * Render and instance stripe credit card box
     */
    render_stripe() {
        const card_elm = $('#card');
        if (card_elm.length) {
            this.stripeObj = new WcStripe(obj.publishable_key);
        }
    }

    /**
     * Event when payment button clicked.
     */
    payment_event() {
        const instance = this;
        $('#frm_register').validate({
            focusInvalid: true,
            submitHandler: function (form, e) {
                e.preventDefault();
                const user_info = {
                    name: $(form).find('input[name=name]').val(),
                    email: $(form).find('input[name=email]').val(),
                };

                //Create stripe source
                instance.stripeObj.create_source(user_info).then(function (result) {
                    if (result.error) {
                        console.error(result.error.message);
                        // Swal.fire({
                        //     title: 'Error',
                        //     html: result.error.message,
                        //     type: 'error',
                        //     confirmButtonText: 'OK'
                        // })
                    } else {
                        console.log(result.source);
                        // Send the source to your server
                        // stripeSourceHandler(result.source, formdata);
                    }
                });
            }
        })
    }

    /**
     * Method to perform registration.
     *
     * @param event_id
     * @param pricing_id
     * @returns {Ajax}
     */
    do_register(event_id, pricing_id) {
        return new Ajax(true, {
            action: 'register',
            event_id: event_id,
            pricing_id: pricing_id,
        });
    }
};
