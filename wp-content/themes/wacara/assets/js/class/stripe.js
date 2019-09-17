'use strict';

/**
 * WcStripe class
 */
export default class WcStripe {
    /**
     * WcStripe constructor.
     */
    constructor(publishable_key) {
        this.instance(publishable_key);
        this.add_event();
    }

    /**
     * Instance stripe
     *
     * @param publishable_key
     */
    instance(publishable_key) {
        // Create a Stripe client.
        this.stripe = Stripe(publishable_key);
        // Create an instance of Elements.
        const elements = this.stripe.elements();
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
        this.card = elements.create('card', {style: style});
        // Add an instance of the card Element into the `card-element` <div>.
        this.card.mount('#card');
    }

    /**
     * Add event listener to stripe obj
     */
    add_event() {
        // Handle real-time validation errors from the card Element.
        this.card.addEventListener('change', function (event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
    }

    /**
     * Create stripe source
     *
     * @param user_info
     *
     * @returns {*}
     */
    create_source(user_info) {
        return this.stripe.createSource(this.card, {
            owner: user_info
        })
    }
}
