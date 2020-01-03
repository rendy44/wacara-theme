'use strict';

import Ajax from './class/ajax.js';

const $ = jQuery;

/**
 * Instance the class.
 */
new class {
    /**
     * Class constructor.
     */
    constructor() {
        this.modal_result = $('#modalBeforeCheckin');
        this.find_participant_before_checkin_event();
        this.participant_checkin_event();
    }

    participant_checkin_event() {
        const instance = this;
        const btn_checkin = $('#btn_go_checkin'),
            btn_checkin_original_text = btn_checkin.html();

        btn_checkin.click(function (e) {
            e.preventDefault();

            btn_checkin.prop('disabled', true).html('Loading...');
            const participant_id = instance.modal_result.attr('data-id');

            instance.do_checkin(participant_id)
                .done(function (data) {
                    btn_checkin.prop('disabled', false).html(btn_checkin_original_text);
                    let alert_type = 'error';
                    if (data.success) {
                        instance.modal_result.modal('toggle');
                        alert_type = 'success';
                    }

                    Swal.fire({
                        html: data.message,
                        type: alert_type,
                    })
                })
                .fail(function (xyz) {

                })
        })
    };

    find_participant_before_checkin_event() {
        const instance = this;
        const btn_find = $('#btn_find_participant'),
            input_find = $('#input_find_participant'),
            btn_find_original_text = btn_find.html();

        btn_find.click(function (e) {
            e.preventDefault();

            const input_find_value = input_find.val();
            if (input_find_value) {
                btn_find.prop('disabled', true).html('Loading...');
                input_find.prop('readonly', true);

                instance.find_participant(input_find_value)
                    .done(function (data) {
                        btn_find.prop('disabled', false).html(btn_find_original_text);
                        input_find.prop('readonly', false);
                        if (data.success) {
                            // Save content into modal.
                            instance.modal_result.attr('data-id', data.callback);
                            instance.modal_result.find('.participant_booking_code').html(data.items.booking_code);
                            instance.modal_result.find('.participant_name').html(data.items.name);
                            instance.modal_result.find('.participant_email').html(data.items.email);
                            instance.modal_result.modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                        } else {
                            Swal.fire({
                                html: data.message,
                                type: 'error',
                            })
                        }
                    })
                    .fail(function (xyz) {

                    })
            }
        })
    }

    find_participant(booking_code) {
        return new Ajax(true, {
            action: 'find_by_booking_code',
            booking_code: booking_code,
        });
    }

    do_checkin(participant_id) {
        return new Ajax(true, {
            action: 'participant_checkin',
            participant_id: participant_id,
        });
    }
};
