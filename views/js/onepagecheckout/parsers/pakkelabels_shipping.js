/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutShippingParser.pakkelabels_shipping = {
  init_once: function (elements) {

  },

  all_hooks_content: function(element) {
    element.after("<script>setTimeout(function() {jQuery('.delivery-option input:checked').click();}, 1000);</script>");
  },

  delivery_option: function (element) {
   
  },

  extra_content: function (element) {
  }

}
