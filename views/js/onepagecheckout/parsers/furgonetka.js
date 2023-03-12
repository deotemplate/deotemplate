/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

deo_confirmOrderValidations['furgonetka'] = function() { 

  if ('undefined' !== typeof furgonetkaCheckMapAjax) {
    var id_delivery = parseInt($('.delivery-options-list input:checked').val());
        if ($('#furgonetka-set-point').is(':visible') && !$('#furgonetka-machine-' + id_delivery).length){
          var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
          shippingErrorMsg.text(shippingErrorMsg.text() + ' (Wybierz punkt odbioru)');
          shippingErrorMsg.show();
          scrollToElement(shippingErrorMsg); 
          return false;
        }
  }

  return true;
}


checkoutShippingParser.furgonetka = {
  init_once: function (elements) {
    
  },

  delivery_option: function (element) {
    
  },

  extra_content: function (element) {
   
  }

}
