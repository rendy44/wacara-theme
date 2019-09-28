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
        this.render_sponsors_event();
    }

    render_sponsors_event() {
        $(document).ready(function () {
            const sponsors = $('#sponsors');
            if (sponsors.length) {
                $('.sponsor-logos').slick({
                    slidesToShow: 6,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 2000,
                    arrows: true,
                    dots: false,
                    pauseOnHover: true,
                    responsive: [
                        {
                            breakpoint: 1200,
                            settings: {
                                slidesToShow: 5
                            }
                        },
                        {
                            breakpoint: 992,
                            settings: {
                                slidesToShow: 4
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 3
                            }
                        },
                        {
                            breakpoint: 576,
                            settings: {
                                slidesToShow: 1
                            }
                        }]
                });
            }
        })
    }

    /**
     * Event when register button being clicked.
     */
    register_event() {
        const instance = this;
        $('.btn-do-register').click(function (e) {
            e.preventDefault();
            const submit_button = $(this),
                event_id = $(this).data('event'),
                pricing_id = $(this).data('pricing'),
                original_caption = $(this).html();
            // Disable the button.
            submit_button.prop('disabled', true).html('Loading...');
            // Perform the registration.
            instance.do_register(event_id, pricing_id)
                .done(function (data) {
                    instance.normalize_error(data, submit_button, original_caption);
                })
                .fail(function (data) {
                    // TODO: Validate error ajax.
                });
        })
    }

    /**
     * Render and instance stripe credit card box
     */
    render_stripe() {
        this.card_elm = $('#card');
        if (this.card_elm.length) {
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
                // Define variables.
                const submit_button = $(form).find('.btn-submit-reg'),
                    btn_original_text = submit_button.html(),
                    inputs = $(form).serializeArray();
                const user_info = {
                    name: $(form).find('input[name=name]').val(),
                    email: $(form).find('input[name=email]').val(),
                };

                // Disable button.
                submit_button.html('Loading...').prop('disabled', true);

                if (instance.card_elm.length) {
                    //Create stripe source
                    instance.stripeObj.create_source(user_info).then(function (result) {
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
                            instance.do_payment(inputs)
                                .done(function (data) {
                                    instance.normalize_error(data, submit_button, btn_original_text);
                                })
                                .fail(function (x) {
                                    // TODO: Validate error ajax.
                                })
                        }
                    });
                } else {
                    // Perform payment.
                    instance.do_payment(inputs)
                        .done(function (data) {
                            instance.normalize_error(data, submit_button, btn_original_text);
                        })
                        .fail(function (x) {
                            // TODO: Validate error ajax.
                        })
                }
            }
        })
    }

    /**
     * Normalize the button depends on ajax status
     *
     * @param data
     * @param button_element
     * @param button_caption
     */
    normalize_error(data, button_element, button_caption) {
        if (data.success) {
            // Reload the page once the payment is success.
            location.href = data.callback;
        } else {
            button_element.prop('disabled', false).html(button_caption);

            Swal.fire({
                html: data.message,
                type: 'error',
            })
        }
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

    /**
     * Method to perform payment.
     *
     * @param inputs
     * @returns {Ajax}
     */
    do_payment(inputs) {
        return new Ajax(true, {
            action: 'payment',
            data: inputs
        });
    }
};
