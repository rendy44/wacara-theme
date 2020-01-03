'use strict';

/**
 * Ajax classes
 */
export default class Ajax {
    /**
     * Ajax constructor
     *
     * @param is_post
     * @param data
     * @returns {*}
     */
    constructor(is_post = true, data = []) {
        return jQuery.ajax({
            url: obj.ajax_url,
            type: is_post ? 'POST' : 'GET',
            data: data,
            dataType: 'json',
        });
    }
}
