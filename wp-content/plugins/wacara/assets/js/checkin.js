'use strict';

import Action from "./class/action.js";
import Modal from "./class/modal.js";
import Helper from "./class/helper.js";

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
            const instance = this;
            $('#btnOpenScanner').click(function (e) {
                instance.modalScanner.show();

                // see if DOM is already available
                if (document.readyState === "complete" || document.readyState === "interactive") {
                    // call on next available tick
                    setTimeout(function () {

                        if ('mediaDevices' in navigator && 'getUserMedia' in navigator.mediaDevices) {

                            navigator.mediaDevices.enumerateDevices()
                                .then(function (devices) {
                                    devices.forEach(function (device) {
                                        console.log(device.kind + ": " + device.label +
                                            " id = " + device.deviceId);
                                    });
                                })
                                .catch(function (err) {
                                    instance.modalScanner.customContent('<p>' + err.message + '</p>');
                                });

                        } else {
                            instance.modalScanner.customContent('<p>Your browser is not supported</p>');
                        }
                    }, 1);
                }
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
