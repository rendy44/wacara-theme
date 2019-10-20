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
                                const reg_status = item.participant_data.reg_status;
                                html_output += '<tr class="' + reg_status + '">';
                                html_output += '<td>' + item.participant_data.booking_code + '</td>';
                                html_output += '<td>' + item.participant_data.name + '</td>';
                                html_output += '<td>' + item.participant_data.email + '</td>';
                                html_output += '<td>' + item.participant_data.company + '</td>';
                                html_output += '<td>' + item.participant_data.position + '</td>';
                                html_output += '<td>' + item.participant_data.phone + '</td>';
                                html_output += '<td>' + item.participant_data.id_number + '</td>';
                                html_output += '<td>' + item.participant_data.readable_reg_status + '</td>';
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

        do_load_participants(event_id) {
            return new Ajax(false, {
                action: 'list_participants',
                id: event_id
            });
        }
    }
})(jQuery); // End of use strict
