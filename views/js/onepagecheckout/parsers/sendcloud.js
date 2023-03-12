/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

deo_confirmOrderValidations['sendcloud'] = function() {
  if (
      /*$('#mondialrelay_widget').is(':visible')*/
      $('.delivery-option.sendcloud input[type=radio]').is(':checked') &&
      !$('.sendcloudshipping-point-details').is(':visible') &&
      !$('.sendcloud-spp__selection-details').is(':visible')
  ) {
    $('.err-sendcloud-point').remove();
    var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
    shippingErrorMsg.append('<span class="err-sendcloud-point"> (Sendcloud pickup point)</span>');
    shippingErrorMsg.show();
    scrollToElement(shippingErrorMsg);
    return false;
  } else {
    return true;
  }
}

var sendcloud_deo_initialized = false;

checkoutShippingParser.sendcloud = {
  init_once: function (elements) {

  },

  delivery_option: function (element) {

  },

  extra_content: function (element) {
    if (!sendcloud_deo_initialized && 'undefined' !== typeof sendcloud_script && sendcloud_script != '') {
      $.getScript(sendcloud_script);
      sendcloud_deo_initialized = true;
    }
  }

}
