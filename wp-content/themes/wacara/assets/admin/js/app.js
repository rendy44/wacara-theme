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

        /**
         * Class constructor.
         */
        constructor() {
            this.inputsosaurus();
            this.inputmoney();
            this.load_participants_event();
            this.detail_participants_event();
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
         * Event when button for fetching all participants being clicked.
         */
        load_participants_event() {
            const instance = this,
                list_participants_mb = $('#event_participant_list_mb');
            $('.load_all_participants').click(function (e) {
                e.preventDefault();
                const post_id = $('#post_ID').val();
                $(this).prop('disabled', true).html('Loading...');

                instance.do_load_participants(post_id)
                    .done(function (data) {
                        const inside_mb = list_participants_mb.find('.inside');
                        if (data.success) {

                            // Instance html output with table tag.
                            let html_output = '<div class="participants_list"><table><thead><tr>';
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
                                const participant = item.participant_data,
                                    reg_status = participant.reg_status;
                                html_output += '<tr class="' + reg_status + '">';
                                html_output += '<td>' + participant.booking_code + '</td>';
                                html_output += '<td>' + participant.name + '</td>';
                                html_output += '<td>' + participant.email + '</td>';
                                html_output += '<td>' + participant.company + '</td>';
                                html_output += '<td>' + participant.position + '</td>';
                                html_output += '<td>' + participant.phone + '</td>';
                                html_output += '<td>' + participant.id_number + '</td>';
                                html_output += '<td>' + participant.readable_reg_status + '</td>';
                                html_output += '<td>' + ('wait_verification' === reg_status ? '<a href="#" class="participant_action" data-id="' + item.post_id + '">[?]</a>' : '') + '</td>';
                                html_output += '</tr>';
                            });
                            // Add table body closer
                            html_output += '</tbody></table></div>';

                            inside_mb.html(html_output)
                        }
                    })
                    .fail(function (x) {

                    })
            })
        }

        /**
         * Method to load participants.
         *
         * @param event_id
         * @returns {Ajax}
         */
        do_load_participants(event_id) {
            return new Ajax(false, {
                action: 'list_participants',
                id: event_id
            });
        }

        /**
         * Event when button detail participant being clicked for verify payment
         */
        detail_participants_event() {
            const instance = this;
            $('body').on('click', '.participant_action', function (e) {
                e.preventDefault();
                const the_id = $(this).attr('data-id');

                // Load the thickbox.
                tb_show('', 'admin-ajax.php?action=check_payment_status&id=' + the_id + '&width=380&height=200')
            })
        }
    }
})(jQuery); // End of use strict
