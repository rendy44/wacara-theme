'use strict';

import Ajax from './class/ajax.js';

class ajaxClass {
    constructor() {
        this.register_event();
    }

    register_event() {
        const instance = this;
        jQuery('.btn-do-register').click(function (e) {
            e.preventDefault();
            const event_id = jQuery(this).data('event'),
                pricing_id = jQuery(this).data('pricing'),
                original_caption = jQuery(this).html();
            // Disable the button.
            jQuery(this).prop('disabled', true).html('Loading...');
            // Perform the registration.
            instance.do_register(event_id, pricing_id)
                .done(function (data) {
                    // Validate the registration result.
                    if (data.success) {
                        location.href = data.callback;
                    } else {
                        // Normalize the button.
                        jQuery(this).prop('disabled', false).html(original_caption);
                    }
                })
                .fail(function (data) {
                });
        })
    }

    do_register(event_id, pricing_id) {
        return new Ajax(true, {
            action: 'register',
            event_id: event_id,
            pricing_id: pricing_id,
        });
    }
}

// Instance the class.
new ajaxClass();
