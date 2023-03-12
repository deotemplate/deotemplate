/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutPaymentParser.pm_subscription_popup = {


    on_ready: function() {
        if ($('[data-module-name^="pm_subscription"]').length)  {
            $('input[name=payment-option]:not([data-module-name^="pm_subscription"]').each(function( index ) {
                if ($(this).data('module-name') != 'free_order') {
                    var paymentOptionID = $(this).attr('id');
                    $('#' + paymentOptionID + '-container').parent().remove();
                    $('#' + paymentOptionID + '-additional-information').remove();
                    $('#payment-option-' + paymentOptionID + '-container').remove();
                    $('#pay-with-payment-option-' + paymentOptionID + '-form').remove();
                } else {
                    // Update action of free_order module
                    var paymentOptionID = $(this).attr('id');
                    $('#pay-with-' + paymentOptionID + '-form form').attr('action', pm_subscription.validationURL);
                }
            });
            $('input[name=payment-option][data-module-name^="pm_subscription"]').prop('checked', true);
        }
        setTimeout(function () {
            // Load only when stripe hosted fields are not initialized yet
            if (!$('#sub-stripe-card-number.card-element').length) {
                $.getScript(tcModuleBaseUrl + '/../pm_subscription/views/js/front/payments.js');
            }
        }, 300)
    },

    all_hooks_content: function (content) {

    },

    container: function(element) {

        var stripe_base_url = '';
        if ('undefined' !== typeof prestashop && 'undefined' !== prestashop.urls && 'undefined' !== prestashop.urls.base_url) {
            stripe_base_url = prestashop.urls.base_url;
        }

      //  element.find('label').append('<img src="' + stripe_base_url + '/modules/stripe_official/views/img/logo-payment.png">');

        // Create additional information block, informing user that payment will be processed after confirmation
        var paymentOptionId = element.attr('id').match(/payment-option-\d+/);

        if (paymentOptionId && 'undefined' !== typeof paymentOptionId[0]) {
            paymentOptionId = paymentOptionId[0];
            element.after('<div id="'+paymentOptionId+'-additional-information" class="js-additional-information definition-list additional-information pm_subscription ps-hidden" style="display: none;"><section><p>'+i18_popupPaymentNotice+'</p></section></div>')
        }

        payment.setPopupPaymentType(element);

        var additional_script_tag = " \
                <script> \
                $(document).ready( \
                    checkoutPaymentParser.pm_subscription.on_ready \
                ); \
                </script> \
        ";

        element.append(additional_script_tag); 
    },

    form: function (element, triggerElementName) {

        if (!payment.isConfirmationTrigger(triggerElementName)) {
            element.remove();
        } else {
           
        }

        return;
    }

}

checkoutPaymentParser.pm_subscription = checkoutPaymentParser.pm_subscription_popup;


