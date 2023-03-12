/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


checkoutPaymentParser.a4ppaypalpro = {
  all_hooks_content: function (content) {
   
  },

  form: function (element) {
    element.find('script').remove();

    element.find('.payment-form').attr('action', 'javascript: $("form[name=a4ppaypalpro_form]").submit()');

    // After content of payment methods is being refreshed, re-attach postfinancecw's handlers
    var additional_script_tag = "<script> \
        $.getScript(tcModuleBaseUrl+'/../a4ppaypalpro/views/js/a4ppaypalpro.js');\
        </script> \
      ";
    element.append(additional_script_tag);
  },

  additionalInformation: function (element) {

  }

}

 