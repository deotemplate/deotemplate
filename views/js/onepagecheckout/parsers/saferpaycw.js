/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutPaymentParser.saferpaycw_creditcard = {
  all_hooks_content: function (content) {
   
  },

  form: function (element) {
    element.find('script').remove();

    // After content of payment methods is being refreshed, re-attach saferpaycw's handlers
    var additional_script_tag = "<script> \
        $.getScript(tcModuleBaseUrl+'/../saferpaycw/js/frontend.js');\
        </script> \
      ";
    element.append(additional_script_tag);
  },

  additionalInformation: function (element) {

  }

}


