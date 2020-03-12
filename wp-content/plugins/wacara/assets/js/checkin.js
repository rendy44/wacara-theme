'use strict';

import Action from "./class/action.js";
import Modal from "./class/modal.js";
import Swal from "../libs/sweetalert2/src/sweetalert2.js"

(function ($) {
    /**
     * Instance the class.
     */
    new class {
        modalCheckin = new Modal('checkin', true);

        /**
         * Class constructor.
         */
        constructor() {
            // this.find_registrant_before_checkin_event();
            this.eventCheckinFormSubmitted()
        }

        /**
         * Event when checkin form submitted.
         */
        eventCheckinFormSubmitted() {
            const instance = this;
            $('#wcr-form-self-checkin').validate({
                focusInvalid: true,
                submitHandler: function (form, e) {
                    e.preventDefault();
                    let bookingCodeElm = $(form).find('input[name=booking_code]');

                    // Validate the booking code.
                    if (bookingCodeElm.val()) {

                        // Trigger finding registrant.
                        Action.doFindRegistrant(bookingCodeElm.val())
                            .done(function (data) {
                                if (data.success) {
                                    instance.modalCheckin.show();

                                    // Start check-in.
                                    instance.modalCheckin.confirm(function () {

                                        // Process the checkin.
                                        Action.doCheckin(data.callback)
                                            .done(function (checkinData) {
                                                let alert_type = checkinData.success ? 'success' : 'error';
                                                Swal.fire({
                                                    text: checkinData.message,
                                                    icon: alert_type
                                                });

                                                instance.modalCheckin.normalize()
                                            })
                                    })
                                } else {
                                    Swal.fire({
                                        text: data.message,
                                        icon: 'error',
                                    })
                                }
                            })
                            .fail(function (xyz) {

                            })
                    }
                }
            });
        };
    };
})(jQuery);
