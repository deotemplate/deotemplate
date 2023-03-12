/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

$('#x-checkout-edit').on('click', function() {
   location.href = $(this).attr('data-href');
});

var contentRowContainer = $('#checkout-payment-step').closest('.row');

if (contentRowContainer.length) {
   contentRowContainer.before($('#separate-payment-order-review'));
}

if ("undefined" !== typeof amazon_ongoing_session && amazon_ongoing_session) {

   $('.payment-options').addClass('amazon_ongoing_session');

   var formEl = $('form[action*="/amazonpay/"]').parent('.js-payment-option-form');
   var additionalEl = formEl.prev('.additional-information');
   var titleEl = additionalEl.prev('div');

   titleEl.find('input[name=payment-option]').prop('checked', true);

   formEl.addClass('amazon-visible');
   additionalEl.addClass('amazon-visible');
   titleEl.addClass('amazon-visible');
}