/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutPaymentParser.sagepay = {
  all_hooks_content: function (content) {

  },

  additionalInformation: function (element) {
    var additional_script_tag = '<script> \
      if ($("#sgp_iframe").length) {\
        $("#sgp_iframe").css("height", 442 + sgp_card_types_count * 48 + "px");\
      }\
      </script>\
    ';

    element.append(additional_script_tag);
  }
}


