/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutPaymentParser.shaim_cashondelivery = {
    all_hooks_content: function (content) {

    },

    additionalInformation: function (element) {
        var paymentOption = element.attr('id').match(/payment-option-\d+/)[0];
        var feeEl = element.find('#shaim_cashondelivery_fee_clean');
        var fee = 0;
        if (feeEl.length) {
        	fee = feeEl.val();
        }
        element.last().append('<div class="payment-option-fee hidden" id="' + paymentOption + '-fee">' + fee + '</div>');
    }
}




