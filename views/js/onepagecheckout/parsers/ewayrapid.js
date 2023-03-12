/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutPaymentParser.ewayrapid = {

    container: function (element) {

        // disable this as binary method - we will keep our confirmation button and call popup display by hooking
        // to .submit event of form
        element.find('input.binary').removeClass('binary');

    },

    additionalInformation: function (content) {

    //     let submitHandler = `
    // <script>
    // eCrypt.init();
    // $('#deoonepagecheckout-payment').on('submit', '[data-payment-module=ewayrapid] .js-payment-option-form form', function() {
    //     if (!ewayPaid) {
    //             eCrypt.showModalPayment(eWAYConfig, resultCallback);
    //     }
    //     return false;
    //   }
    // );
    // </script>
    // `;

        // https://babeljs.io/repl
        var submitHandler = "\n    <script>\n    eCrypt.init();\n    $('#deoonepagecheckout-payment').on('submit', '[data-payment-module=ewayrapid] .js-payment-option-form form', function() { \n        if (!ewayPaid) {\n                eCrypt.showModalPayment(eWAYConfig, resultCallback);\n        }\n        return false; \n      }\n    );\n    </script>\n    ";
        //


        var regex = /document\.addEventListener\("DOMContentLoaded", function\(event\) {\s+(.*)}\);([^}]*<\/script>.*)/g;
        var subst = "$1$2";

        htmlContent = content.html().replace(regex, subst);

        // remove link include if it's been once included already
        if ('undefined' !== typeof eCrypt) {
            regex = /<script .*?eCrypt.js.*?script>/m;
            htmlContent = htmlContent.replace(regex, '');
            htmlContent = htmlContent + submitHandler;
        }

        content.html(htmlContent);

        content.find('a#processPayment.eway-iframe').hide();
    }

}
