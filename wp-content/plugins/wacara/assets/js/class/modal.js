'use strict';

/**
 * Modal class.
 */
export default class Modal {
    modalId;
    useFooter;
    modalElement;
    toggleClass = 'wcr-modal-visible';

    /**
     * Modal class constructor.
     *
     * @param modalId
     * @param useFooter
     */
    constructor(modalId, useFooter) {
        this.modalId = modalId;
        this.useFooter = useFooter;
        this.modalSelector = '#wcr-modal-' + this.modalId;
        this.modalElement = jQuery(this.modalSelector);
        this.eventClickClose();
    }

    /**
     * Method to open modal.
     */
    show() {
        this.modalElement.addClass(this.toggleClass);
    }

    /**
     * Method to close modal.
     */
    hide() {
        this.modalElement.removeClass(this.toggleClass)
    }

    /**
     * Method to add hidden input.
     *
     * @param variables
     */
    addData(variables) {
        const instance = this;
        let modalBody = instance.modalElement.find('.wcr-modal-body');
        jQuery.each(variables, function (v_key, v_value) {
            modalBody.append('<input class="wcr-modal-field" type="hidden" name="' + v_key + '" value="' + v_value + '"/>');
        })
    }

    confirm() {

    }

    /**
     * Event when close button being clicked.
     */
    eventClickClose() {
        const instance = this;
        jQuery('body').on('click', this.modalSelector + '  span.wcr-modal-close', function () {
            instance.hide();
        })
    }
}