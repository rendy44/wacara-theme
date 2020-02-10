import Ajax from '../../js/class/ajax.js';

(function ($) {
    "use strict";
    $.fn.inputFilter = function (inputFilter) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function (event) {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                if (this.oldValue.length < 2) {
                    this.value = '';
                } else {
                    this.value = this.oldValue;
                }
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            }
        });
    };

    /**
     * Instance new class
     */
    new class {
        load_registrants_page = 0;

        /**
         * Class constructor.
         */
        constructor() {
            this.inputsosaurus();
            this.inputmoney();
            this.load_registrants_event();
            this.detail_registrants_event();
            this.verify_registrant_event();
        }

        /**
         * Render inputosaurus
         */
        inputsosaurus() {
            $('.inputosaurus-field input').inputosaurus({
                width: '350px'
            });
        }

        /**
         * Event when input money changed.
         */
        inputmoney() {
            $('.number-only-field input').inputFilter(function (value) {
                return /^\d+$/.test(value);
            });
        }

        /**
         * Event when button for fetching all registrants being clicked.
         */
        load_registrants_event() {
            const instance = this;
            $('.load_all_registrants').click(function (e) {
                e.preventDefault();
                const post_id = $('#post_ID').val();
                $(this).prop('disabled', true).html('Loading...');

                instance.do_load_and_parse_registrant(post_id);
            })
        }

        /**
         * Method to load registrants.
         *
         * @param event_id
         * @returns {Ajax}
         */
        do_load_registrants(event_id) {

            // Update the load registrant pagination status.
            this.load_registrants_page += 1;

            return new Ajax('list_registrants', false, {
                id: event_id,
                page: this.load_registrants_page
            });
        }

        do_load_and_parse_registrant(event_id) {
            const instance = this,
                list_registrants_mb = $('#event_registrant_list_mb');

            instance.do_load_registrants(event_id)
                .done(function (data) {
                    const inside_mb = list_registrants_mb.find('.inside');
                    if (data.success) {

                        // Instance html output with table tag.
                        let html_output = '<div class="registrants_list"><table><thead><tr>';
                        const theads = data.callback;

                        // Fetch table header columns
                        $.each(theads, function (x, thead) {
                            html_output += '<th>' + thead + '</th>';
                        });
                        // Add column for admin action  manually.
                        html_output += '<th></th>';
                        // Add table head tag closer.
                        html_output += '</tr></thead>';

                        // Add table body tag.
                        html_output += '<tbody>';

                        // Load the items.
                        $.each(data.items, function (x, item) {
                            const registrant = item.registrant_data,
                                reg_status = registrant.reg_status;
                            html_output += '<tr class="' + reg_status + '">';
                            html_output += '<td>' + registrant.booking_code + '</td>';
                            html_output += '<td>' + registrant.name + '</td>';
                            html_output += '<td>' + registrant.email + '</td>';
                            html_output += '<td>' + registrant.company + '</td>';
                            html_output += '<td>' + registrant.position + '</td>';
                            html_output += '<td>' + registrant.phone + '</td>';
                            html_output += '<td>' + registrant.id_number + '</td>';
                            html_output += '<td>' + registrant.readable_reg_status + '</td>';
                            html_output += '<td>' + ('waiting-verification' === reg_status ? '<a href="#" class="registrant_action" data-id="' + item.post_id + '">[?]</a>' : '') + '</td>';
                            html_output += '</tr>';
                        });
                        // Add table body closer
                        html_output += '</tbody></table></div>';

                        inside_mb.html(html_output)
                    }
                })
                .fail(function (x) {

                })
        }

        /**
         * Event when button detail registrant being clicked for verify payment
         */
        detail_registrants_event() {
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
        verify_registrant_event() {
            const instance = this;
            $('body').on('click', '.btn-do-payment-action', function (e) {
                e.preventDefault();
                const the_id = $(this).attr('data-id'),
                    new_status = $(this).hasClass('done') ? 'done' : 'fail',
                    original_text = $(this).html(),
                    grand_parent = $(this).closest('#TB_ajaxContent');
                $(this).prop('disabled', true).html('Loading...');

                instance.do_verify_registrant(the_id, new_status)
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
        do_verify_registrant(registrant_id, new_status) {
            return new Ajax('verify_payment',true, {
                id: registrant_id,
                status: new_status
            });
        }
    }
})(jQuery); // End of use strict
