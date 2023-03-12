/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


checkoutPaymentParser.cashondeliverywithfee = {
  all_hooks_content: function (content) {

  },

  additionalInformation: function (element) { 
    var paymentOption = element.attr('id').match(/payment-option-\d+/)[0];
    var feeEl = element.find('#payment-fee');
    if ('undefined' !== typeof feeEl && feeEl) {
    	var fee = parseFloat(feeEl.val().replace(/[^\d,]/g,'').replace(',','.'));
    	element.last().append('<div class="payment-option-fee hidden" id="'+paymentOption+'-fee">'+fee+'</div>');
    }
  }
}


