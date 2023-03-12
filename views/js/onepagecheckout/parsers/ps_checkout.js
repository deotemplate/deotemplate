/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

window.deo_ps_checkout = {
    init: false
};

window.ps_checkout = {
    selectors: {
        CONDITIONS_CHECKBOXES: '[id="conditions_to_approve[terms-and-conditions]"]',
        BASE_PAYMENT_CONFIRMATION: '.popup-payment-button #payment-confirmation button'
    },
    events: new EventTarget(),
}

// We enable all eligible payment options
window.ps_checkout.events.addEventListener('payment-option-active', function (event) {
    var HTMLElementContainer = event.detail.HTMLElementContainer;
    var myHTMLElementContainer = HTMLElementContainer.parentElement;

    var HTMLBinaryContainer = event.detail.HTMLElementBinary
        .parentElement.parentElement;

    // We remove the disabled style for the binaries on payment options
    // The default payment tunnel does this but this is not the case on your module
    HTMLBinaryContainer.classList.remove('disabled');
    myHTMLElementContainer.style.display = '';

    // var confirmation_container = $('.popup-payment-button #payment-confirmation');
    // confirmation_container.html('');
    // //var data_mod_name = $('#' + option).attr('data-module-name');
    // $('.js-payment-binary').appendTo(confirmation_container);
});

window.ps_checkout.events.addEventListener('init', () => {
    window.deo_ps_checkout.init = true;
});

checkoutPaymentParser.ps_checkout = {

    after_load_callback: function() {
        //console.info('after load callback');
        if (window.deo_ps_checkout.init) {
            window.ps_checkout.renderCheckout();
        }
    },

    init_once: function (content, triggerElementName) {
        function ps_checkout_init() {
            // We hide all payment options because we don't know if they are eligible
            content.each(function(_, paymentOption) {
                paymentOption.style.display = 'none'
            });
        }

        ps_checkout_init();
    },

    container: function (element) {

        // Create additional information block, informing user that payment will be processed after confirmation
        var paymentOptionId = element.attr('id').match(/payment-option-\d+/);

        if (paymentOptionId && 'undefined' !== typeof paymentOptionId[0]) {
            paymentOptionId = paymentOptionId[0];
            element.after('<div id="'+paymentOptionId+'-additional-information" class="js-additional-information definition-list additional-information stripe_official ps-hidden" style="display: none;"><section><p>'+i18_popupPaymentNotice+'</p></section></div>')
        }

        payment.setPopupPaymentType(element);

        // disable this as binary method - we will keep our confirmation button and call popup display by hooking
        // to .submit event of form
        // element.find('input.binary').removeClass('binary');

    },

    all_hooks_content: function (content) {
        // empty
    },

    form: function (element) {
        // empty
    },

    additionalInformation: function (element) {
        // empty
    }

}