/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
$(document).ready(function() {
    //only for product generate
    $('.plist-eedit').click(function(){
        element = $(this).data('element');
        $.fancybox.open([{
                type: 'iframe', 
                href : ($('#deotemplate_products_form').length ? $('#deotemplate_products_form').attr('action') : $('#deotemplate_details_form').attr('action')) + '&pelement=' + element,
                afterLoad:function(){
                    if( $('body',$('.fancybox-iframe').contents()).find("#main").length  ){
                        hideSomeElement();
                        $('.fancybox-iframe').load(hideSomeElement);
                    }else { 
                        $('body',$('.fancybox-iframe').contents()).find("#psException").html('<div class="alert error">Can not find this element</div>');
                    }
                },
                afterClose: function (event, ui) { 
                }
            }], {
            padding: 10
        });
    });
    
    $('.element-list .plist-element').draggable({
        cursor: 'move',
        connectToSortable: ".layout-container .content",
        revert: true,
        helper: "clone",
        start: function( event, ui ) {
            $(this).addClass('latest-draggable'); 
        },
        stop: function(event,ui) {
            setSortAble();
            $(this).removeClass('latest-draggable');
        },
        revert : function(wrapper) {
            if (wrapper){
                if (wrapper.parent().hasClass("box") && $(this).hasClass("box")){
                    wrapper.parent().find(".latest-draggable").addClass('error').removeClass('latest-draggable');
                    // return true;
                }else{
                    wrapper.closest('.layout-container').find('.plist-element').removeClass('latest-draggable');
                    // return false;
                }
            }
        }
    });
    
    $('#deotemplate_products_form').submit(function(e) {
        // e.preventDefault();
        genreateForm();
    });

    $(document).on('click','.plist-code',function(){
        textAre = $(this).closest('.plist-element').find('textarea').first();
        if(textAre.attr('rows') == 20){
            $(textAre).attr('rows',5);
            $(this).removeClass('more');
        }else{
            $(textAre).attr('rows',20);
            $(this).addClass('more');
        }
    });

    $(document).on('click','.plist-eremove',function(){
        if (!confirm(deo_message_delete)) return false;
        $(this).closest('.plist-element').remove();
    });

    $(document).on("click", ".btn-status", function () {
        let element = $(this).closest('.plist-element');
        let objForm = element.data("form");
        if (element.hasClass("deactive")) {
            element.removeClass("deactive").addClass("active");
            objForm.active = 1;
            $(this).children().removeClass("icon-remove");
            $(this).children().addClass("icon-ok");
        } else {
            element.removeClass("active").addClass("deactive");
            objForm.active = 0;
            $(this).children().addClass("icon-remove");
            $(this).children().removeClass("icon-ok");
        }
        element.data('form', objForm);
    });

    $(document).on('click', '.product_list_builder .element-config', function (e) {
        let element = $(this).closest('.plist-element');
        let data = element.data('form');

        element.addClass('active-formmodal');
        $('#modal_form .modal-footer').show();
        $('#modal_form .modal-body').html('');

        let config = $(this).data('config');
        let column_config = $("#"+config).clone(true);

        // console.log(column_config);
        Object.keys(data).forEach(function (key) {
            column_config.find('input[name='+key+']').val(data[key]);
            column_config.find('select[name='+key+']').val(data[key]);
        });


        $('#modal_form .modal-body').append('<form class="formmodal"></form>');
        $('#modal_form .formmodal').append(column_config);
        $('#modal_form .modal-title').html(title_modal[config]);
        $('#modal_form .responsive').trigger('change');
        $('#modal_form .show_count').trigger('change');

        $('#modal_form').removeClass('modal-new').addClass('modal-edit');
        $("#modal_form").modal({
            "backdrop": "static"
        });
    });

    $(".btn-savewidget").click(function(){
        let data = new Object;
        $.map($(".formmodal").serializeArray(), function(n, i){
            data[n['name']] = n['value'];
        });

        if ($('.active-formmodal').data('element') == 'product_thumbnail'){
            $('.active-formmodal').find('.labelflag').html(deo_labelflag[data['labelflag']]);
            $('.active-formmodal').find('.effecthover').html(deo_effecthover[data['effecthover']]);
        }

        if ($('.active-formmodal').data('element') == 'deo_more_image_product' || $('.active-formmodal').data('element') == 'deo_more_image_product_pro' || $('.active-formmodal').data('element') == 'product_thumbnail'){
            $('.active-formmodal').find('.size').html(data['size']);
        }

        if ($('.active-formmodal').data('element') == 'deo_more_image_product' || $('.active-formmodal').data('element') == 'deo_more_image_product_pro'){
            $('.active-formmodal').find('.type').html(deo_type_more_image[data['type']]);
        }

        $('.active-formmodal').data('form', data);
        $("#modal_form .close").trigger('click');
    });

    $('#modal_form').on('hidden.bs.modal', function () {
        $('#modal_form .modal-body').html('');
        $('.active-formmodal').removeClass('active-formmodal');
    });

    $(document).on('change', '.responsive', function() {
        if ($(this).val() == 1){
            $('#modal_form .breakpoints-input').show(400);
        }else{ 
            $('#modal_form .breakpoints-input').hide(400);
        }
    });

    $(document).on('change', '.show_count', function() {
        if ($(this).val() == 1){
            $('#modal_form .count-text-input').show(400);
        }else{ 
            $('#modal_form .count-text-input').hide(400);
        }
    });
    
    setSortAble();

    function genreateForm(){
        //generate grid first
        var ObjectFrom = {};
        ObjectFrom.image = returnObjElemnt('.product_list_builder .image-block-content');
        ObjectFrom.meta = returnObjElemnt('.product_list_builder .meta-block-content');

        $('input[name=params]').val(JSON.stringify(ObjectFrom));
        $('#list-temp-element').remove();
        // console.log(JSON.stringify(ObjectFrom));
    }

    function returnObjElemnt(element){
        var Object = {};
        $(element).children().each(function(key){
            var Obj = {};
            Obj.name = $(this).data('element');

            if ($(this).data("form") == undefined || $(this).data("form") == "" || $(this).data("form") == '\"\"')
                Obj.form = {};
            else
                Obj.form = $(this).data('form');

            if ($(this).hasClass('deactive'))
                Obj.form.active = 0;
            else
                Obj.form.active = 1;
            
            if($(this).hasClass('box')){
                Obj.element = returnObjElemnt($('.content', $(this)));
                Obj.form.css = $(this).find('.css').first().val();
            }
            if($(this).hasClass('code')){
                Obj.code = replaceSpecialString($('textarea', $(this)).val());
            }
            Object[key] = Obj;
        });

        // console.log(Object);
        return Object;
    }

    function hideSomeElement(){
        $('body',$('.fancybox-iframe').contents()).addClass("page-sidebar-closed");
    }

    function setSortAble(){
        $(".layout-container .content").sortable({
            cursor: 'move',
            connectWith: ".content",
            revert: 'true',
            stop: function(event, ui) {
                let item = $(ui.item);
                let wrapper = $(event.target);
                if (item.parent().parent().hasClass("box") && item.hasClass("box")){
                    $(this).sortable('cancel');
                    $(".layout-container").find(".error").remove();
                    showErrorMessage(deo_message_box);
                }
            }
        });
    }
});
