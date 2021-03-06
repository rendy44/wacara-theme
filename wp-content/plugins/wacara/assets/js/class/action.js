'use strict';

import Ajax from "./ajax.js";

/**
 * Action classes.
 */
export default class Action {

    /**
     * Method to perform registration.
     *
     * @param event_id
     * @param pricing_id
     * @returns {Ajax}
     */
    static doRegister(event_id, pricing_id) {
        return new Ajax('select_price', true, {
            event_id: event_id,
            pricing_id: pricing_id,
        });
    }

    /**
     * Method to perform payment.
     *
     * @param inputs
     * @returns {Ajax}
     */
    static doFillDetail(inputs) {
        return new Ajax('fill_detail', true, inputs);
    }

    /**
     * Perform checkout action.
     *
     * @param inputs
     * @return {Ajax}
     */
    static doCheckout(inputs) {
        return new Ajax('checkout', true, inputs);
    }

    /**
     * Find registrant by booking code.
     *
     * @param booking_code
     * @return {Ajax}
     */
    static doFindRegistrant(booking_code) {
        return new Ajax('find_registrant', true, {
            booking_code: booking_code,
        });
    }

    /**
     * Perform registrant checkin
     *
     * @param public_key
     * @return {Ajax}
     */
    static doCheckin(public_key) {
        return new Ajax('checkin', true, {
            public_key: public_key,
        });
    }
}