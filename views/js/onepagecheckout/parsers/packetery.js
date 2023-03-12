/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

deo_confirmOrderValidations['packetery'] = function() { 
  if (
    $('#packetery-widget').is(':visible') &&
    "" == $('#packeta-branch-id').val()
    ) {
    var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
    shippingErrorMsg.show();
    scrollToElement(shippingErrorMsg);
    return false; 
  } else {
    return true;
  }
}

checkoutShippingParser.packetery = {
  init_once: function (elements) {
    
  },

  delivery_option: function (element) {
    
  },

  extra_content: function (element) {
    element.after("<script>\
      var country = 'cz,sk'; /* Default countries */\
      $(document).ready(function(){\
        if ('undefined' !== typeof initializePacketaWidget &&  $(\".zas-box\").length)\
           initializePacketaWidget();\
        if ('undefined' !== typeof tools){\
          tools.fixextracontent(country);\
          if ('undefined' !== typeof tools && 'undefined' !== typeof tools.readAjaxFields) {\
              tools.readAjaxFields();\
          }\
          var packeteryEl = $('.carrier-extra-content.packetery');\
          if (packeteryEl.length) {\
            var extra = packeteryEl.parent();\
            if ('undefined' !== typeof packetery && 'undefined' !== typeof packetery.widgetGetCities) {\
              packetery.widgetGetCities(extra);\
            }\
          }\
        }\
      });\
      </script>");
  }

}
