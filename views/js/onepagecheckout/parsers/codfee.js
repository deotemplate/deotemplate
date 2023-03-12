/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


checkoutPaymentParser.codfee = {
    all_hooks_content: function (content) {

    },

    form: function (element) {
        var paymentOption = element.attr('id').match(/payment-option-\d+/)[0];
        var fee = element.find('[name^=codfee_fee]').val();
        if ('undefined' !== typeof fee) {
            fee = fee.replace(/[^0-9.,-]+/g, "").replace(/\.*$/,""); // 2nd replacement due to Arabic currency (containing dots)
        }
        element.last().append('<div class="payment-option-fee hidden" id="' + paymentOption + '-fee">' + fee + '</div>');
    }
}


