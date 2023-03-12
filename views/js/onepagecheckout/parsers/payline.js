/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutPaymentParser.payline = {
  all_hooks_content: function (content) {
   
  },

  form: function (element) {
    var form = element.find('form');
    var originalAction = form.attr('action');

    if (originalAction.startsWith('javascript')) {
      originalAction = originalAction.substring('javascript:'.length);

      var onSubmitAction = '$.getScript( \'https://payment.payline.com/scripts/widget-min.js\', function( data, textStatus, jqxhr ) { setTimeout(function(){ ' + originalAction + '  }, 1500);});';
      form.attr('action', 'javascript:void(0);');
      form.attr('onsubmit', onSubmitAction);
    }
  },

  additionalInformation: function (element) {

    // In additionalInformation, paylina loads remote script (widget-min.js). In OPC context, right after updating payment mehtods
    // block, form confirmation and Payline.Api.init() is called, but sometimes it failes due to script reload; so let's
    // avoid reloading and if script is loaded, just use it.
    element.find('script').remove();

    var additional_script_tag = "<script> \
        $('#PaylineWidget').closest('.js-additional-information').css('position', 'initial');\
        </script> \
      ";
    element.append(additional_script_tag);
  }

}


