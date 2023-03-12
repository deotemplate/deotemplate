/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutPaymentParser.redsys = {
  all_hooks_content: function (content) {

  },

  container: function (element) {
    var paymentOption = element.attr('id').match(/payment-option-\d+/)[0];
    var feeEl = element.find('label span.h6');
    if ('undefined' !== typeof feeEl && feeEl) {
    	var fee = parseFloat(feeEl.text().replace(/[^\d,€]/g,'').replace(',','.'))
    	element.last().append('<div class="payment-option-fee hidden" id="'+paymentOption+'-fee">'+fee+'</div>');
    }
  }
}


