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
                        Action.doFindRegistrant(bookingCodeElm.val())
                            .done(function (data) {
                                if (data.success) {
                                    instance.modalCheckin.show();
                                    instance.modalCheckin.addData({
                                        registrant_id: 1000,
                                        registrant_hash: 'asdsadasda'
                                    });
                                    instance.modalCheckin.confirm(function () {
                                        console.log('diconfirm');
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
