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
            this.eventCheckinFormSubmitted();
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
                    let buttonElm = $(form).find('.wcr-login-button'),
                        buttonTxt = buttonElm.text();

                    // Validate the booking code.
                    if (bookingCodeElm.val()) {

                        // Disable the button.
                        buttonElm.prop('disabled', true).text('Loading...');

                        // Trigger finding registrant.
                        Action.doFindRegistrant(bookingCodeElm.val())
                            .done(function (data) {

                                // Normalize the button.
                                buttonElm.prop('disabled', false).text(buttonTxt);

                                // Validate the result.
                                if (data.success) {

                                    // Show modal.
                                    instance.modalCheckin.show();

                                    // Add function if modal being confirmed.
                                    instance.modalCheckin.confirm(function () {

                                        //Process the checkin.
                                        Action.doCheckin(data.callback)
                                            .done(function (checkinData) {

                                                // Normalize the modal.
                                                instance.modalCheckin.normalize(checkinData);
                                            })
                                            .fail(function (qwe) {
                                                // TODO: validate on failed ajax.
                                            })
                                    });
                                } else {
                                    Swal.fire({
                                        text: data.message,
                                        icon: 'error',
                                    })
                                }
                            })
                            .fail(function (xyz) {
                                // TODO: validate on failed ajax.
                            })
                    }
                }
            });
        };
    };
})(jQuery);
