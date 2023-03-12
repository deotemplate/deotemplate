/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if ($('.colorpicker-element').length){
    $('.colorpicker-element').colorpicker();
    $('.colorpicker-element .color-picker').keyup(function(){
        if ($(this).val() == ''){
            let colorpicker = $(this).closest('.colorpicker-element');  
            colorpicker.colorpicker('setValue', '#000000');
            $(this).val('');
        }
    });
}