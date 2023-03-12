/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

deo_confirmOrderValidations['prestatillhomedelivery'] = function() {
	
	if (_checkIdEC()) 
	{
		console.log('check hd carrier');
		if(parseInt($('#hd_box').attr('data-creneau')) == 0){
			var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
			shippingErrorMsg.text(shippingErrorMsg.text() + ' (Choose a slot)');
			shippingErrorMsg.show();
			scrollToElement(shippingErrorMsg);
			return false;
		} else {
			return true;  
		}
	}
	else {
		return true;
	}
}

checkoutShippingParser.prestatillhomedelivery = {
  init_once: function (elements) {
    
  },

  delivery_option: function (element) {
    
  },

  extra_content: function (element) {
  }

}

function _checkIdEC() {
        if($('.delivery-option input[type=radio]:checked').length > 0)
        {
            var id_selected_carrier = $('.delivery-option input[type=radio]:checked').val();
            id_selected_carrier = id_selected_carrier.replace(",","");
            
            if($('body').find('#hd_id_carrier_'+id_selected_carrier).length > 0)
                return true;
                
            return false;
        }
    }