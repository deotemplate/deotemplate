/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutPaymentParser.twintcw_twint = {

  form: function (element) {

    // handlers re-attach when called this emit event
    // we just need to ensure this is called *after* markup modification
    var additional_script_tag = "<script> \
    	$(document).ready(function() { prestashop.emit('steco_event_updated')}); \
      ";
    element.append(additional_script_tag); 
  }

}


