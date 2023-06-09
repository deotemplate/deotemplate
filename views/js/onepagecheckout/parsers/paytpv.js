/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutPaymentParser.paytpv = {

   
    init_once: function (content, triggerElementName) {

       
    },

    container: function (element) {

        // disable this as binary method - we will keep our confirmation button and call popup display by hooking
        // to .submit event of form
        //element.find('input.binary').removeClass('binary');
        
        // Fee parsing
        // var paymentOption = element.attr('id').match(/payment-option-\d+/)[0];
        // var feeHtml = element.find('label span').html();
        // var fee = payment.parsePrice(feeHtml.replace(/.*?\((.*?)\).*/,"$1"));
        // element.last().append('<div class="payment-option-fee hidden" id="'+paymentOption+'-fee">'+fee+'</div>');
    },

    all_hooks_content: function (content) {

    },

    form: function (element) {
          var additional_script_tag = '<script>\
                            $(document).ready(function(){\
                              if ($(\'input[id^=conditions_to_approve\').is(\':checked\')) {\
                                $(\'.payment_module.paytpv_iframe\').show();\
                              } else{\
                                $(\'.payment_module.paytpv_iframe\').hide();\
                              }\
                            });\
                            </script>\
                        ';
                        element.append(additional_script_tag); 
    },

    additionalInformation: function (element) {
          var divRow = element.find('div.row');
          divRow.addClass("js-payment-paytpv");
    }

}