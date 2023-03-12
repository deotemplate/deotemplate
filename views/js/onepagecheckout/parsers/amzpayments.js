/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


checkoutPaymentParser.amzpayments = {

  container: function (element) {
    var removeSubmitBtn = true;
    payment.setPopupPaymentType(element, removeSubmitBtn);
  },

  additionalInformation: function (element, triggerElementName) {

  }

} 