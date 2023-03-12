/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

function SetButonSaveToHeader() {
    var html_save_and_stay = 
    '<li>' +
        '<a id="page-header-desc-deotemplate_shortcode-SaveAndStay" class="toolbar_btn  pointer" href="javascript:void(0);" title="Save and stay" onclick="TopSaveAndStay()">' +
            '<i class="process-icon-save"></i>' +
            '<div>Save and stay</div>' +
        '</a>' +
    '</li>';
    $('.toolbarBox .btn-toolbar ul').prepend(html_save_and_stay);
    
}

function TopSave(){
    if (typeof TopSave_Name !== 'undefined') {
        $("button[name$='"+TopSave_Name+"']").click();
    }
}

function TopSaveAndStay(){
    if (typeof TopSaveAndStay_Name !== 'undefined') {
        $("button[name$='"+TopSaveAndStay_Name+"']").click();
    }
}

function ToolbarSave(classBtn){
    if (typeof classBtn !== 'undefined') {
        $("button."+classBtn).click();
    }
}

function ToolbarSaveAndStay(classBtn){
    if (typeof classBtn !== 'undefined') {
        $("button."+classBtn).click();
    }
}

function calculate_rate_image(width,height){
    return Math.round(((height/width)*100)*10)/10;
}

function toogle_switch(element,group,duration,reverse = false){
    if (!element.length && !group.length) return false;
    element.change(function(){
        if (reverse){
            if ($(this).val() == 0){
                group.removeClass('hide-config').show(duration);
            }else{                   
                group.hide(duration);
            }
        }else{
            if ($(this).val() == 1){
                group.removeClass('hide-config').show(duration);
            }else{                   
                group.hide(duration);
            }
        }
    });
    if (element.is(':radio')){
        $(element.selector+':checked').trigger('change');
    }else{
        $(element).trigger('change');
    }
}

function toogle_select(element, group, value_show, duration){
    element.change(function(){
        if($(this).val() == value_show){
            group.removeClass('hide-config').show(duration);
        }else{
            group.hide(duration);
        }
    });
    element.trigger('change');
}

/**
 * review  $('.nav-bar').on('click', '.menu-collapse', function() {
 */
function miniLeftMenu(parameters) {
    if( !$('body').hasClass('page-sidebar-closed')){
        $('body').toggleClass('page-sidebar-closed');
        if ($('body').hasClass('page-sidebar-closed')) {
            $('nav.nav-bar ul.main-menu > li')
            .removeClass('ul-open open')
            .find('a > i.material-icons.sub-tabs-arrow').text('keyboard_arrow_down');
        }
    }
}

function replaceSpecialString(str){
    return str.replace(/\t/g, "_APTAB_").replace(/\r/g, "_APNEWLINE_").replace(/\n/g, "_APENTER_").replace(/"/g, "_APQUOT_").replace(/'/g, "_APAPOST_");
}

// Masony
$(document).ready(function() {
    let DeoMasonry = {
        init : function (options, el) {
            let base = this;
            base.$elem = $(el);
            base.options = $.extend({}, $.fn.DeoMasonry.options, options);

            $(window).resize(function() {
                base.createGrid(base.$elem);
            });
            base.createGrid(base.$elem);
        },
        createGrid: function(grid){
            let base = this;

            if (grid.css('display') != 'grid')
                grid.css('display','grid');

            if (base.options.minWidth)
                grid.css('grid-template-columns','repeat(auto-fill, minmax('+base.options.minWidth+',1fr))');

            let rowHeight = grid.css('grid-auto-rows');
            if (rowHeight == "auto"){
                rowHeight = base.options.grid_auto_rows;
                grid.css('grid-auto-rows', base.options.grid_auto_rows+'px');
            }else{
                rowHeight = parseInt(base.options.grid_auto_rows);
            }

            let rowGap = grid.css('grid-gap');
            if (rowGap == "normal normal"){
                rowGap = base.options.grid_row_gap;
                grid.css('grid-row-gap', base.options.grid_row_gap+'px');
                grid.css('grid-column-gap', base.options.grid_column_gap+'px');
            }else{
                rowGap = parseInt(base.options.grid_row_gap);
            }

            let child = (base.options.classChild) ? grid.children(base.options.classChild) : grid.children();
            child.each(function(){
                let height = (base.options.classContent) ? $(this).children(base.options.classContent).outerHeight() : $(this).children().outerHeight();
                let rowSpan = Math.ceil((height+rowGap)/(rowHeight+rowGap));
                $(this).css({
                    "grid-row-end" : "span "+rowSpan,
                    "width" : "auto",
                });

            });
        }
    };

    $.fn.DeoMasonry = function (options) {
        return this.each(function () {
            DeoMasonry.init(options, this);
        });
    };

    $.fn.DeoMasonry.options = {
        classChild : false,
        classContent: false,
        minWidth: false,
        grid_auto_rows: 20,
        grid_row_gap: 0,
        grid_column_gap: 0,
    };
});
