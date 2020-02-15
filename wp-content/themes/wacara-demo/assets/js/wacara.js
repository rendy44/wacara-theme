'use strict';

(function ($) {
    new class {
        navbarElm = $('nav.wcr-event-nav');

        constructor() {
            this.eventNavbarClicked();
        }

        eventNavbarClicked() {
            const instance = this;
            $('body').on('click', '.wcr-nav-toggle-wrapper', function () {
                instance.navbarElm.toggleClass('wcr-nav-expanded');
            })
        }
    }
})(jQuery);