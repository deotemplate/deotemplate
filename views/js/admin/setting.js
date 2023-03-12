/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
function proccessData(id) {
    var list = $.trim($(id).val()).split(",");
    var result = "";
    var sep = "";
    for(var i = 0; i < list.length; i++) {
        if($.trim(list[i])) {
            result += sep + $.trim(list[i]);
            sep = ",";
        }
    }
    $(id).val(result);
}
$(document).ready(function() {
    $('.panel-content-builder').slideUp();
    $('span.open-content').click(function(){
        $(this).closest('.panel').find('.panel-content-builder').slideToggle();
    });
	
    // SHOW FULL input html
    $(".deo-html-full").closest( ".col-lg-9.col-lg-offset-3" ).removeClass("col-lg-9 col-lg-offset-3");
	
	// hide home config with theme of deotheme, redirect to profile page
	if ($('#psthemecusto').length && (typeof deo_check_theme_name != 'undefined')){		
		$('#psthemecusto .panel').hide();
		$('#psthemecusto').append('<div class="panel"><div class="panel-heading">'+deo_profile_txt_redirect+': <a target="_blank" href="'+deo_profile_url+'">'+deo_profile_url+'</a></div></div>');
	}
});
function compareListHooks(oldHooks, newHooks) {
    //console.log(oldHooks);
    //console.log(newHooks);
    isEqual = true;
    if(oldHooks.length > 0 && newHooks.length > 0) {
        for(var i = 0; i < oldHooks.length; i++) {
            var isSubLook = false;
            for(var j = 0; j < newHooks.length; j++) {
                if(oldHooks[i] === newHooks[j]) {
                    newHooks.splice(j, 1);
                    //console.log(newHooks);
                    isSubLook = true;
                    break;
                }
            }
            if(!isSubLook) {
                return false;
            }
        }
    }
    return isEqual;
}

$(function() {

    // SHOW MENU 'Deo Module Configuration' - BEGIN
    if( (typeof(js_deo_controller) != 'undefined') && (js_deo_controller == 'module_configuration')){
        $('#subtab-AdminDeoTemplate').addClass('active');
        $('#subtab-AdminDeoDeveloperConfigure').addClass('active');
        $('#subtab-AdminParentModulesSf').removeClass('active');
    }
    // SHOW MENU 'Deo Module Configuration' - END
    
    // Hide RESUME at left menu, BO
    if( (typeof(js_dep_dev) != 'undefined') && (js_deo_dev == 1)){
        // $('#tab-AdminDashboard').hide();
        $('.onboarding-navbar').hide();
    }
    
    $("#hook_header_old").val($("#HEADER_HOOK").val());
    $("#hook_content_old").val($("#CONTENT_HOOK").val());
    $("#hook_footer_old").val($("#FOOTER_HOOK").val());
    $("#hook_product_old").val($("#PRODUCT_HOOK").val());
    
    $(".list-all-hooks a").click(function() {
        var newHook = $.trim($(this).text());
        var id = $("#position-hook-select").val();
        id = id ? id : "#HEADER_HOOK";
        var listHook = $.trim($(id).val());
        if(listHook.search(newHook) < 0) {
            listHook += (listHook ? "," : "") + newHook;
            $(id).val(listHook);
        } else {
            alert("This hook is existed");
        }
    });
    $("#HEADER_HOOK, #CONTENT_HOOK, #FOOTER_HOOK, #PRODUCT_HOOK").focus(function() {
        var id = "#" + $(this).attr("id");
        $("#position-hook-select").val(id);
    });
    
    /**
     * SAVE MODULE CONFIGUATION
     */
    $("#btn-save-deotemplate").click(function(e) {
        $isChange = false;
        proccessData("#HEADER_HOOK");
        proccessData("#CONTENT_HOOK");
        proccessData("#FOOTER_HOOK");
        proccessData("#PRODUCT_HOOK");
        // Check change config hooks
        var oldHook = $.trim($("#hook_header_old").val()).split(",");
        var currentHook = $.trim($("#HEADER_HOOK").val()).split(",");
        if(oldHook.length != currentHook.length || !compareListHooks(oldHook, currentHook)) {
            $isChange = true;
        }
        oldHook = $.trim($("#hook_content_old").val()).split(",");
        currentHook = $.trim($("#CONTENT_HOOK").val()).split(",");
        if(oldHook.length != currentHook.length || !compareListHooks(oldHook, currentHook)) {
            $isChange = true;
        }
        oldHook = $.trim($("#hook_footer_old").val()).split(",");
        currentHook = $.trim($("#FOOTER_HOOK").val()).split(",");
        if(oldHook.length != currentHook.length || !compareListHooks(oldHook, currentHook)) {
            $isChange = true;
        }
        oldHook = $.trim($("#hook_product_old").val()).split(",");
        currentHook = $.trim($("#PRODUCT_HOOK").val()).split(",");
        if(oldHook.length != currentHook.length || !compareListHooks(oldHook, currentHook)) {
            $isChange = true;
        }
        if($isChange) {
            if(!confirm($("#message_confirm").val())) {
                e.stopPropagation();
                return false;
            }
            $("#is_change").val("is_change");
        }
        
    });
    
    $("#modal_form").on("click", ".btn-savewidget", function() {
        // Is form add new module to deotemplate
        if($("#modal_form").find(".form_ap_module").length > 0) {
            // Validate select hook
            if(!$("#select-hook").val()) {
                alert($("#select-hook-error").val());
                return false;
            }
        }
    });
    
    /* EXPORT + IMPORT - BEGIN */
    $(".btn_xml_export").click(function(e) {
        e.preventDefault();
        if (typeof record_id !== 'undefined') {
            var url = $(this).closest('a').attr('href');
            $('input[name="'+record_id+'"]').each(function(i){
                if( $(this).is(':checked')){
                    url += '&record_id[]=' + $(this).attr('value');
                }
            });
            window.location.href = url;
        }
    });
    
    $(".btn_xml_import").click(function(e) {
        e.preventDefault();
        $('#xml_import_form').slideToggle();
        $(':focus').blur();
    });
    /* EXPORT + IMPORT - END */
});

$(document).ready(function() {
    $('.reset-customize').click(function(e) {
        let id = $(this).attr('id');
        let default_value = JSON.parse($('input[name="'+id+'"]').val());
        $.each(default_value, function(key, value){
            $('input[name="'+key+'"]').val(value);
            if ($('input[name="'+key+'"]').hasClass('mColorPicker')){
                $('input[name="'+key+'"]').trigger('keyup');
            }else{
                $('input[name="'+key+'"]').trigger('change');
            }
        });
    });
});