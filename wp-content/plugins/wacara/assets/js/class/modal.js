'use strict';

/**
 * Modal class.
 */
export default class Modal {
    modalId;
    useFooter;
    modalSelector;
    modalElement;
    confirmButtonSelector;
    confirmButtonElement;
    confirmButtonText;
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
        this.confirmButtonSelector = this.modalSelector + '  button.wcr-modal-confirm';
        this.confirmButtonElement = jQuery(this.confirmButtonSelector);
        this.confirmButtonText = this.confirmButtonElement.text();
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
     * Method when confirm button clicked
     *
     * @param callback_function
     */
    confirm(callback_function) {
        const instance = this;
        jQuery('body').on('click', this.confirmButtonSelector, function () {

            instance.confirmButtonElement.prop('disabled', true).text('Loading...');

            callback_function();
        })
    }

    /**
     * Normalize confirm button.
     */
    normalize() {
        this.confirmButtonElement.prop('disabled', false).text(this.confirmButtonText);
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