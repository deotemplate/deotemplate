/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

// 20.5.2019: For the moment, we won't be using any parsing, we'll just override form submit routine
// IMPORTANT NOTE: It's necessary to update modules/payplug/payplug.php, in method getEmbeddedPaymentOption,
// remove lightbox condition, and to init payment always, like this:
// if (true || (int)Tools::getValue('lightbox') == 1) {

// Since payplug v3.1.3, update instead: 
// - file: modules/payplug/payplug.php
// - original line: $id_customer = (isset($cart->id_customer)) ? $cart->id_customer : $cart['cart']->id_customer;
// - replace with: 	$id_customer = (isset($cart->id_customer)) ? $cart->id_customer : 0;

$('#deoonepagecheckout-payment').on('submit', '.payplug .payment-form', function () {

  var url = $('#payplug_form_js').data('payment-url');
  // Only for embedd mode, otherwise, let the default action
  if ('undefined' !== typeof Payplug)
  {
  	Payplug.showPayment(url);
  	return false;
  }
});

checkoutPaymentParser.payplug = {}
