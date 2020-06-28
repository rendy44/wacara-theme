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
        lastScanned = null;
        scanBusy = false;

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

                            Html5Qrcode.getCameras().then(devices => {
                                /**
                                 * devices would be an array of objects of type:
                                 * { id: "id", label: "label" }
                                 */
                                if (devices && devices.length) {
                                    let deviceId = '';
                                    devices.forEach(device => {
                                        deviceId = device.id;
                                        return false;
                                    });

                                    const html5QrCode = new Html5Qrcode('qr-reader');
                                    html5QrCode.start(
                                        deviceId,
                                        {
                                            fps: 10,    // Optional frame per seconds for qr code scanning
                                            // qrbox: 50  // Optional if you want bounded box UI
                                        },
                                        qrCodeMessage => {
                                            if (qrCodeMessage !== instance.lastScanned && false === instance.scanBusy) {
                                                const booking_code = qrCodeMessage.toUpperCase();

                                                // Change state.
                                                instance.lastScanned = booking_code;
                                                instance.scanBusy = true;

                                                // Loading.
                                                instance.modalScanner.startLoadingContent();

                                                Action.doFindRegistrant(booking_code)
                                                    .done(function (data) {
                                                        if (data.success) {
                                                            Action.doCheckin(data.callback)
                                                                .done(function (checkinData) {

                                                                    // Stop loading.
                                                                    instance.modalScanner.stopLoadingContent();

                                                                    // Change state.
                                                                    instance.scanBusy = false;

                                                                    // Normalize the modal.
                                                                    instance.modalCheckin.normalize(checkinData);
                                                                })
                                                                .fail(function (qwe) {
                                                                    // TODO: validate on failed ajax.
                                                                })
                                                        } else {

                                                            // Stop loading.
                                                            instance.modalScanner.stopLoadingContent();

                                                            // Change state.
                                                            instance.scanBusy = false;

                                                            // Show alert.
                                                            Swal.fire('Sorry', 'QRCode is not valid', 'error');
                                                        }
                                                    })
                                            }
                                        },
                                        errorMessage => {
                                            // alert(errorMessage);
                                            // parse error, ignore it.
                                        })
                                        .catch(err => {
                                            Swal.fire(
                                                'Sorry!',
                                                err.message,
                                                'error'
                                            );
                                        });
                                }
                            }).catch(err => {
                                Swal.fire(
                                    'Sorry!',
                                    err.message,
                                    'error'
                                );
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
