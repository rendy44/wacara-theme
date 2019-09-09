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

    new class {
        constructor() {
            this.inputsosaurus();
            this.inputmoney();
        }

        inputsosaurus() {
            $('.inputosaurus-field input').inputosaurus({
                width: '350px'
            });
        }

        inputmoney() {
            $('.number-only-field input').inputFilter(function (value) {
                return /^\d+$/.test(value);
            });
        }
    }
})(jQuery); // End of use strict
