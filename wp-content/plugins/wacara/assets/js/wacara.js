'use strict';
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
    };
})(jQuery);