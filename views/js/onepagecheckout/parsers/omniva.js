/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutShippingParser.omniva = {
  init_once: function (elements) {
    var additional_script_tag = "<script> \
        if ('function' === typeof initOmniva) {\
            initOmniva(); \
            if (!$('[name=omniva_terminal]').val() && $('[name=omniva_city]').length) { $('[name=omniva_city]').val(''); } \
            $('[name=omniva_city]').on('change', function() { omnivaSelectedCity = $('[name=omniva_city]').val(); }); \
            $('[name=omniva_terminal]').on('change', function() { omnivaSelectedTerminalId = $('[name=omniva_terminal]').val(); }); \
        }\
        </script> \
      ";
    elements.last().append(additional_script_tag);

  }
}