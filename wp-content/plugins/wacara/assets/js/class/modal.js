'use strict';

import Helper from "./helper.js";

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
    confirmCallback;
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
        this.eventClickConfirm();
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
     * Normalize confirm button.
     *
     * @param data
     * @param isHideModalOnSuccess
     */
    normalize(data, isHideModalOnSuccess) {

        // Set default value.
        if ('undefined' === typeof isHideModalOnSuccess) {
            isHideModalOnSuccess = true;
        }

        if (isHideModalOnSuccess) {
            this.hide();
        }

        Helper.doNormalizeError(data, this.confirmButtonElement, this.confirmButtonText, false);
    }

    /**
     * Save callback
     *
     * @param callback
     */
    confirm(callback) {
        this.confirmCallback = callback;
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

    /**
     * Event when confirm button being clicked.
     */
    eventClickConfirm() {
        const instance = this;
        this.confirmButtonElement.click(function (e) {
            jQuery(this).prop('disabled', true).text('Loading...');
            instance.confirmCallback();
        })
    }
}