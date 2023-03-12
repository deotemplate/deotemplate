/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

$(document).ready(function() {
    if ($('.deo-tab-config').length > 0){
        //set tab aciton
        // $('.deo-tab-config').each(function () {
        //     if (!$(this).parent().hasClass('active')) {
        //         let element = $(this).attr('href').toString().replace("#", ".");
        //         $(element).hide();
        //     }
        // });

        $('.deo-tab-config').click(function () {
            let divElement = $(this).attr('href').toString().replace("#", ".");
            let aElement = $(this);
            $(divElement).show();
            $('.deo-tab-config').parent().removeClass('active');
            $(aElement).parent().addClass('active');

            $('.form-action', $(divElement)).each(function () {
                $(this).trigger("change");
            });

            $('.tabConfig:not(.active) .deo-tab-config').each(function () {
                let element = $(this).attr('href').toString().replace("#", ".");
                $(this).parent().removeClass('active');
                $(element).hide();
            });

            if ($(this).data('value') == 'tab_google_map'){
                toogle_switch($('.show_select_store input[type="radio"]'),$('.group_show_select_store'),400);
            }
            if ($(this).data('value') == 'tab_general'){
                toogle_select($('.select_skin'), $('.group_show_select_skin'), 'custom-skin', 400);
            }
            if ($(this).data('value') == 'tab_infinite_scroll'){
                toogle_switch($('.show_load_more_product input[type="radio"]'),$('.group_show_load_more_product'),400);
            }
        });
        $('.tabConfig.active .deo-tab-config').trigger('click');
    }

    $('input[name="show_popup_after_add_to_cart"]').change(function(){
        if ($(this).val() == 1){
            $('input[name="open_advance_cart_after_add_to_cart"][value="0"]').prop('checked', true);
            $('input[name="open_advance_cart_after_add_to_cart"][value="1"]').prop('checked', false);
        }else{
            $('input[name="open_advance_cart_after_add_to_cart"][value="0"]').prop('checked', false);
            $('input[name="open_advance_cart_after_add_to_cart"][value="1"]').prop('checked', true);
        }
        // $('input[name="open_advance_cart_after_add_to_cart"]').trigger('change');
    });
    $('input[name="open_advance_cart_after_add_to_cart"]').change(function(){
        if ($(this).val() == 1){
            $('input[name="show_popup_after_add_to_cart"][value="0"]').prop('checked', true);
            $('input[name="show_popup_after_add_to_cart"][value="1"]').prop('checked', false);
        }else{
            $('input[name="show_popup_after_add_to_cart"][value="0"]').prop('checked', true);
            $('input[name="show_popup_after_add_to_cart"][value="1"]').prop('checked', false);
        }
        // $('input[name="show_popup_after_add_to_cart"]').trigger('change');
    });

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
});