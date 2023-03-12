/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutPaymentParser.paynlpaymentmethods = {

  container: function(element) {
    var paymentOption = element.attr('id').match(/payment-option-\d+/)[0];
    var feeHtml = element.find('span.h6').html();
    var fee = payment.parsePrice(feeHtml);
    element.last().append('<div class="payment-option-fee hidden" id="'+paymentOption+'-fee">'+fee+'</div>'); 
  },


}


