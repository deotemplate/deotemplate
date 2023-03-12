/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


checkoutPaymentParser.atos = {
  all_hooks_content: function (content) {
    // Remove 'accept TOS warning'
    content.find('.js-payment-binary .alert.alert-warning.accept-cgv').remove();
    content.find('.js-payment-binary.js-payment-atos.disabled').removeClass('disabled');
  },

  form: function (element) {

  }

}
