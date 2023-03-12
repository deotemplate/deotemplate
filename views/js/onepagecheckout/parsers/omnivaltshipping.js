/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

deo_confirmOrderValidations['omnivaltshipping'] = function() { 
  if (
    $('[name=omnivalt_parcel_terminal]').is(':visible') &&
    !$('[name=omnivalt_parcel_terminal]').val()
    ) {
    var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
    shippingErrorMsg.show();
    scrollToElement(shippingErrorMsg);
    return false; 
  } else {
    return true;
  }
} 

checkoutShippingParser.omnivaltshipping = {
  init_once: function (elements) {
    var additional_script_tag = "<script> \
        if ('undefined' !== typeof omnivaltDelivery) {\
          $('.delivery-options .delivery-option input[type=\"radio\"]').on('click',function(){\
            omnivaltDelivery.init();\
          });\
        }\
        </script> \
      ";
    elements.last().append(additional_script_tag);

  }
}
