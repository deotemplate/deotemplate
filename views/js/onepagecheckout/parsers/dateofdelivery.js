/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


checkoutShippingParser.dateofdelivery= {

  refreshDoD: function() {
	if ('undefined' !== typeof refreshDateOfDelivery) { 
		refreshDateOfDelivery(); 
	}
  },

  init_once: function (elements) {
  	$(document).ready(function() {
  		setTimeout(checkoutShippingParser.dateofdelivery.refreshDoD, 200);
	});
  }
}