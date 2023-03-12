/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

deo_confirmOrderValidations['zasilkovna'] = function() { 
  if (
    $('#packetery-widget select[name=name]').is(':visible') && 
    !$('#packetery-widget select[name=name]').val()
    ) {
    var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
    shippingErrorMsg.show();
    scrollToElement(shippingErrorMsg);
    return false; 
  } else {
    return true;
  }
}

checkoutShippingParser.zasilkovna = {
  init_once: function (elements) {
    
  },

  delivery_option: function (element) {
    
  },

  extra_content: function (element) {
    element.after("<script>\
      $(document).ready(function(){\
        if ('undefined' !== typeof initializePacketaWidget &&  $(\".zas-box\").length)\
           initializePacketaWidget();\
        if ('undefined' !== typeof tools){\
          tools.fixextracontent();\
          tools.readAjaxFields();\
          var zasilkovnaEl = $('.carrier-extra-content.zasilkovna');\
          if (zasilkovnaEl.length) {\
            var extra = zasilkovnaEl.parent();\
            packetery.widgetGetCities(extra);\
          }\
        }\
      });\
      </script>");
  }

}
