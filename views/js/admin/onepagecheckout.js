/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

var windowWidth = $(window).width();
$(document).ready(function() {
    //only for product generate
    $(".admindeotemplatedetails #nav-sidebar .menu-collapse").trigger("click");

    $('.plist-eedit').click(function(){
        element = $(this).data('element');
        $.fancybox.open([{
                type: 'iframe', 
                href : ($('#deotemplate_products_form').length?$('#deotemplate_products_form').attr('action'):$('#deotemplate_onepagecheckout_form').attr('action')) + '&pelement=' + element,
                afterLoad:function(){
                    if( $('body',$('.fancybox-iframe').contents()).find("#main").length  ){
                        hideSomeElement();
                        $('.fancybox-iframe').load( hideSomeElement );
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

    $(".hook-content").sortable({
        cursor: 'move',
        connectWith: ".hook-content",
        revert: "true",
        handle: ".gaction-drag"
    });

    $('.element-list .plist-element').draggable({
        connectToSortable: ".layout-container .column-content,.layout-container .box-content",
        cursor: 'move',
        revert: "true",
        helper: "clone",
        appendTo: '.admin-pagebuilder-onepagecheckout',
        scroll: false,
        zIndex: 10000,
        // handle: ".waction-drag",
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

    $('.show-postion').click(function(){$("#postion_layout img").show()});
    $('.postion-img-co').click(function(){$("#postion_layout img").toggle()});


    $(document).on("click", ".btn-change-colwidth", function () {
        cla = returnWidthClass();
        elementColumn = $(this).closest('.column-row');
        objColumn = $(elementColumn).data('form');
        console.log(objColumn);
        valueColToNum = objColumn[cla].toString().replace("-", ".");
        val = $(this).data("value");
        // console.log(cla + '--' + valueColToNum + 'claa' + cla);
        if (val == 1 && parseFloat(valueColToNum) >= 12) {
            alert($("#form_content").data("increase"));
            return false;
        }
        if (val == -1 && parseFloat(valueColToNum) <= 1) {
            alert($("#form_content").data("reduce"));
            return false;
        }

        let widthSupport = ["1", "2", "2.4", "3", "4", "5", "4.8", "6", "7", "7.2", "8", "9", "9.6", "10", "11", "12"];
        //get index of current width
        indexW = jQuery.inArray(valueColToNum.toString(), widthSupport);
        indexW = parseInt(indexW) + val;
        //get new width
        objColumn[cla] = widthSupport[indexW];
        //set class again
        classColumn = getClassColumn(objColumn);

        console.log(objColumn);
        $(elementColumn).attr("class", classColumn);
        $(".deo-btn-width .width-val", $(elementColumn)).attr("class", "width-val deo-w-" + objColumn[cla].toString().replace(".", "-"));
        $(elementColumn).data("form", objColumn);
        getNumberColumnInClass(elementColumn, returnWidthClass());
        updateClassWidget(elementColumn);

        return false;
    });

    $(document).on("click", ".change-colwidth", function () {
        cla = returnWidthClass();
        width = $(this).data('width');
        elementColumn = $(this).closest('.column-row');
        objColumn = $(elementColumn).data('form');
        //get new width
        objColumn[cla] = width;
        //set class again
        classColumn = getClassColumn(objColumn);

        $(elementColumn).attr("class", classColumn);
        $(".deo-btn-width .width-val", $(elementColumn)).attr("class", "width-val deo-w-" + objColumn[cla].toString().replace(".", "-"));
        $(elementColumn).data("form", objColumn);
        $(this).closest("ul").find("li").removeClass("selected");
        $(this).closest("li").addClass("selected");
        getNumberColumnInClass(elementColumn, returnWidthClass());
        updateClassWidget(elementColumn);

        return false;
    });

    function updateClassWidget (element) {
        let type = $(element).data('type');
        let data_form = $(element).data('form');
        let data_class = $.trim($(element).data('class'));
        let data_class_form;
        if (type == 'DeoColumn'){
            let columnClass = '';
            Object.keys({xxl:12, xl:12, lg: 12, md: 12, sm: 12, xs: 12, sp: 12}).forEach(function (key) {
                columnClass += ' col-' + key + '-' + data_form[key].toString().replace('.', '-');
            });
            data_class_form = $.trim(data_form.class)+' '+columnClass;
        }else{
            data_class_form = $.trim(data_form.class);
        }

        if ($(element).hasClass('new-shortcode')){
            $(element).addClass(data_class_form);
            $(element).removeClass('new-shortcode');
            $(element).data('class',data_class_form);
        }else{
            if (data_class_form != data_class){
                $(element).removeClass(data_class);
                $(element).addClass(data_class_form);
                $(element).data('class',data_class_form);
            }
        }
    };

    function getClassColumn (objCol) {
        let classWidget = 'plist-element ui-widget ui-widget-content ui-helper-clearfix ui-corner-all';
        let arrayCol = ["sp", "xs", "sm", "md", "lg", "xl", "xxl"];

        classColumn = 'column-row ' + classWidget;
        classColumn += (objCol.active == 1) ? ' active' : '';
        for (ic = 0; ic < arrayCol.length; ic++) {
            if (objCol[arrayCol[ic]]) {
                valueCol = objCol[arrayCol[ic]];
                if (valueCol.toString().indexOf(".") != -1) {
                    valueCol = valueCol.toString().replace(".", "-");
                }
                classColumn += " col-" + arrayCol[ic] + "-" + valueCol;
            }
        }
        return classColumn;
    }

    function returnWidthClass (width) {
        if (!width)
            width = $(window).width();
        if (parseInt(width) >= 1500)
            return 'xxl';
        if (parseInt(width) >= 1200)
            return 'xl';
        if (parseInt(width) >= 992)
            return 'lg';
        if (parseInt(width) >= 768)
            return 'md';
        if (parseInt(width) >= 576)
            return 'sm';
        if (parseInt(width) >= 480)
            return 'xs';
        if (parseInt(width) < 480)
            return 'sp';
    }
    
    $(document).on("click", ".column-add", function () {
        createColumn(this);
    });

    setProFormAction();
    setSortAble();
    createGroup();

    $('body').on('click', function (e) {
        $('[data-toggle=popover]').each(function () {
            // hide any open popovers when the anywhere else in the body is clicked
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });

    widthselect();
    setCssClass();
    saveData();

    $('#deotemplate_onepagecheckout_form').submit(function(e) {
        // e.preventDefault();
        genreateForm();
    });


    function genreateForm(){
        //generate grid first
        let ObjectFrom = new Object();
        ObjectFrom.objectForm = returnObjElemnt('.admin-pagebuilder-onepagecheckout .hook-content');
        ObjectFrom.class = $("#main_class").val();
        $('input[name=params]').val(JSON.stringify(ObjectFrom));
        console.log(ObjectFrom);
        $('#list-temp-element').remove();
    }

    function returnObjElemnt(element){
        let Object_result = {};
        // console.log(element);
        $(element).children('.plist-element').each(function(key){
            let Obj = {};

            Obj.name = $(this).data('element');
            if ($(this).data("form") == undefined || $(this).data("form") == "" || $(this).data("form") == '\"\"')
                Obj.form = {};
            else
                Obj.form = $(this).data('form');

            if ($(this).hasClass('deactive'))
                Obj.form.active = 0;
            else
                Obj.form.active = 1;

            if (Obj.name=='product_cover_thumbnails' && Obj.form.type == 'thumbnail'){
                if (Obj.form.thumb == 'left' || Obj.form.thumb == 'right' || Obj.form.thumb == 'bottom')
                    $("#main_class").val('detail-thumbnail product-thumbs-'+Obj.form.thumb);
                else 
                    $("#main_class").val('detail-thumbnail no-thumbs');
            }else if (Obj.name=='product_cover_thumbnails' && Obj.form.type == 'gallery'){
                $("#main_class").val('detail-gallery');
            }

            if ($(this).hasClass('group-row')){
                Obj.columns = returnObjElemnt($('.group-content', $(this)));
            }

            if ($(this).hasClass('column-row')){
                Obj.sub = returnObjElemnt($('.column-content', $(this)));
            }

            if ($(this).hasClass('box')){
                Obj.form.css = $(this).find('.css').first().val();
                Obj.element = returnObjElemnt($('.box-content', $(this)));
            }

            if($(this).hasClass('code')){
                Obj.code = replaceSpecialString($('textarea', $(this)).val());
            }

            Object_result[key] = Obj;
        });

        // console.log(Object_result);

        return Object_result;
    }
    function replaceSpecialString(str){
        return str.replace(/\t/g, "_APTAB_").replace(/\r/g, "_APNEWLINE_").replace(/\n/g, "_APENTER_").replace(/"/g, "_APQUOT_").replace(/'/g, "_APAPOST_");
    }
    function saveData(){
        $(".btn-savewidget").click(function(){
            let data_old = $(".active-formmodal").data('form');
            let data = getFormData($(".formmodal"));
            let old_class = data_old.class;
            let new_class = data.class;

            if ($('.active-formmodal').data('element') == 'account'){
                $('.active-formmodal').find('.type').html(deo_type_account[data['type']]);
                $('.active-formmodal').find('.use_tab').html(deo_use_tab[data['use_tab']]);
            }

            if (old_class != new_class){
                $('.active-formmodal').removeClass(old_class).addClass(new_class);
            }

            $('.active-formmodal').data('form', data);
            $("#modal_form .close").trigger('click');
        });

        $('#modal_form').on('hidden.bs.modal', function () {
            $('.active-formmodal').removeClass('active-formmodal');
        });
    }

    function getFormData($form){
        let unindexed_array = $form.serializeArray();
        let indexed_array = {};
        let current = $('.active-formmodal');
        let array_col_key = ['xxl','xl','lg','md','sm','xs','sp'];

        // remove class width column
        if (current.hasClass('column-row')){
            $.each(current.data('form'), function(key,value){
                if (array_col_key.indexOf(key) >= 0)
                    current.removeClass('col-'+key+'-'+value);
            });
        }

        $.map(unindexed_array, function(n, i){
            if (n['name']!='hidden_from[]')
                indexed_array[n['name']] = n['value'];

            // process new class width column
            if (current.hasClass('column-row') && array_col_key.indexOf(n['name']) >=0){
                current.addClass('col-'+n['name']+'-'+n['value']);
            }
        });

        return indexed_array;
    }

    function hideSomeElement(){
        $('body',$('.fancybox-iframe').contents()).addClass("page-sidebar-closed");
    }

    function setSortAble(){
        $(".layout-container .column-content.sort-content,.layout-container .box-content.sort-content").sortable({
            cursor: 'move',
            connectWith: ".column-content.sort-content,.box-content.sort-content",
            revert: 'true',
            handle: ".waction-drag",
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
        $(".layout-container .group-content.sort-content").sortable({
            cursor: 'move',
            connectWith: ".group-content.sort-content",
            revert: 'true',
            handle: ".caction-drag",
         });
    }

    function setCssClass(){
        $(".select-class").click(function(){
            let classChk = $(this).attr("value");
            let elementText = $(this).closest('.form-group').find('.element_class').first();
            let str = elementText.val();
            let regex =  new RegExp("\\b"+classChk+"\\b\(\?\!\-\)", "g"); 

            if ($(this).is(':checked')) {
                // NOT EXIST AND ADD
                if (!regex.test(str)){
                    str += " "+classChk;
                }
            }else{
                // EXIST AND REMOVE
                str = str.replace(regex, "");
            }

            elementText.val($.trim(str));
        });


        $(".element_class").change(function () {
            elementChk = $(this).closest('.well').find('input[type=checkbox]');
            classText = $(this).val();
            $(elementChk).each(function () {
                let classChk = $(this).attr("value");
                let regex =  new RegExp("\\b"+classChk+"\\b\(\?\!\-\)", "g"); 
                if (regex.test(classText)) {
                    if (!$(this).is(':checked'))
                        $(this).prop("checked", true);
                } else {
                    $(this).prop("checked", false);
                }
            });
        });
    }

    //set action when c
    function setProFormAction(){
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

        $(document).on("click", ".btn-duplicate", function () {
            let element = $(this).closest('.plist-element');
            let parent = element.parent();
            parent.append(element.clone(1));
        });

        $(document).on('click', '#home_wrapper .plist-code', function (e) {
            let textAre = $(this).closest('.plist-element').find('textarea').first();
            if (textAre.attr('rows') == 20){
                $(textAre).attr('rows',5);
                $(this).removeClass('more');
            }else{
                $(textAre).attr('rows',20);
                $(this).addClass('more');
            }
        });

        $(document).on('click', '#home_wrapper .plist-eremove', function (e) {
            if (!confirm(deo_message_delete)) return false;
            $(this).closest('.plist-element').remove();
        });


        $(document).on('change', '.use_tab', function() {
            if ($(this).val() == '1'){
                $(this).closest('.formmodal').find('.group-type').hide(400);
            }else{
                $(this).closest('.formmodal').find('.group-type').show(400);
            }
        });


        $(document).on('click', '#home_wrapper .btn-edit-group', function (e) {
            let group = $(this).closest('.plist-element');
            group.addClass('active-formmodal');
            $('#modal_form .modal-footer').show();

            $('#modal_form .modal-body').html('');

            let column_config = $("#group_config").clone(true);

            //load config
            let data = group.data('form');

            column_config = setData(data, column_config);
            
            $('#modal_form .modal-body').append('<form class="formmodal"></form>');
            $('#modal_form .formmodal').append(column_config);
            $('#modal_form .modal-title').html(title_group);
            $('#modal_form .element_class').trigger("change");

            $('#modal_form').removeClass('modal-new').addClass('modal-edit');
            $("#modal_form").modal({
                "backdrop": "static"
            });
        });

        $(document).on('click', '#home_wrapper .element-config', function (e) {
            let element = $(this).closest('.plist-element');
            element.addClass('active-formmodal');
            $('#modal_form .modal-footer').show();

            $('#modal_form .modal-body').html('');

            let dataconfig = $(this).data('config');

            let column_config = $("#"+dataconfig).clone(true);
            //load config
            
            let data = element.data('form');
            
            column_config = setData(data, column_config);

            $('#modal_form .modal-body').append('<form class="formmodal"></form>');
            $('#modal_form .formmodal').append(column_config);
            $('#modal_form .modal-title').html(title_image);
            $('#modal_form .element_class').trigger("change");

            $('#modal_form').removeClass('modal-new').addClass('modal-edit');
            $("#modal_form").modal({
                "backdrop": "static"
            });

            $('#modal_form .use_tab').trigger('change');
        });

        editcolumn();
    }

    function setData(data, column_config){
        if (!data || data == 'undefined' || data == 'undefined') return column_config;
        Object.keys(data).forEach(function (key) {
            let input = $(column_config).find('[name='+key+']');
            if (input.attr('type') == 'radio'){
                input.each(function(index, element){
                    if ($(element).val() === data[key]){
                        $(element).prop('checked', true);
                    }
                });
            }else{
                input.val(data[key]);
            }

            // if (key=="class" && $(column_config).find('.select-class').length){
            //     $(column_config).find('.select-class').each(function(){
            //         if (data[key].indexOf($(this).attr('value')) != -1) 
            //             $(this).prop("checked", true);
            //     });
            // }
            if (key=="xxl" || key=="xl" || key=="lg" || key=="md" || key=="sm" || key=="xs" || key=="sp"){
                let classcss = 'width-select-'+key+'-'+data[key];
                classcss = classcss.replace(".", "-");
                $(column_config).find('.'+classcss).trigger('click');
            }
        });

        return column_config;
    }

    function editcolumn(){
        $(document).on('click', '#home_wrapper .btn-edit-column', function (e) {
            let column = $(this).closest('.plist-element');
            column.addClass('active-formmodal');

            $('#modal_form .modal-body').html('');

            $('#modal_form .modal-footer').show();

            let data = column.data('form');

            let column_config = $("#column_config").clone(true);
            column_config = setData(data, column_config);

            $('#modal_form .modal-body').append('<form class="formmodal"></form>');
            $('#modal_form .formmodal').append(column_config);
            $('#modal_form .modal-title').html(title_column);
            $('#modal_form .element_class').trigger("change");

            $('#modal_form').removeClass('modal-new').addClass('modal-edit');
            $("#modal_form").modal({
                "backdrop": "static"
            });
        });
    }

    function widthselect(){
        $('.width-select').click(function () {
            let btnGroup = $(this).closest('.btn-group');
            let spanObj = $('.width-val', $(this));
            let width = $(spanObj).data('width');
            $('.col-val', $(btnGroup)).val(width);
            $('.deo-btn-width .width-val', $(btnGroup)).html($(spanObj).html());
            $('.deo-btn-width .width-val', $(btnGroup)).attr('class', $(spanObj).attr('class'));
        });
    }

    function createColumn(obj) {
        let widthCol = $(obj).data('width');
        let classActive = returnWidthClass();
        let col = $(obj).data('col');
        let realValue = widthCol.toString().replace('.', '-');
        for (let i = 1; i <= col; i++) {
            let wrapper = $(obj).closest('.group-row').children('.group-content');
            
            let column = $('#default_column').clone(1);
            
            let cls = $(column).attr("class");
            //column-row col-sp-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 ui-widget ui-widget-content ui-helper-clearfix ui-corner-all
            cls = cls.replace("col-xxl-12", "col-xxl-" + realValue);
            cls = cls.replace("col-xl-12", "col-xl-" + realValue);
            cls = cls.replace("col-lg-12", "col-lg-" + realValue);
            cls = cls.replace("col-md-12", "col-md-" + realValue);
            cls = cls.replace("col-sm-12", "col-sm-" + realValue);
            cls = cls.replace("col-xs-12", "col-xs-" + realValue);
            cls = cls.replace("col-sp-12", "col-sp-" + realValue);
            $(column).attr("class", cls);
            let objColumn = {form_id: "form_" + getRandomNumber()};
            if (classActive == "md" || classActive == "lg" || classActive == "xl" || classActive == "xxl") {
                objColumn.md = widthCol;
                objColumn.lg = widthCol;
                objColumn.xl = widthCol;
                objColumn.xxl = widthCol;
            }
    		// set default for sm, xs, sp
    		objColumn.sm = 12;
    		objColumn.xs = 12;
    		objColumn.sp = 12;

            $(column).data("form", objColumn);

            column.removeAttr('id').removeClass('hide');
            column.addClass('column-row plist-element active ui-widget ui-widget-content ui-helper-clearfix ui-corner-all');
            column.find('.column-content').sortable({
                cursor: 'move',
                connectWith: ".column-content",
                handle: ".waction-drag"
            });
            wrapper.append(column);

            getNumberColumnInClass(column, classActive);
            $(".label-tooltip").tooltip();
        }
    }

    function createGroup(){
        $('.btn-new-group').click(function(){
            let clone_row = $('#default_group').clone(1);
            clone_row.removeAttr('id');
            clone_row.removeClass('hide group-temp').addClass('group-row plist-element active ui-widget ui-widget-content ui-helper-clearfix ui-corner-all');
            clone_row.children('.group-content').sortable({
                cursor: 'move',
                connectWith: ".group-content",
                handle: ".caction-drag",
            });
            let default_data = new Object();
            default_data.class = 'row';
            clone_row.data('form',default_data);
            $('.hook-content').append(clone_row);
        });
    }

    function getNumberColumnInClass(obj, type) {
        let cls = $(obj).attr("class").split(" ");
        let len = cls.length;
        let result = "";
        for (let i = 0; i < len; i++) {
            if (cls[i].search("col-" + type) >= 0) {
                result = cls[i];
                break;
            }
        }
        let temp = result.replace("col-" + type + "-", "");
        $(obj).find(".pull-right .btn-group .btn span:first-child").attr("class", "width-val deo-w-" + temp);
        let group = $(obj).find("ul.dropdown-menu-right");
        $(group).find("li").removeClass("selected");
        $(group).find(".col-" + temp).addClass("selected");
    }

    function getRandomNumber()
    {
        return (+new Date() + (Math.random() * 10000000000000000)).toString().replace('.', '');
    }
    function returnWidthClass(width) {
        if (!width)
            width = windowWidth;
        if (parseInt(width) >= 1500)
            return 'xxl';
        if (parseInt(width) >= 1200)
            return 'xl';
        if (parseInt(width) >= 992)
            return 'lg';
        if (parseInt(width) >= 768)
            return 'md';
        if (parseInt(width) >= 576)
            return 'sm';
        if (parseInt(width) >= 480)
            return 'xs';
        if (parseInt(width) < 480)
            return 'sp';
    };
    $(document).on("click", ".btn-fwidth", function () {
        $('#home_wrapper').css('width', $(this).data('width'));

        let btnElement = $(this);
        $('.btn-fwidth').removeClass('active');
        $(this).addClass('active');
        //reset    
        if ($(this).hasClass('width-default')) {
            windowWidth = $(window).width();
            $('#home_wrapper').attr('class', 'default');
        } else {
            $('#home_wrapper').attr('class', 'col-' + returnWidthClass(parseInt($(this).data('width'))));
            windowWidth = $(this).data('width');
        }
        let classVal = returnWidthClass();
        $(".column-row", $('#home_wrapper')).each(function () {
            valueFra = $(this).data("form")[classVal];
            $(".deo-btn-width .width-val", $(this)).attr("class", "width-val deo-w-" + valueFra.toString().replace(".", "-"));
        });
        initColumnSetting();
    });

    function initColumnSetting() {
        let classActive = returnWidthClass();
        $(".column-row").each(function () {
            getNumberColumnInClass(this, classActive);
        });
    }

    function getNumberColumnInClass(obj, type) {
        let cls = $(obj).attr("class").split(" ");
        let len = cls.length;
        let result = "";
        for (let i = 0; i < len; i++) {
            if (cls[i].search("col-" + type) >= 0) {
                result = cls[i];
                break;
            }
        }
        let temp = result.replace("col-" + type + "-", "");
        $(obj).find(".pull-right .btn-group .btn span:first-child").attr("class", "width-val deo-w-" + temp);
        let group = $(obj).find("ul.dropdown-menu-right");
        $(group).find("li").removeClass("selected");
        $(group).find(".col-" + temp).addClass("selected");
    }
});

