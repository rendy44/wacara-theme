'use strict';
import Ajax from "./class/ajax.js";

(function ($) {
    /**
     * The main class.
     */
    new class {
        countdownWrappers = $('.wcr-event-counter-wrapper');

        /**
         * Class constructor.
         */
        constructor() {
            this.eventLoadCountdown();
            this.eventRegister();
            this.eventCheckout();
        }

        /**
         * This function will be triggered once the countdown element is available.
         */
        eventLoadCountdown() {
            $.each(this.countdownWrappers, function () {
                let dataTarget = $(this).data('target').toString(),
                    dayCounter = $(this).find('.wcr-event-counter-day .wcr-event-count-value'),
                    hourCounter = $(this).find('.wcr-event-counter-hour .wcr-event-count-value'),
                    minuteCounter = $(this).find('.wcr-event-counter-minute .wcr-event-count-value'),
                    secondCounter = $(this).find('.wcr-event-counter-second .wcr-event-count-value');
                if (!dataTarget) {
                    return;
                }
                // Define the date target.
                let targetObj = new Date(dataTarget).getTime();
                // Update the timer every one second.
                let wcrTimer = setInterval(function () {
                    // Get today's date and time
                    let todayOnj = new Date().getTime();
                    // Find the distance between now and the count down date
                    let distance = targetObj - todayOnj;

                    // Time calculations for days, hours, minutes and seconds
                    let daysLeft = Math.floor(distance / (1000 * 60 * 60 * 24));
                    let hoursLeft = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutesLeft = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let secondsLeft = Math.floor((distance % (1000 * 60)) / 1000);

                    // If the count down is finished, write some text
                    if (distance < 0) {
                        clearInterval(wcrTimer);

                        daysLeft = 0;
                        hoursLeft = 0;
                        minutesLeft = 0;
                        secondsLeft = 0;
                    }

                    // Display the result in the element with id="demo"
                    dayCounter.text(daysLeft);
                    hourCounter.text(hoursLeft);
                    minuteCounter.text(minutesLeft);
                    secondCounter.text(secondsLeft);
                }, 1000);
            })
        }

        /**
         * This function will be triggered once user click pricing package.
         */
        eventRegister() {
            const instance = this;
            $('.wcr-pricing-cta').click(function (e) {
                e.preventDefault();
                const submit_button = $(this),
                    event_id = $(this).data('event'),
                    pricing_id = $(this).data('pricing'),
                    original_caption = $(this).html();
                // Disable the button.
                submit_button.prop('disabled', true).html('Loading...');
                // Perform the registration.
                instance.doRegister(event_id, pricing_id)
                    .done(function (data) {
                        instance.doNormalizeError(data, submit_button, original_caption);
                    })
                    .fail(function (data) {
                        // TODO: Validate error ajax.
                    });
            })
        }

        /**
         * This function will be triggered once user checkout the registration.
         */
        eventCheckout() {
            const instance = this;
            $('.wcr-form.wcr-registrant-form').validate({
                focusInvalid: true,
                submitHandler: function (form, e) {
                    e.preventDefault();
                    // Define variables.
                    const submit_button = $(form).find('.wcr-registrant-form-submit'),
                        btn_original_text = submit_button.html(),
                        inputs = $(form).serializeArray();
                    // const user_info = {
                    //     name: $(form).find('input[name=name]').val(),
                    //     email: $(form).find('input[name=email]').val(),
                    // };

                    // Disable button.
                    submit_button.html('Loading...').prop('disabled', true);

                    instance.doCheckout(inputs)
                        .done(function (data) {
                            instance.doNormalizeError(data, submit_button, btn_original_text);
                        })
                        .fail(function (x) {
                            // TODO: Validate error ajax.
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
        doRegister(event_id, pricing_id) {
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
        doCheckout(inputs) {
            return new Ajax(true, {
                action: 'payment',
                data: inputs
            });
        }
        /**
         * Normalize the button depends on ajax status
         *
         * @param data
         * @param button_element
         * @param button_caption
         */
        doNormalizeError(data, button_element, button_caption) {
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
    };
})(jQuery);