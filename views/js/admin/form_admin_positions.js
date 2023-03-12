/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

$(document).ready(function() {
    $('.deo_delete_position').each(function(){
        
        $(this).closest('a').attr('href',"javascript:void(0);");
        
        $('<input>', {
            type: 'hidden',
            id : 'deo_delete_position',
            name: 'deo_delete_position',
            value: '0'
        }).appendTo( $(this).parent() );
        
        $(this).closest('a').click(function(){
            if (confirm(deo_confirm_text)){
                $('#deo_delete_position').val('1');
                $(this).closest('form').attr('action', deo_form_submit);
                $(this).closest('form').submit();
            }
        });
    });
});