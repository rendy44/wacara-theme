'use strict';

import Ajax from "../../../wacara/assets/js/class/ajax.js";

(function ($) {

    /**
     * Instance a new class.
     */
    new class {

        /**
         * Class constructor.
         */
        constructor() {
            this.detailRegistrantsEvent();
            this.verifyRegistrantEvent();
        }

        /**
         * Event when button detail registrant being clicked for verify payment
         */
        detailRegistrantsEvent() {
            $('body').on('click', '.registrant_action', function (e) {
                e.preventDefault();
                const the_id = $(this).attr('data-id');

                // Load the thickbox.
                tb_show('', 'admin-ajax.php?action=wcr_payment_status&id=' + the_id + '&width=380&height=200');
            })
        }

        /**
         * Event when either reject or verify button being clicked,
         */
        verifyRegistrantEvent() {
            const instance = this;
            $('body').on('click', '.btn-do-payment-action', function (e) {
                e.preventDefault();
                const the_id = $(this).attr('data-id'),
                    new_status = $(this).hasClass('done') ? 'done' : 'fail',
                    original_text = $(this).html(),
                    grand_parent = $(this).closest('#TB_ajaxContent');
                $(this).prop('disabled', true).html('Loading...');

                instance.doVerifyRegistrant(the_id, new_status)
                    .done(function (data) {
                        if (data.success) {
                            grand_parent.html('<div style="text-align: center;display: flex;height: 100%;justify-content: center;align-items: center;">' + data.message + '</div>');
                        } else {
                            $(this).prop('disabled', false).html(original_text);
                            alert(data.message);
                        }
                    })
            })
        }

        /**
         * Method for either verify or reject payment.
         *
         * @param registrant_id
         * @param new_status
         * @returns {Ajax}
         */
        doVerifyRegistrant(registrant_id, new_status) {
            return new Ajax('verify_payment',true, {
                id: registrant_id,
                status: new_status
            });
        }
    }
})(jQuery);