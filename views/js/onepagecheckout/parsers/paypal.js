/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutPaymentParser.paypal = {

    isGermanPaypalPlus: function () {
       // return false; // Paypal v5.3.1 by 202 ecommerce update
        return ('undefined' !== typeof PAYPAL && 'undefined' !== typeof PAYPAL.apps && 'undefined' !== typeof PAYPAL.apps.PPP);
    },

    init_once: function (content, triggerElementName) {

        $.each(content, function (n, paymentContent) {
            if ($(paymentContent).find('.payment_module.braintree-card').length) {
                $(paymentContent).addClass('paypal-braintree-card');
                var braintreeRadio = $(paymentContent).find('.payment-option');
                payment.setPopupPaymentType(braintreeRadio);

                var formElement = $(paymentContent).find('.js-payment-option-form');

                if (!payment.isConfirmationTrigger(triggerElementName)) {
                    formElement.remove();
                } else {
                    if ('undefined' !== typeof initBraintreeCard) {
                        var additional_script_tag = '<script>\
                            $(document).ready(function(){\
                              if (\'undefined\' !== typeof initBraintreeCard) {\
                                setTimeout(initBraintreeCard, 100);\
                              }\
                            });\
                            </script>\
                        ';
                        formElement.append(additional_script_tag);
                    }
                }

            }
        });

        var express_checkout_make_visible = '<script>\
            $(document).ready(function(){\
              $(\'[data-module-name^=express_checkout_s]\').closest(\'.deo-main-payment\').show();\
              setTimeout(function() {$(\'[data-module-name^=express_checkout_s]\').prop(\'checked\', true);}, 100);\
            });\
            </script>\
        ';
        content.append(express_checkout_make_visible);

    },

    container: function (element) {
        // Fee parsing
        // var paymentOption = element.attr('id').match(/payment-option-\d+/)[0];
        // var feeHtml = element.find('label span').html();
        // var fee = payment.parsePrice(feeHtml.replace(/.*?\((.*?)\).*/,"$1"));
        // element.last().append('<div class="payment-option-fee hidden" id="'+paymentOption+'-fee">'+fee+'</div>');

    },

    all_hooks_content: function (content) {

    },

    form: function (element) {

        if (this.isGermanPaypalPlus()) {
            // First, set the 'form' action to be our background confirmation button click
            // On this background confirmation button, stripe action is hooked
            let form = element.find('form');
            let onSubmitAction = '$(\'#payment-confirmation button\').click();';
            form.attr('action', 'javascript:void(0);');
            form.attr('onsubmit', onSubmitAction);
        }
    },

    additionalInformation: function (element) {

        if (this.isGermanPaypalPlus()) {

            if (element.find('#ppplus').length && 'undefined' === typeof modePPP) {
                element.append('<a id="pppplus_reload" href="javascript: location.reload()"><span></span></a>');

            } else {
                if ('undefined' !== typeof countryIsoCodePPP && null === countryIsoCodePPP && $('[data-address-type=invoice] [name=country_iso_hidden]').length) {
                    countryIsoCodePPP = $('[data-address-type=invoice] [name=country_iso_hidden]').val();
                }

                var additional_script_tag = " \
                    <script> \
                    $.getScript(tcModuleBaseUrl + '/../paypal/views/js/payment_ppp.js') \
                    </script> \
                ";

                element.append(additional_script_tag);
            }
        }
    }

}