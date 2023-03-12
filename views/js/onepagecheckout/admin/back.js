/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

function dataUpdate() {
    var sortedObject = sortable('table.address-fields tbody', 'serialize');
    var json = JSON.stringify(sortedObject[0].container, null, 2);
    $('.invoice_fields_input > input').first().val(json).trigger('change');
    json = JSON.stringify(sortedObject[1].container, null, 2);
    $('.delivery_fields_input > input').first().val(json).trigger('change');
}

function customerFieldsUpdate() {
    var sortedObject = sortable('table.customer-fields tbody', 'serialize');
    var json = JSON.stringify(sortedObject[0].container, null, 2);
    $('.customer_fields_input > input').first().val(json).trigger('change');
}

function disableDetailsOnVisibilityChange() {
    $('input[name="visible"]').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).closest('tr').find('input,select').not('[name="visible"]').not('[type="hidden"]').attr('disabled', false);
        } else {
            $(this).closest('tr').find('input,select').not('[name="visible"]').not('[type="hidden"]').attr('disabled', true);
        }
    });
}

function resetDefaultConfiguration(resetActionName) {
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: "json",
        data: "&ajax_request=1&action=" + resetActionName,
        success: function (jsonData) {
            location.reload();
        }
    });
}


// ===============================================================================
// READY
// ===============================================================================

$(document).ready(function () {

    sortable('.customer-fields tbody', {
        items: "tr",
        placeholderClass: 'ph-class',
        hoverClass: 'hvr-class',
        forcePlaceholderSize: true,
        handle: '.js-handle',
        containerSerializer: function (serializedContainer) {
            var serialized = {};
            var width = null;
            $.each(serializedContainer.node.children, function () {
                width = $(this).find('[name="width"]').val();
                serialized[$(this).find('[name="field-name"]').val()] = {
                    'visible': $(this).find('[name="visible"]').is(':checked'),
                    'required': $(this).find('[name="required"]').is(':checked'),
                    'width': (isNaN(parseInt(width)) || width < 0 || width > 100) ? 100 : width
                }
            });
            return serialized;
        }
    });
    sortable('.customer-fields tbody')[0].addEventListener('sortupdate', customerFieldsUpdate);
    $('.customer-fields input,.customer-fields select').on('change', customerFieldsUpdate);
    


    sortable('.address-fields tbody', {
        items: "tr",
        // placeholder: "<tr><td colspan=\"4\"><span class=\"center\">The row will appear here</span></td></tr>",
        placeholderClass: 'ph-class',
        hoverClass: 'hvr-class',
        forcePlaceholderSize: true,
        handle: '.js-handle',
        containerSerializer: function (serializedContainer) {

            var serialized = {};
            var width = null;
            $.each(serializedContainer.node.children, function () {
                width = $(this).find('[name="width"]').val();
                serialized[$(this).find('[name="field-name"]').val()] = {
                    'visible': $(this).find('[name="visible"]').is(':checked'),
                    'required': $(this).find('[name="required"]').is(':checked'),
                    'width': (isNaN(parseInt(width)) || width < 0 || width > 100) ? 100 : width,
                    'live': $(this).find('[name="live"]').is(':checked')
                }
            });
            return serialized;
        }
    });
    sortable('.address-fields tbody')[0].addEventListener('sortupdate', dataUpdate);
    sortable('.address-fields tbody')[1].addEventListener('sortupdate', dataUpdate);
    $('.address-fields input,.address-fields select').on('change', dataUpdate);

    disableDetailsOnVisibilityChange();


    $(document).on('click', '.reset-link a', function () {
        // deoonepagecheckout_reset_conf_for is set in hookDisplayBackOfficeHeader()
        var retVal = confirm(deoonepagecheckout_reset_conf_for + " [" + $(this).data('section') + '] ?');
        if (retVal == true) {
            resetDefaultConfiguration($(this).data('action'));
        }
    });
    
    $('.tinymce-on-demand').closest('.translatable-field').parent().closest('.form-group').find('textarea').after('<div class="init-html-editor-container"><a href="javascript:void(0)" class="init-on-demand-html-editor">' + deoonepagecheckout_init_html_editor + '</a></div>');

    $('.form-group').on('click', '.init-on-demand-html-editor', function () {
        $(this).closest('.form-group').addClass('about-to-init-tinymce');
        tinySetup({selector: '.about-to-init-tinymce .tinymce-on-demand', forced_root_block: ''});
        $(this).closest('.form-group').removeClass('about-to-init-tinymce');
        $(this).fadeOut();
    });

    if ($('.deo-tab-config').length > 0){
        $('.deo-tab-config').click(function () {
            if ($(this).data('value') == 'tab_customer_address'){
                toogle_switch($('.show_business_fields input[type="radio"]'),$('.group_show_business_fields'),400);
                toogle_switch($('.show_private_customer input[type="radio"]'),$('.group_show_private_customer'),400);
            }
            if ($(this).data('value') == 'tab_shipping_payment'){
                toogle_switch($('.show_separate_payment input[type="radio"]'),$('.group_show_separate_payment'),400, true);
            }
        });
        $('.tabConfig.active .deo-tab-config').trigger('click');
    }
});
