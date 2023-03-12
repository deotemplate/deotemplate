/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


checkoutShippingParser.dpdfrance = {
  init_once: function (elements) {

  },

  delivery_option: function (element) {
    element.append("<script>$(document).ready(setTimeout(function(){ if ('function' === typeof dpdfrance_display) { $(\"input[name*='delivery_option[']\").change(function() { dpdfrance_display(); }); dpdfrance_display();} },200));</script>");
  },

  extra_content: function (element) {
  }

}