'use strict';

/**
 * Helper classes
 */
export default class Helper {

    /**
     * Normalize the button depends on ajax status
     *
     * @param data
     * @param button_element
     * @param button_caption
     */
    static doNormalizeError(data, button_element, button_caption) {
        if (data.success) {
            // Reload the page once the payment is success.
            location.href = data.callback;
        } else {
            button_element.prop('disabled', false).html(button_caption);

            Swal.fire({
                html: data.message,
                type: 'error',
            })
        }
    }
}