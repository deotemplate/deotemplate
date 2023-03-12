/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

deo_confirmOrderValidations['prestatilldrive'] = function() {
  
  if (_checkStoresCarrier() > 0) 
  {
    console.log('check carrier');
    if(parseInt($('#table_box').attr('data-creneau')) == 0){
      var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
      shippingErrorMsg.text(shippingErrorMsg.text() + ' (Click & Collect)');
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

checkoutShippingParser.prestatilldrive = {
  init_once: function (elements) {
   
  },

  delivery_option: function (element) {
    
  },

  extra_content: function (element) {
  }

}

function _checkStoresCarrier() {
  var count_stores = 0;
    if($('input[data-id_store]').length > 0)
    {
        $('input[data-id_store]').each(function(){
            if($('.delivery-option input[type=radio]:checked').val() == $(this).val() + "," 
            || $('.delivery-option input[type=radio]:checked').val() == $(this).val())
            {
                count_stores++;
            }
        });
    }
    
    // 2.0.0
    if($('#store_selector_modal .store_list li.active').length == 1)
    {
        count_stores++;
    }
    
    return count_stores;
}