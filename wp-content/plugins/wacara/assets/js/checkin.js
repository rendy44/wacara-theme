'use strict';

import Action from "./class/action.js";
import Modal from "./class/modal.js";
import Helper from "./class/helper.js";
import QrScanner from '../vendor/js/qr-scanner.min.js';

(function ($) {
    /**
     * Instance the class.
     */
    new class {
        modalCheckin = new Modal('checkin', true);
        modalScanner = new Modal('qrcode-scanner', false);

        /**
         * Class constructor.
         */
        constructor() {
            this.eventCheckinFormSubmitted();
            this.eventScannerOpened();
        }

        /**
         * Event when open scanner clicked
         */
        eventScannerOpened() {
            QrScanner.WORKER_PATH = '../vendor/js/qr-scanner-worker.min.js';
            const instance = this,
                videoElm = document.getElementById('scannerVid');
            $('#btnOpenScanner').click(function (e) {
                instance.modalScanner.show();

                const scanner = new QrScanner(videoElm, result => console.log(result));
                scanner.start();
            })
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

                                Helper.doNormalizeError(data, buttonElm, buttonTxt, false, false)
                                    .then(function (result) {
                                        if (result) {

                                            // Display result in modal.
                                            instance.modalCheckin.modalBodyElement.find('p.wcr-registrant-name').text(data.items[0]);
                                            instance.modalCheckin.modalBodyElement.find('p.wcr-registrant-email').text(data.items[1]);

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
                                        }
                                    });
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
