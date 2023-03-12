/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

deo_confirmOrderValidations['envoimoinscher'] = function() {
  if (
      /*$('#mondialrelay_widget').is(':visible')*/
      $('.delivery-option.envoimoinscher input[type=radio]').is(':checked') &&
      $('.emcListPoints').is(':visible') &&
      "undefined" !== typeof Emc && 
      !Emc.validateCarrierForm(true)
  ) {
    var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
    shippingErrorMsg.text(shippingErrorMsg.text());
    shippingErrorMsg.show();
    scrollToElement(shippingErrorMsg);
    return false;
  } else {
    return true;
  }
}

checkoutShippingParser.envoimoinscher = {
  init_once: function (elements) {
   
  },

  delivery_option: function (element) {
    
  },

  extra_content: function (element) {
  }

}
