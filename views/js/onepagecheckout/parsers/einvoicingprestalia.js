/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


/* 
   ***** INSTALLATION OF einvoicingprestalia  module *****
    add this code to template /modules/deotemplate/views/templates/front/onepagecheckout/block/address-invoice.tpl at the end of <section class="form-fields">:
    {block name='form_field'}
      {widget name="einvoicingprestalia"}
    {/block}
*/

deo_confirmOrderValidations['einvoicingprestalia'] = function() {
  
  if (installedModules['einvoicingprestalia']) {

    var einvoicingprestalia_requried_fields = new Array('prestalia_pec', 'prestalia_sdi');
    var einvoicingprestalia_errors = {};

    $.each(einvoicingprestalia_requried_fields, function(index, einvoicingprestalia_field_name) 
    {
      
        if (
                $('[name='+einvoicingprestalia_field_name+']').length && 
                (
                    '' == jQuery.trim($('[name='+einvoicingprestalia_field_name+']').val()) ||
                    (
                        jQuery.trim($('[name='+einvoicingprestalia_field_name+']').val()).length <7 ||
                        jQuery.trim($('[name='+einvoicingprestalia_field_name+']').val()).length >7
                    )
                ) &&
                $('[name='+einvoicingprestalia_field_name+']').closest('.form-group').find('.required:visible').length
            )
        {
            einvoicingprestalia_errors[einvoicingprestalia_field_name] = i18_sdiLength;
        }

    });

    if (!$.isEmptyObject(einvoicingprestalia_errors)) {
      printContextErrors('#deoonepagecheckout-address-invoice', einvoicingprestalia_errors);
      return false;
    } else {
      return true;
    }

  }//if (installedModules['einvoicingprestalia'])

  return true;
}//deo_confirmOrderValidations

checkoutShippingParser.einvoicingprestalia = {
  init_once: function (elements) {

  },

  delivery_option: function (element) {

  },

  extra_content: function (element) {
  }

}
