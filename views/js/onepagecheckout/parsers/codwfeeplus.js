/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


checkoutPaymentParser.codwfeeplus = {
  all_hooks_content: function (content) {

  },

  additionalInformation: function (element) {
    var paymentOption = element.attr('id').match(/payment-option-\d+/)[0];
    var feeHtml = element.find('#codwfeeplus_fee').html();
    var fee = payment.parsePrice(feeHtml);
    element.last().append('<div class="payment-option-fee hidden" id="'+paymentOption+'-fee">'+fee+'</div>');
  }
}


