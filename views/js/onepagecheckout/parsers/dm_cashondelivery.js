/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


checkoutPaymentParser.dm_cashondelivery = {
    all_hooks_content: function (content) { 

    },

	container: function(element) {
        var paymentOption = element.attr('id').match(/payment-option-\d+/)[0];
		var feeHtml = element.find('label span').html();
		var fee = payment.parsePrice(feeHtml);
		if (!isNaN(fee)) {
	  		element.last().append('<div class="payment-option-fee hidden" id="'+paymentOption+'-fee">'+fee+'</div>'); 
		}
	},

    additionalInformation: function (element) {
        var paymentOption = element.attr('id').match(/payment-option-\d+/)[0];
        var feeHtml = element.find('section p').html();
        var fee = payment.parsePrice(feeHtml);
        if (!isNaN(fee)) {
        	element.last().append('<div class="payment-option-fee hidden" id="' + paymentOption + '-fee">' + fee + '</div>');
        }
    }
}


 