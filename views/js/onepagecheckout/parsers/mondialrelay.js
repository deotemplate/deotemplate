/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

deo_confirmOrderValidations['mondialrelay'] = function() {
  if (
      /*$('#mondialrelay_widget').is(':visible')*/
      $('.delivery-option.mondialrelay input[type=radio]').is(':checked') &&
      !$('#mondialrelay_summary').is(':visible')
  ) {
    var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
    shippingErrorMsg.text(shippingErrorMsg.text() + ' (Mondial relay)');
    shippingErrorMsg.show();
    scrollToElement(shippingErrorMsg);
    return false;
  } else {
    return true;
  }
}

checkoutShippingParser.mondialrelay = {
  init_once: function (elements) {

  },

  delivery_option: function (element) {
    // Uncheck mondialrelay item, so that it can be manually selected
    //element.after("<script>$('.delivery-option.mondialrelay input[name^=delivery_option]').prop('checked', false)</script>");
    // Mondial v3.0+ by 202 ecommerce
    element.append("<script>$(document).ready(setTimeout(function(){$('#js-delivery').find('[name^=\"delivery_option\"]:checked').trigger('change');},500)); prestashop.emit(\"updatedDeliveryForm\",{dataForm:$('#js-delivery').serializeArray(),deliveryOption:$('#js-delivery').find('[name^=\"delivery_option\"]:checked')});</script>");
  },

  extra_content: function (element) {
  }

}
