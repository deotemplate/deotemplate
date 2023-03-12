/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


deo_confirmOrderValidations['itellashipping'] = function() { 
  if (
    $('.delivery-option.itellashipping input[name^=delivery_option]').is(':checked') &&
    !$('#itella_pickup_point_id').val()
    ) {
    var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
    shippingErrorMsg.show();
    scrollToElement(shippingErrorMsg);
    return false; 
  } else {
    return true;
  }
} 

checkoutShippingParser.itellashipping = {
  extra_content: function (element) {
    element.after("<script>\
      $(document).ready(function(){\
        if ('undefined' !== typeof ItellaModule && 'undefined' !== typeof ItellaModule.init) {\
          typeof ItellaModule.init();\
        }\
      });\
      </script>");
  }, 

  init_once: function (elements) {
    

  }

}
