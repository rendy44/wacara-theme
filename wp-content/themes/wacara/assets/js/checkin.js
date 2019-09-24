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
        this.find_participant_before_checkin_event();
    }

    find_participant_before_checkin_event() {
        const instance = this;
        const btn_find = $('#btn_find_participant'),
            input_find = $('#input_find_participant'),
            btn_find_original_text = btn_find.html();
        btn_find.click(function () {
            const input_find_value = input_find.val();
            if (input_find_value) {
                btn_find.prop('disabled', true).html('Loading...');
                input_find.prop('readonly', true);

                instance.find_participant(input_find_value)
                    .done(function (data) {
                        btn_find.prop('disabled', false).html(btn_find_original_text);
                        input_find.prop('readonly', false);
                        if (data.success) {

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
};
