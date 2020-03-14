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
     * @param isRedirect
     */
    static doNormalizeError(data, button_element, button_caption, isRedirect) {

        // Set default value.
        if ('undefined' === typeof isRedirect) {
            isRedirect = true;
        }

        // Default alert type.
        let alertType = data.success ? 'success' : 'error';

        // Maybe normalize button.
        button_element.prop('disabled', false).text(button_caption);

        // Maybe redirect.
        if (true === isRedirect && data.success) {
            location.href = data.callback;
        } else {
            Swal.fire({
                html: data.message,
                type: alertType,
            })
        }
    }
}