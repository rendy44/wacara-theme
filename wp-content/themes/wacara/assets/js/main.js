'use strict';

import Ajax from './class/ajax.js';

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

    render_stripe() {
        const card_elm = $('#card');
        console.log(obj);
        if (card_elm.length) {
            // Create a Stripe client.
            const stripe = Stripe(obj.publishable_key);
            // Create an instance of Elements.
            const elements = stripe.elements();
            // Custom styling can be passed to options when creating an Element.
            // (Note that this demo uses a wider set of styles than the guide below.)
            const style = {
                base: {
                    color: '#555',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };
            // Create an instance of the card Element.
            const card = elements.create('card', {style: style});
            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#card');
        }
    }

    /**
     * Method to perform registration.
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
