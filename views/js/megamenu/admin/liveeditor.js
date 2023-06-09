/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
(function($, window, document) {
	$.fn.DeoMegamenuEditor = function(opts) {
		// default configuration
		let config = $.extend({}, {
			lang:null,
			opt1: null,
			action:null,
			action_save:null,
			id_shop:null,
			JSON:null,
			param_widgets_menu: new Array(),
			list_option_widgets : new Array(),
			array_position : new Array(),
		}, opts);

		let add_group_menu = false;
		let liveeditor = $('#live-editor').offset();

		/**
		 * active menu 
		 */
		let new_menu = '<li class="nav-item add-new" data-id="0"><span class="add-menu"><i class="icon-plus"></i>New</span>';
		let btn_menu = '<span class="add-menu"></span><span class="edit-menu"></span><span class="delete-menu"></span>';
		let btn_add_row = '<a href="javascript:void(0)" title="Add row" class="add-row">Add row</a>';
		let btn_rows = '<span class="btn-action-row"><a href="javascript:void(0)" title="Add column" class="add-col">Add column</a><a href="javascript:void(0)" title="Remove row" class="remove-row">Remove row</a></span>';
		let btn_widgets = '<div class="btn-action-widget"><a href="javascript:void(0)" title="Delete widget" class="w-delete"><i class="icon"></i></a><a href="javascript:void(0)" title="Edit widget" class="w-edit"><i class="icon"></i></a><div class="w-name"><a href="javascript:void(0)" title="Change widget" class="icon"></a></div></div>';
		let btn_cols = '<div class="btn-action-col"><a href="javascript:void(0)" title="Add new widget" class="add-widget-col"></a><a href="javascript:void(0)" title="Remove column" class="remove-col"></a><a href="javascript:void(0)" title="Edit column" class="setting-col"></a></div>';
		let select_widgets = '<select name="inject_widget_name" class="inject_widget_name"></select>';
		let row_empty = '<div class="dropdown-widget dropdown-menu"><div class="dropdown-menu-inner"><div class="row active empty">'+btn_rows+'</div></div>'+btn_add_row+'</div>';
		let submenu_empty = '<div class="dropdown-menu"><div class="dropdown-menu-inner"><div class="row"><div class="col-sp-12 mega-col" data-colwidth="12" data-type="menu"><div class="inner"><ul></ul></div></div></div></div></div>';
		let btn_close_modal = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
		let panel_heading = '<div class="panel-heading"></div>';
		let activeMenu = null;


		/**
		 * action menu form handler
		 */	
		function actionMenuForm(){
			/**
			 * change subwith
			 */
			$(".menu_subwith","#menu-form").change(function (){
				if (activeMenu){
					let id = activeMenu.data('id');
					let align = (typeof activeMenu.data('align') != 'undefined') ? activeMenu.data('align') : 'aligned-fullwidth';
					let subwidth = activeMenu.data('subwidth');;
					let subwith = $(this).val();
					let subwith_old = activeMenu.data('subwith');
					let submenu = activeMenu.children('.dropdown-menu');
					let allow = false;

					if (subwith != 'none'){
						if (subwith_old == 'none'){
							if (submenu.length == 0){
								// load widget or submenu
								allow = true;
							}else if (submenu.length == 1){
								// has submenu or widget
								if ((subwith == 'widget' && (activeMenu.children('.dropdown-widget').length == 0)) || (subwith == 'submenu' && (activeMenu.children('.level1').length == 0))){
									allow = true;
								}
							}
						}else if ((subwith == 'widget' && (activeMenu.children('.dropdown-widget').length == 0)) || (subwith == 'submenu' && (activeMenu.children('.level1').length == 0))){
							allow = true;
						}
					}

					if (allow){
						let btn = $(".apply","#menu-form");
						btn.addClass('loading');
						$.ajax({
							async: false,
							url: config.action_gensubmenu+'&id='+id+'&subwith='+subwith,
							type:'POST',
						}).done(function(jsonData) {
							jsonData = jQuery.parseJSON(jsonData);
							if (jsonData.success){
								// has submenu or widget
								let new_submenu = $(jsonData.data);
								if (subwith == 'widget'){
									new_submenu = initWidget(new_submenu);

									function initWidget(widget){
										widget.append(btn_add_row);
										widget.find('.row').each( function() {
											$(this).append(btn_rows);
										});

										// init select
										let select_change_widget = $(select_widgets);
										initSelect(select_change_widget);

										let ajaxCols = new Array();
										widget.find(".mega-col").each( function() {
											if ($(this).data( 'widgets') && $(this).data("type") != "menu"){  
												ajaxCols.push($(this));
											}		
										});

										// init sub menu for widget
										$.each(ajaxCols, function (i, col) {
											let widgets = col.data('widgets').toString().split('|');
											$.each(widgets, function (i, widget) {
												if (typeof config.param_widgets_menu[widget] != 'undefined') {
													let widget_element = $(config.param_widgets_menu[widget].html);
													widget_element.append($(btn_widgets).clone(1));
													widget_element.find('.w-name').append(select_change_widget.clone(1).val(widget_element.data('id_widget')));
													col.children('.mega-col-inner').append(widget_element);
												}else{
													col.children('.mega-col-inner').append('<div class="deo-widget empty" data-id_widget="'+ widget +'">Widget not exist'+btn_widgets+'</div>');
												}
											});

											col.append(btn_cols);
											$("a", col).not(".nav-tab-link").attr('href','javascript:void(0)');
										});

										return widget;
									}
								}else if (subwith == 'submenu'){
									new_submenu.addClass('dropdown-mega-menu');
									new_submenu.find('.nav-link').append(btn_menu); 
								}
								activeMenu.append(new_submenu);
							}else{
								if (subwith == 'widget' && (activeMenu.children('.dropdown-widget').length == 0)){
									// init empty widget
									activeMenu.append($(row_empty));
								}
							}
							
							btn.removeClass('loading');
						});
					}


					if (subwith == 'widget'){
						activeMenu.children('.dropdown-menu.dropdown-widget').removeClass('hide');
						activeMenu.children('.dropdown-menu.dropdown-mega-menu').addClass('hide');
						activeMenu.addClass('enable-widget').removeClass('active-submenu enable-submenu none');
					}else if (subwith == 'submenu'){
						activeMenu.children('.dropdown-menu.dropdown-widget').addClass('hide');
						activeMenu.children('.dropdown-menu.dropdown-mega-menu').removeClass('hide');
						activeMenu.addClass('active-submenu enable-submenu').removeClass('enable-widget none');
					}else if (subwith == 'none'){
						activeMenu.children('.dropdown-menu').addClass('hide');
						activeMenu.addClass('none').removeClass('active-submenu enable-submenu enable-widget');
					}

					if (subwith == 'widget'){
						if (!$(activeMenu).parent().hasClass('vertical')){
							$('#menu-form .aligned-submenu').show();
							$('.button-alignments button[data-option="'+align+'"]').trigger('click');
						}else{
							$('#menu-form .width_submenu').hide();
						}

						$('#menu-form .width_submenu').show();
						$('#menu-form .menu_subwidth').trigger('keyup');
					}else{
						$('#menu-form .aligned-submenu').hide();
						$('#menu-form .width_submenu').hide();
						$(".width_submenu").val('');
						$(".button-alignments button").removeClass("active");
						activeMenu.attr('class',activeMenu.attr("class").replace(/aligned-\w+/g,''));
					}
					
					processSettingSubMenu(activeMenu);
					$(".button-submit .apply","#menu-form").trigger('click');
				}
			});


			/**
			 * change width
			 */	
			$(".menu_subwidth","#menu-form").keyup(function(){
				if (activeMenu){
					let width = $(this).val();
					if (width){
						let myRe = /^\d*(\.|,){0,1}\d*$/;
						if (myRe.test(width)){
							width = parseInt(width);
							$(".dropdown-widget", activeMenu).outerWidth(width);
							$(".button-submit .apply","#menu-form").trigger('click');
						}else{
							showErrorMessage('The value is not valid. Value must be a number!');
						}
					}
				}
			});


			/**
			 * button align
			 */	
			$(".button-alignments button").click( function(){
				if (activeMenu && !$(activeMenu).parent().hasClass('vertical')){
					$(".button-alignments button").removeClass("active");
					$(this).addClass('active');

					let align = $(this).data('option');
					let dropdown = activeMenu.find('.dropdown-widget').first();
					if (align == 'aligned-fullwidth'){
						$('#menu-form').find('.width_submenu').hide();
						dropdown.css('width','');
					}else{
						$('#menu-form').find('.width_submenu').show();
						if ($('.menu_subwidth').val()){
							dropdown.css('width',$('.menu_subwidth').val());
						}
					}

					if ((/aligned-\w+/g).test($(activeMenu).attr("class"))){
						$(activeMenu).attr('class',$(activeMenu).attr("class").replace(/aligned-\w+/g,align));
					}else{
						$(activeMenu).addClass(align);
					}

					processSettingSubMenu(activeMenu);
					$(".button-submit .apply","#menu-form").trigger('click');
				}
			});


			/**
			 * apply sub megamenu menu
			 */
			$("#menu-form .apply").click(function(){
				let btn = $(this);
				btn.addClass('loading');

				let id = $(activeMenu).data('id');
				let subwith = $("#menu-form .menu_subwith").val();
				let align = $("#menu-form .button-alignments .btn.active").data('option');
				let menu_subwidth = $("#menu-form .menu_subwidth").val();

				$(activeMenu).addClass('parent dropdown');
				$(activeMenu).data('subwith',subwith);
				$(activeMenu).data('align',align);

				if (menu_subwidth){
					$(activeMenu).data('subwidth',menu_subwidth);
				}else{
					$(activeMenu).removeData('subwidth');
				}

				setTimeout(function(){
					btn.removeClass('loading');
					// showSuccessMessage(config.message_apply);
				}, 200);

			});
		}


		/**
		 * listen Events to operator Elements of MegaMenu such as link, colum, row and Process Events of buttons of setting forms.
		 */	
		function listenEvents($megamenu){
			$('.form-setting').hide();
			$($megamenu).delegate('a.nav-link', 'click', function(e){
				e.stopPropagation();
				let parent = $(this).parent();
				if (parent.hasClass('open-sub')){
					return false;
				}
				parent.closest('ul').children('li').each(function(index){
					$(this).removeClass('open-sub');

					$(this).attr('class',$(this).attr("class").replace(/aligned-\w+/g,'').replace(/\s{2,}/g,' '));
					if ($(this).data('align')){
						$(this).addClass($(this).data('align'));
					}

					if ($(this).data('subwith')){
						let subwith = $(this).data('subwith');
						if (subwith == 'widget'){
							$(this).addClass('enable-widget').removeClass('active-submenu enable-submenu none');
						}else if (subwith == 'submenu'){
							$(this).addClass('active-submenu enable-submenu').removeClass('enable-widget none');
						}else if (subwith == 'none'){
							$(this).addClass('none').removeClass('active-submenu enable-submenu enable-widget');
						}
					}
				});
				parent.addClass('open-sub');
				if (parent.parent().hasClass('megamenu')){
					activeMenu = parent;
					removeRowActive();
					removeColumnActive();

					$("#menu-form form")[0].reset();

					let subwidth = parent.data('subwidth');
					if (subwidth){
						$('#menu-form .menu_subwidth').val(subwidth);
					}else{
						$('#menu-form .menu_subwidth').val('');
					}

					let align = parent.data('align');
					$('#menu-form .button-alignments button').removeClass('active');
					if (align){
						$('#menu-form .button-alignments button[data-option="'+align+'"]').addClass('active');
					}else{
						$('#menu-form .button-alignments button[data-option="aligned-left"]').addClass('active');
					}

					let subwith = parent.data('subwith');
					$('#menu-form .menu_subwith').val(subwith).trigger('change');
					
					processSettingSubMenu(parent);
					$(".form-setting").hide();
					$("#menu-form").show();
				}

				return false;  
			});
		

			/**
			 * Row action Events Handler
			 */
			$($megamenu).delegate('.add-row', 'click', function(e){ 
				let row = $('<div class="row">'+btn_rows+'<div>').addClass('active empty');
				let dropdown_sub = $(this).closest('.dropdown-widget').children('.dropdown-menu-inner');
				dropdown_sub.children(".row").removeClass('active');
				dropdown_sub.append(row);
			});

			$($megamenu).delegate('.remove-row', 'click', function(e){ 
				// if( activeMenu ){
					let row = $(this).closest('.row');
					let confirm_message = confirm("Do you want to delete this row?");
					if (confirm_message == true) {
						row.remove();
					}
					removeRowActive();	
				// }
				
			});


			/**
			 * add class active when click row
			 */
			$($megamenu).delegate('.row', 'click', function(e){ 
				$(".row",$megamenu).removeClass('active');
				$(this).addClass('active');  
				e.stopPropagation();
			}); 


			/**
			* init column
			*/ 
			$($megamenu).delegate('.add-col', 'click', function(e){ 
				// if (activeMenu){ 
					let row = $(this).closest('.row');
					let num = 6;
					let col = $('<div class="mega-col col-xxl-'+ num +' col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 col-sp-12 empty"><div class="mega-col-inner"></div>'+ btn_cols +'</div>');
					col.data('xxl',num).data('xl','12').data('lg','12').data('md','12').data('sm','12').data('xs','12').data('sp','12');
					row.append(col).removeClass('empty');

					let cols = $(".dropdown-menu .mega-col", activeMenu).length; 
					$(activeMenu).data('cols', cols);
				// }
			});	


			/**
			 * remove column
			 */ 
			$($megamenu).delegate('.remove-col', 'click', function(e){
				$(this).addClass('loading');
				let row = $(this).closest('.row');
				let mega_col = $(this).closest(".mega-col");
				if( mega_col.data('type') == 'menu' ) {
					alert('You could not remove this column having menu item(s)');
					return true;
				}else {
					let confirm_message = confirm("Do you want to delete this column?");
					if (confirm_message == true) {
						mega_col.remove();
						if (row.find('.mega-col').length){
							row.removeClass('empty');
						}else{
							row.addClass('empty');
						}
					}
				}
				$(this).removeClass('loading');
				removeColumnActive();
			});


			/**
			 * setting col
			 */ 
			var oldClass = '';
			$($megamenu).delegate('.mega-col .setting-col', 'click', function(e){
				e.stopPropagation();
				$(this).addClass('loading');
				let mega_col = $(this).closest('.mega-col');
				$('#column-form').find('form').trigger("reset");

				oldClass = mega_col.data('colclass');
				if (mega_col.data('widgets')){
					let keywidget =  mega_col.data('widgets');
					if (keywidget){
						$(".inject_widget option").each(function(){
							var value = $(this).val();
							if (value && value == keywidget)
								$(this).attr('selected', 'selected');	
						});
					}
				}

				mega_col.addClass('active');
				mega_col.parent().addClass('active');

				$('#column-form').modal('show');

				$.each(mega_col.data(), function( i, val ){
					$('[name='+i+']','#column-form').val( val );
				});
				$(this).removeClass('loading');
			});


			/**
			 * show form choose widget to add to col
			 */ 
			$($megamenu).delegate('.mega-col .add-widget-col', 'click', function(e){
				e.stopPropagation();
				$(this).addClass('loading');
				$(this).closest('.mega-col').addClass('active');
				$(this).closest('.row').addClass('active');
				$('#widget-setting').find('.key_widget').prop('selectedIndex',0);
				$('#widget-setting').modal('show');
				$(this).removeClass('loading');
			});


			/**
			 * add new widgets
			 */
			$("#btn-inject-widget").click(function(){
				let wid = $('.key_widget').val();	
				if (wid != ''){
					let col = $(".mega-col.active", activeMenu);
					let widgets = $(col).data('widgets');
					let listWidgets = (widgets) ? widgets.toString().split('|') : [];

					if(listWidgets.indexOf(wid) == -1) { 
						listWidgets.push(wid);
					}else{
						alert('Widget has exist!');
						return;
					}

					updateCol(col,listWidgets);
				}else {
					alert( 'Please select a widget to insert' );
					return;
				}
			});


			/**
			 * Column Form Action Event Handler
			 */
			$('#column-form .save').click(function(e){
				// col width
				$('select', '#column-form').each(function(){
					if( activeMenu ) {
						let col = $( ".mega-col.active", activeMenu );
						if( $(this).hasClass('colwidth') ){
							let classCol = $(col).attr('class');
							$(col).attr('class',classCol.replace(new RegExp('col-' + $(this).attr('name') + '-(\\d(\.)?\\d?)','gm'),'col-'+ $(this).attr('name') + '-' + $(this).val() + ' '));
							$(col).attr('data-'+$(this).attr('name'), $(this).val());
							// $(col).attr('data-colwidth', $(this).val());
						}
						$(col).data( $(this).attr('name') ,$(this).val() );

					}	
				});

				//class CSS
				if( activeMenu ) {
					let oldText = $('input', '#column-form').val();
					let col = $( ".mega-col.active", activeMenu );
					$(col).data($('input', '#column-form').attr('name') ,$('input', '#column-form').val());
					$(col).removeClass(oldClass).addClass(oldText);
					oldClass = oldText;
				}

				removeRowActive();
				$('#column-form').modal('hide');
			});

			$('#column-form').on('hide.bs.modal', function(){
				removeRowActive();
				removeColumnActive();
			});

			$('#widget-setting').on('hide.bs.modal', function(){
				if (!$('#widget-setting').hasClass('show-form-additional')){
					removeRowActive();
					removeColumnActive();
				}
			});

			$('#list-widgets').on('hide.bs.modal', function(){
				if (!$('#widget-setting').hasClass('show-form-additional')){
					removeRowActive();
					removeColumnActive();
					removeWidgetActive();
				}
			});

			$('#form-widget').on('hide.bs.modal', function(){
				removeRowActive();
				removeColumnActive();
				removeWidgetActive();
				$('#form-widget').find('.modal-content').empty();
				$('#widget-setting').removeClass('show-form-additional');

				// remove MCE from windown
				$('#form-widget textarea.autoload_rte').each((index, textarea) => {
				    if (window.tinyMCE) {
						const editor = window.tinyMCE.get(textarea.id);

						if (editor) {
							// Reset content to force refresh of editor
							tinyMCE.execCommand('mceRemoveEditor', false, textarea.id);
						}
				    }
				});
			});

			$('#form-submenu').on('hide.bs.modal', function(){
				removeRowActive();
				removeColumnActive();
				removeWidgetActive();
				$('#form-submenu').find('.modal-content').empty();

				$('#form-submenu textarea.autoload_rte').each((index, textarea) => {
				    if (window.tinyMCE) {
						const editor = window.tinyMCE.get(textarea.id);

						if (editor) {
							// Reset content to force refresh of editor
							tinyMCE.execCommand('mceRemoveEditor', false, textarea.id);
						}
				    }
				});
			});

			$("#form-submenu,#form-widget,#list-widgets,#widget-setting").modal({
				show: false,
				backdrop: 'static'
			});


			$(".form-setting").each( function(){
				var $p = $(this);
				$(".popover-title span",this).click( function(){
					if( $p.attr('id') == 'menu-form' ){
						removeMenuActive();
					}else if( $p.attr('id') == 'column-form' ){
						removeColumnActive();
					}else {
						$('#widget-setting').hide();
					}
				} );
			} );

			$("#menu-form").draggable();

			/**
			 * create new widget
			 */
			$("#btn-create-widget").click( function(){
				$('#widget-setting').addClass('show-form-additional');
				$('#widget-setting').modal('hide');
				$('#list-widgets').modal('show');
			});


			/**
			 * Remove class form widget setting form widget,form setting when finish add or close modal
			 */
			$("#list-widgets .close-btn, #widget-setting #btn-close-widget").click( function(){
				removeRowActive();
				removeColumnActive();
				removeWidgetActive();
				$('#widget-setting').removeClass('show-form-additional');
			});

			
			/**
			 * unset mega menu setting
			 */
			$("#unset-data-menu").click( function(){
				if( confirm('Are you sure to reset megamenu configuration') ){
					$.ajax({
						url: config.action,
						data: 'doreset=1&id_shop='+config.id_shop,
						type:'POST',
					}).done(function( data ) {
						location.reload();
					});
				}
				return false;			 	
			});


			/**
			 * delete widget
			 */
			$($megamenu).on('click', '.w-delete', function(e) {
				$(this).addClass('loading');
				if (confirm('Do you want to delete this widget?')){
					//$(".row",$megamenu).removeClass('active');
					// $(this).addClass('active'); 
					let widget = $(this).closest('.deo-widget');
					let col = $(this).closest('.mega-col'); 
					let listWidget = col.data('widgets').toString().split('|');
					let id_widget = widget.data('id_widget').toString();
					let index = listWidget.indexOf(id_widget);

					if(index >= 0){
						listWidget.splice(index, 1);
						// col.data('widgets',listWidget.join('|'));
						// widget.remove();
						updateCol(col,listWidget);
					}
				}
				$(this).removeClass('loading');
				return false;
			});


			/**
			 * change widget
			 */
			$($megamenu).on('change', '.deo-widget .inject_widget_name', function(e) {
				let btn = $(this).closest('.w-name');
				btn.addClass('loading');
				let widget = $(this).closest('.deo-widget');
				let col = $(this).closest('.mega-col'); 
				let listWidget = col.data('widgets').toString().split('|');
				let id_widget = widget.data('id_widget').toString();
				let new_widget = $(this).val();
				let index = listWidget.indexOf(id_widget);
				if (index != -1) {
					listWidget[index] = new_widget;
					updateCol(col,listWidget);
					btn.removeClass('loading');
				}
			});


			/**
			 * create delete submenu
			 */
			$($megamenu).on('click', '.delete-menu', function(e) {
				e.preventDefault();
				let confirm_message = confirm("Do you want to delete this menu?");
				if (confirm_message == false) {
					return false;
				}
				let btn = $(this);
				let li = $(this).closest('.nav-item');
				let id_deomegamenu = li.data('id');
				btn.addClass('loading');
				$("#deo_loading").show();

				$.ajax({
					url: config.action_submenu+'&deletemenu&id_deomegamenu='+id_deomegamenu,
					type:'POST',
				}).done(function(jsonData) {
					jsonData = jQuery.parseJSON(jsonData);
					if (jsonData.success){
						let num_li = li.closest('ul').children('li').length;
						// console.log(num_li);
						if (num_li > 1){
							li.remove();
						}else{
							let li_parent = $('.nav-item[data-id="'+li.data('id_parent')+'"]',$megamenu);
							li_parent.find('.caret').first().remove();
							li_parent.children('a').removeClass('has-category');
							li_parent.children('.dropdown-menu.dropdown-mega-menu').remove();
							let classParent = 'parent dropdown';
							if (li_parent.data('id_parent') != 0){
								classParent = 'parent dropdown-submenu';
							}
							li_parent.removeClass(classParent);
						}
						showSuccessMessage(jsonData.msg);
					}else{
						showErrorMessage(jsonData.msg);
					}
					btn.removeClass('loading');
					$("#deo_loading").hide();
				});
			});


			/**
			 * create form edit submenu
			 */
			$($megamenu).on('click', '.edit-menu', function(e) {
				e.preventDefault();
				let btn = $(this);
				btn.addClass('loading');
				$("#deo_loading").show();
				let id_deomegamenu = $(this).closest('.nav-item').data('id');

				$.ajax({
					url: config.action_submenu+'&id_deomegamenu='+id_deomegamenu,
					type:'POST',
				}).done(function(jsonData) {
					jsonData = jQuery.parseJSON(jsonData);
					let modal_content = $(jsonData);
					if (modal_content.find('.panel-heading').length){
						modal_content.find('.panel-heading').append(btn_close_modal);
					}else{
						modal_content.find('.panel').prepend(panel_heading);
						modal_content.find('.panel-heading').append(btn_close_modal);
					}

					modal_content.find('#configuration_form_cancel_btn').removeAttr('onclick').attr('data-dismiss','modal');
					$('#form-submenu').find('.modal-content').append(modal_content);
					$("#form-submenu").DeoMegaMenuList();
					$('#form-submenu .dropdown-toggle').dropdown();
					btn.removeClass('loading');

					$('#form-submenu').modal('show');
					$("#deo_loading").hide();
					$(".image-choose").DeoImageSelector();
				});
			});


			/**
			 * create form add submenu
			 */
			$($megamenu).on('click', '.add-menu', function(e) {
				e.preventDefault();
				let btn = $(this);
				btn.addClass('loading');
				$("#deo_loading").show();
				let id_parent = $(this).closest('.nav-item').data('id');
				
				$.ajax({
					url: config.action_submenu,
					type:'POST',
				}).done(function(jsonData) {
					jsonData = jQuery.parseJSON(jsonData);
					let modal_content = $(jsonData);
					if (modal_content.find('.panel-heading').length){
						modal_content.find('.panel-heading').append(btn_close_modal);
					}else{
						modal_content.find('.panel').prepend(panel_heading);
						modal_content.find('.panel-heading').append(btn_close_modal);
					}
					modal_content.find('#configuration_form_cancel_btn').removeAttr('onclick').attr('data-dismiss','modal');
					modal_content.find('#id_parent').val(id_parent);
					$('#form-submenu').find('.modal-content').append(modal_content);
					$("#form-submenu").DeoMegaMenuList();
					$('#form-submenu .dropdown-toggle').dropdown();
					btn.removeClass('loading');
					$('#form-submenu').modal('show');
					$("#deo_loading").hide();
					$(".image-choose").DeoImageSelector();
				});
			});


			/**
			 * submit submenu
			 */
			$('#form-submenu #configuration_form').live('submit', function(e) {
				e.preventDefault();

				let btn = $(this).find('.save');
				btn.addClass('loading');
				let data = $(this).serializeArray();
				let url = $(this).attr('action');
				let form_edit = true;

				if (!$(this).find('#id_deomegamenu').val()){
					form_edit = false;
				}

				$.ajax({
					url: url,
					type: 'POST',
					data: data,
				}).done(function(jsonData) {
					jsonData = jQuery.parseJSON(jsonData);
					if (jsonData.success){

						if (form_edit){
							let id_deomegamenu = jsonData.data.id_deomegamenu;
							let li = $('.nav-item[data-id="'+id_deomegamenu+'"]',$megamenu);
							let a = li.children('.nav-link');
							let content_menu = a.children('.content-menu');
							let oldClass = li.data('menu_class');


							// menu_class
							li.data('menu_class',jsonData.data.menu_class);
							li.removeClass(oldClass).addClass(jsonData.data.menu_class);

							// active
							if (parseInt(jsonData.data.active) == 1){
								li.data('active', jsonData.data.active);
								li.removeClass('menu-disable');
							}else{
								li.data('active', jsonData.data.active);
								li.addClass('menu-disable');
							}

							// menu type
							li.data('menu-type',jsonData.data.type);

							// title
							content_menu.find('.menu-title').html(jsonData.data.title);

							// show_title
							li.data('show-title', jsonData.data.show_title);
							if (jsonData.data.show_title == 1){
								content_menu.addClass('show-title');
								content_menu.removeClass('hide-title');
							}else{
								content_menu.addClass('hide-title');
								content_menu.removeClass('show-title');
							}

							// sub title
							content_menu.find('.sub-title').html(jsonData.data.text);
							if (jsonData.data.text){
								content_menu.addClass('show-sub-title');
								content_menu.removeClass('hide-sub-title');
							}else{
								content_menu.addClass('hide-sub-title');
								content_menu.removeClass('show-sub-title');
							}

							// icon-image + icon_class
							if (jsonData.data.icon_class || jsonData.data.image != '#'){
								content_menu.addClass('has-icon');
							}else{
								content_menu.removeClass('has-icon');
							}

							// icon-image
							li.data('image', jsonData.data.image);
							content_menu.find('.menu-icon-image').attr('src',jsonData.data.image);
							if (jsonData.data.image != '#'){
								content_menu.addClass('icon-img');
							}else{
								content_menu.removeClass('icon-img');
							}

							// icon-class
							li.data('icon-class', jsonData.data.icon_class);
							content_menu.find('.menu-icon-class').empty();
							content_menu.find('.menu-icon-class').append($(jsonData.data.icon_class));
							if (jsonData.data.icon_class){
								content_menu.addClass('icon-class');
							}else{
								content_menu.removeClass('icon-class');
							}

						}else{
							//add new to root
							if (jsonData.data.id_parent == 0){
								let li = $(createSubmenu(jsonData.data));
								li.addClass('enable-submenu active-submenu');
								li.insertBefore($('#mainmenutop .navbar-nav.megamenu > .add-new'));
								$('#form-submenu').modal('hide');
								showSuccessMessage(jsonData.msg);
								$('#mainmenutop .navbar-nav.megamenu > .add-new > .add-menu').removeClass('loading');

								return true;
							}

							let id_deomegamenu = jsonData.data.id_parent;
							let li = $('.nav-item[data-id="'+id_deomegamenu+'"]',$megamenu);
							let a = li.children('.nav-link');
							let dropdown_menu = li.children('.dropdown-menu.dropdown-mega-menu');

							if (dropdown_menu.length){
								let ul = dropdown_menu.find('ul').first();
								ul.append($(createSubmenu(jsonData.data)));
							}else{
								let dropdown_menu_parent = li.closest('.dropdown-menu.dropdown-mega-menu').first();
								let level = 0;
								let classParent = 'parent dropdown enable-submenu active-submenu';
								if (li.data('id_parent') != 0){
									level = (/level\w+/g).exec(dropdown_menu_parent.attr('class'))[0].replace('level','');
									classParent = 'parent dropdown-submenu enable-submenu active-submenu';
								}
								let new_menu = $(submenu_empty);
								new_menu.addClass('level'+(parseInt(level) + 1));
								if (li.hasClass('enable-submenu')){
									new_menu.addClass('dropdown-mega-menu');
									new_menu.removeClass('dropdown-widget');
								}else if(li.hasClass('enable-widget')){
									new_menu.addClass('dropdown-widget');
									new_menu.removeClass('dropdown-mega-menu');
								}
								let ul = new_menu.find('ul').first();
								ul.append($(createSubmenu(jsonData.data)));
								li.addClass(classParent);
								li.append(new_menu);
								a.addClass('has-category');
								if (li.children('.caret').length == 0){
									li.append('<b class="caret"></b>');
								}
							}
						}

						$('#form-submenu').modal('hide');
						showSuccessMessage(jsonData.msg);
					}else{
						showErrorMessage(jsonData.msg);
					}
					btn.removeClass('loading');
				});
			});

			function createSubmenu(data){
				let class_menu, html = '';

				class_menu += (data.icon_class || data.image != '#') ? ' has-icon' : '';
				class_menu += (data.icon_class) ? ' icon-class' : '';
				class_menu += (data.image != '#') ? ' icon-img' : '';
				class_menu += (data.show_title == 1) ? ' show-title' : ' hide-title';
				class_menu += (data.text) ? ' show-sub-title' : ' hide-sub-title';

				html += '<li class="nav-item '+data.menu_class+'" data-id="'+data.id+'" data-menu-type="'+data.type+'" data-subwith="'+data.sub_with+'" data-id_parent="'+data.id_parent+'" data-menu_class="'+data.menu_class+'" data-active="'+data.active+'" data-icon-image="'+data.image+'" data-show-title="'+data.show_title+'" data-sub-title="'+data.text+'" data-position="'+data.position+'">';
					html += '<a class="nav-link" href="javascript:void(0)">';
						html += '<span class="content-menu '+class_menu+'">';
							html += '<span class="icons">';
								html += '<i class="menu-icon-class '+data.icon_class+'"></i>';
								html += '<img class="menu-icon-image" src="'+data.image+'"/>';
							html += '</span>';
							html += '<span class="title">';
								html += '<span class="menu-title">'+data.title+'</span>';
								html += '<span class="sub-title">'+data.text+'</span>';
							html += '</span>';
						html += '</span>';
						html += btn_menu;
					html += '</a>';
				html += '</li>';

				return html;
			}


			/**
			 * create content new widget
			 */
			// $(document).on('click', '#choose-list-widgets .widget-type', function(e) {
			$("#choose-list-widgets .widget-type").click( function(){
				let btn = $(this);
				btn.addClass('loading');
				$("#deo_loading").show();
				let type_widget = $(this).data('widget_type');
				$.ajax({
					url: config.base_url_widget+'&load_form_widget&addbtmegamenu_widgets&wtype='+type_widget,
					type:'POST',
				}).done(function(jsonData) {
					jsonData = jQuery.parseJSON(jsonData);
					let modal_content = $(jsonData);
					if (modal_content.find('.panel-heading').length){
						modal_content.find('.panel-heading').append(btn_close_modal);
					}else{
						modal_content.find('.panel').prepend(panel_heading);
						modal_content.find('.panel-heading').append(btn_close_modal);
					}
					modal_content.find('#configuration_form_cancel_btn').removeAttr('onclick').attr('data-dismiss','modal');
					$('#form-widget').find('.modal-content').append(modal_content);
					$('#form-widget').find('form').validate();
					initWidgetForm();
					$('#form-widget .dropdown-toggle').dropdown();
					btn.removeClass('loading');
					$('#list-widgets').modal('hide');
					$('#form-widget').modal('show');
					$("#deo_loading").hide();
				});
			});


			/**
			 * edit content widget
			 */
			$($megamenu).delegate('.w-edit', 'click', function(){ 
				let btn = $(this);
				btn.addClass('loading');
				$("#deo_loading").show();
				let widget = $(this).closest('.deo-widget');
				let col = $(this).closest('.mega-col');
				let id_widget = widget.data('id_widget');
				let id_deomegamenu_widgets = config.param_widgets_menu[id_widget].id_deomegamenu_widgets;
				col.addClass('active');
				widget.addClass('active');
				$.ajax({
					url: config.base_url_widget+'&load_form_widget&updatebtmegamenu_widgets&id_deomegamenu_widgets='+id_deomegamenu_widgets,
					type:'POST',
				}).done(function( jsonData ) {
					jsonData = jQuery.parseJSON(jsonData);
					let modal_content = $(jsonData);
					if (modal_content.find('.panel-heading').length){
						modal_content.find('.panel-heading').append(btn_close_modal);
					}else{
						modal_content.find('.panel').prepend(panel_heading);
						modal_content.find('.panel-heading').append(btn_close_modal);
					}
					modal_content.find('#configuration_form_cancel_btn').removeAttr('onclick').attr('data-dismiss','modal');
					$('#form-widget').find('.modal-content').append(modal_content);
					$('#form-widget').find('form').validate();
					initWidgetForm();
					$('#form-widget .dropdown-toggle').dropdown();
					$('#form-widget').modal('show');
					btn.removeClass('loading');
					$("#deo_loading").hide();
				});

			});
		}


		/**
		 * submit widget
		 */
		$('#form-widget #configuration_form').live('submit', function(e) {
			e.preventDefault();
			$(this).validate();
			let btn = $(this).find('.save');
			btn.addClass('loading');
			let url = $(this).attr('action');
			let form_edit = true;
			let widget,key_widget = null;

			if ($(this).find('#id_deomegamenu_widgets').val()){
				widget = $('.deo-widget.active');
				key_widget = widget.data('id_widget');
				url += '&savedeowidget&ajax&id_widget='+key_widget;
			}else{
				form_edit = false;
				url += '&savedeowidget&ajax';
				$('input[type="text"], textarea',$(this)).each(function(){
					if ($(this).closest('.translatable-field').css('display') == 'block'){
						if ($(this).hasClass('autoload_rte')){
							$(this).closest('.form-group').find('input[type="text"], textarea').val(tinymce.get($(this).attr('id')).getContent());
						}else if ($(this).val() != ''){
							$(this).closest('.form-group').find('input[type="text"], textarea').val($(this).val());
						}
					}
				});
			}

			let data = $(this).serializeArray();

			$.ajax({
				url: url,
				type: 'POST',
				data: data,
			}).done(function(jsonData) {
				jsonData = jQuery.parseJSON(jsonData);
				
				let col = $(".mega-col.active");
				let widgets = col.data('widgets');
				let listWidgets = (widgets) ? widgets.toString().split('|') : [];

				let key_widget = jsonData.key_widget;

				if (form_edit){
					config.param_widgets_menu[key_widget] = jsonData;
					$.each(config.list_option_widgets, function (key, option) {
						if (option.key_widget == key_widget){
							option.name = jsonData.name;
						}
					});
				}else{
					listWidgets.push(key_widget);
					config.param_widgets_menu[key_widget] = jsonData;
					config.list_option_widgets.push({key_widget : jsonData.key_widget, name : jsonData.name});
				}
				updateCol(col,listWidgets);

				// init select
				let select_change_widget = $(select_widgets);
				initSelect(select_change_widget);

				// update option to widget
				$(".mega-col:not(.active) .deo-widget").each(function(index) {
					$(this).find('.w-name .inject_widget_name').remove();
					$(this).find('.w-name').append(select_change_widget.clone(1).val($(this).data('id_widget')));
				});

				// update select to add widget
				initSelect($("#widget-setting .key_widget"));

				removeRowActive();
				removeColumnActive();
				removeWidgetActive();
				btn.removeClass('loading');
				$('#form-widget').modal('hide');
			});
		});


		/**
		 * submit widget
		 */
		$('#configuration_form.AdminDeoMegamenu').live('submit', function(e) {
			if (add_group_menu){
				return true;
			}
			e.preventDefault();
			let data = $(this).serializeArray();
			let url = $(this).attr('action');

			$.ajax({
				async: false,
				url: url,
				type: 'POST',
				data: data,
			}).done(function(jsonData) {

			});

		});


		/**
		 * Show popup setting submenu
		 */
		function processSettingSubMenu(parent){
			if (parent.parent().hasClass('megamenu')){
				let pos =  parent.offset();
				$('#menu-form').css({
					'left' 	: pos.left - (30 + liveeditor.left),
					'top'	: pos.top - ($('#menu-form').outerHeight() + liveeditor.top)
				});
			}
		}

		/**
		 * remove active status for current widget.
		 */
		function removeWidgetActive(){
			$( "#mainmenutop .deo-widget.active" ).removeClass('active');
		}

		/**
		 * remove active status for current row.
		 */
		function removeRowActive(){
			$( "#mainmenutop .row.active" ).removeClass('active');
		}

		/**
		 * remove column active and hidden column form.
		 */
		function removeColumnActive(){
			$( "#mainmenutop .mega-col.active" ).removeClass('active');
		}

		/**
		 * remove active status for current menu, row and column and hidden all setting forms.
		 */
		function removeMenuActive(){
			$('.form-setting').hide();
			$( "#mainmenutop .open-sub" ).removeClass('open-sub');
			$( "#mainmenutop .row.active" ).removeClass('active');
			$( "#mainmenutop .mega-col.active" ).removeClass('active');
			if( activeMenu ) {	
				activeMenu = null;
			}
		}


		/**
		 * update widget on col
		 */
		function updateCol(col,listWidgets){
			let html = '';
			let mega_col_inner = col.children('.mega-col-inner');
			mega_col_inner.children('.deo-widget').remove();
			mega_col_inner.append('<div class="loading">Loading....</div>');
			col.data('widgets', listWidgets.join('|'));

			// wait 1s;
			setTimeout(function(){
				$.each(listWidgets, function (i, widget) {
					if (typeof config.param_widgets_menu[widget] != 'undefined') {
						let widget_element = $(config.param_widgets_menu[widget].html);
						let select = widget_element.find('.w-name .inject_widget_name').remove();
						let select_change_widget = $(select_widgets);
						initSelect(select_change_widget);
						widget_element.append($(btn_widgets).clone(1));
						widget_element.find('.w-name').append(select_change_widget.clone(1).val(widget_element.data('id_widget')));
						mega_col_inner.append(widget_element);
					}else{
						mega_col_inner.append('<div class="deo-widget empty" data-id_widget="'+ widget +'">Widget not exist'+btn_widgets+'</div>');
					}
				});

				if (col.find('.deo-widget').length){
					col.removeClass('empty');
				}else{
					col.addClass('empty');
				}

				mega_col_inner.children('.loading').remove();
				$( "a", col ).not(".nav-tab-link").attr('href', 'javascript:void(0)');
				$(col).removeClass('active');
				$('#widget-setting').modal('hide');
			}, 600);
		}


		/**
		 * process saving menu data using ajax request. Data Post is json string
		 */	
		function saveMenuData(btn){
			if (add_group_menu){
				$('#configuration_form.AdminDeoMegamenu').submit();
				return false;
			}

			btn.children('i').addClass('process-icon-loading');
			$("#deo_loading").show();
			// var output = new Array();
			let output_widget = new Object();	
			$('#mainmenutop .navbar-nav > li.parent.enable-widget').each( function() {
				let data = $(this).clone(1).data();
				let id_menu = data.id;
				data.rows = new Array();
				// remove id property
				delete data.id;
				delete data.sortableItem;

				$(this).children('.dropdown-widget').find('.row').each(function(){
					let row =  new Object();
					row.cols = new Array();
					$(this).children(".mega-col").each(function(){
						let data = $(this).data();
						row.cols.push($(this).data());
					});
					data.rows.push(row);
				});

				// output.push(data);  
				output_widget[id_menu] = data;
			});
			// console.log(JSON.stringify(output_widget));

			let output_subwith = new Object();	
			$('#mainmenutop .navbar-nav > li:not(.add-new)').each( function() {
				let id_menu = $(this).data('id');
				let subwith = $(this).data('subwith');
				output_subwith[id_menu] = subwith;
			});

			// console.log(JSON.stringify(output_subwith));

			$.ajax({
				url: config.action_save,
				data:'params_widget='+encodeURIComponent(JSON.stringify(output_widget))+'&params_subwith='+encodeURIComponent(JSON.stringify(output_subwith))+'&id_shop='+config.id_shop,
				type:'POST',
			}).done(function(data) {
				$('#configuration_form.AdminDeoMegamenu').submit();
				btn.children('i').removeClass('process-icon-loading');
				$("#deo_loading").hide(); 
				showSuccessMessage(data);
			});

			// btn.removeClass('loading');
		}


		/**
		 * Make Ajax request to fill widget content into column
		 */
		function loadWidgets(){
			let ajaxCols = new Array();
			$("#progress-menu").hide();
			$("#megamenu-content #mainmenutop .mega-col").each( function() {
				if ($(this).data( 'widgets') && $(this).data("type") != "menu"){  
					ajaxCols.push($(this));
				}		
			});

			if( ajaxCols.length > 0 ){
				$("#progress-menu").show();
				$("#megamenu-content").hide();
			}


			// ONE ALL WIDGETS ONE AJAX - BEGIN
			let allWidgets = {};
			$("#megamenu-content #mainmenutop .mega-col").each( function() {
				let objHook = {};
				if( $(this).data( 'widgets') && $(this).data("type") != "menu" ){
					objHook['id_widget'] = $(this).data( 'widgets');
					objHook['id_shop'] = config.id_shop;
					allWidgets[$(this).data('widgets')] = objHook;
				}
			});
			$.ajax({
				url: config.action_widget,
				cache: false,
				data: {
					ajax : true,
					allWidgets : 1,
					dataForm : encodeURIComponent(JSON.stringify(allWidgets)),
				},
				type:'POST',
			}).done(function( jsonData ) {
				jsonData = jQuery.parseJSON(jsonData);
				let cnt = 0, check_end = 0;
				$.each(ajaxCols, function (i, col) {
					col.children('.mega-col-inner').html(jsonData[$(col).data( 'widgets')]['html']);
					col.append(btn_cols);
					cnt++;
					$("#progress-menu .progress-bar").css("width", (cnt*100)/ajaxCols.length+"%" );
					if( ajaxCols.length == cnt ) {
						$("#megamenu-content").delay(1000).fadeIn();
						$("#progress-menu").delay(1000).fadeOut();
					}
					$( "a", col ).not(".nav-tab-link").attr('href','javascript:void(0)');
					check_end++;
				});
			});

			return;
		}


		/**
		 * load all widgets and set for global variable
		 */	
		function loadAllWidget(){
			$.ajax({
				url: config.action_loadwidget,
				data: {
					getListWidgets: 1,
					backoffice: 1,
				},
				type:'POST',
			}).done(function(data) {
				data = jQuery.parseJSON(data);
				if (!data.success){
					$("#progress-menu .progress-bar").css("width", "100%");
					$("#progress-menu .progress-bar").attr('aria-valuenow',100);
					$("#progress-menu .progress-bar .percentage").html("100%");
					$("#megamenu-content").delay(600).fadeIn();
					$("#progress-menu").delay(600).fadeOut();

					return false;
				}

				config.param_widgets_menu = data.data;

				// create list select option
				let widgets = data.data;
				$.each(widgets, function (i, widget) {
					config.list_option_widgets.push({key_widget : widget.key_widget, name : widget.name});
				});

				$("#megamenu-content #mainmenutop .dropdown-widget.dropdown-menu").each( function() {
					$(this).append(btn_add_row);
				});

				$("#megamenu-content #mainmenutop .dropdown-widget.dropdown-menu .row").each( function() {
					$(this).append(btn_rows);
				});

				// init select
				let select_change_widget = $(select_widgets);
				initSelect(select_change_widget);

				// init select to add widget
				initSelect($("#widget-setting .key_widget"));
				$("#widget-setting #btn-inject-widget").removeClass('hide');

				let ajaxCols = new Array();
				$("#megamenu-content #mainmenutop .dropdown-widget .mega-col").each(function() {
					if ($(this).data("type") != "menu"){  
						ajaxCols.push($(this));
					}		
				});

				if (ajaxCols.length == 0){
					$("#progress-menu .progress-bar").css("width", "100%" );
					$("#progress-menu .progress-bar").attr('aria-valuenow',100);
					$("#progress-menu .progress-bar .percentage").html("100%");
					$("#megamenu-content").delay(600).fadeIn();
					$("#progress-menu").delay(600).fadeOut();
				}else{
					// init widget
					let cnt = 0, check_end = 0;
					$.each(ajaxCols, function(i, col){
						if (typeof col.data('widgets') != 'undefined'){
							let widgets = col.data('widgets').toString().split('|');
							$.each(widgets, function(i, widget){
								if (typeof config.param_widgets_menu[widget] != 'undefined') {
									let widget_element = $((config.param_widgets_menu[widget].html).replace(/<\!--.*?-->/g, ""));
									widget_element.append($(btn_widgets).clone(1));
									widget_element.find('.w-name').append(select_change_widget.clone(1).val(widget_element.data('id_widget')));
									col.children('.mega-col-inner').append(widget_element);
								}else{
									col.children('.mega-col-inner').append('<div class="deo-widget empty" data-id_widget="'+ widget +'">Widget not exist'+btn_widgets+'</div>');
								}
							});
						}

						col.append(btn_cols);
						$( "a", col).not(".nav-tab-link").attr('href','javascript:void(0)');

						cnt++;
						let percentage = parseInt((cnt*100)/ajaxCols.length);

						$("#progress-menu .progress-bar").css("width", percentage+"%" );
						$("#progress-menu .progress-bar").attr('aria-valuenow',percentage);
						$("#progress-menu .progress-bar .percentage").html(percentage+"%");
						if (ajaxCols.length == cnt) {
							$("#megamenu-content").delay(600).fadeIn();
							$("#progress-menu").delay(600).fadeOut();
						}
						check_end++;
					});
				}

				$('#mainmenutop .navbar-nav.megamenu').append(new_menu);

				// create array position
				$('#mainmenutop .navbar-nav > li:not(.add-new)').each(function(key) {
					config.array_position[key] = $(this).data("id");
				});	
				
				// init sort menu
				$("#mainmenutop .navbar-nav.megamenu").sortable({
					connectWith: "#mainmenutop .navbar-nav.megamenu",
					items: "li[data-id_parent='0']:not(.add-new)",
				});	
			});

			/**
			 * update position
			 */
			$("#mainmenutop .navbar-nav.megamenu").on("sortupdate", function( event, ui ) {
				$('#mainmenutop .navbar-nav > li:not(.add-new)').each(function(key) {
					id_loop = $(this).data("id");
					// console.log(config.array_position[key], id_loop);
					if (config.array_position[key] != id_loop){
						let id_nav_1 = config.array_position[key];
						let id_nav_2 = id_loop;
						let nav_1 = $('#mainmenutop .navbar-nav > li[data-id="'+id_nav_1+'"]');
						let nav_2 = $('#mainmenutop .navbar-nav > li[data-id="'+id_nav_2+'"]');
						let position_nav_1 = nav_1.data('position');
						let position_nav_2 = nav_2.data('position');

						$("#deo_loading").show();
						$.ajax({
							url: config.action_changeposition+'&id_nav_1='+id_nav_1+'&id_nav_2='+id_nav_2+'&position_nav_1='+position_nav_1+'&position_nav_2='+position_nav_2,
							type:'POST',
						}).done(function(jsonData) {
							jsonData = jQuery.parseJSON(jsonData);
							if (jsonData.success){
								nav_1.data('position', position_nav_2);
								nav_2.data('position', position_nav_1);

								// create array position again
								$('#mainmenutop .navbar-nav > li:not(.add-new)').each(function(key) {
									config.array_position[key] = $(this).data("id");
								});	
								
								showSuccessMessage(jsonData.msg);

								// console.log(position_nav_1, position_nav_2);							
							}
							
							$("#deo_loading").hide();
						});


						return false;
					}
				});	

				// console.log(config.array_position);

			});
		};


		/**
		 * create select
		 */	
		function initSelect(select){
			select.empty();
			$.each(config.list_option_widgets, function (key, option) {
				select.append(new Option(option.name, option.key_widget));
			});
		}


		/**
		 * reload menu data using in ajax complete and add healders to process events.
		 */	
		function reloadMegamenu(){
			let megamenu = $("#megamenu-content #mainmenutop");
			$("a", megamenu).not(".nav-tab-link").attr('href','javascript:void(0)');
			$('[data-toggle="dropdown"]',megamenu ).attr('data-toggle','deo-dropdown');
			$(".nav-item .nav-link",megamenu).append(btn_menu); 
			listenEvents(megamenu);
			//submenuForm();
			actionMenuForm();
			// loadWidgets();
		}

		/**
		 * initialize every element
		 */
		this.each(function() {
			let megamenu = this;
			$("#form-setting").hide();
			$.ajax({
				async: false,
				url: config.action,
				type:'POST',
			}).done(function(data) {
				data = jQuery.parseJSON(data);
				if (data.success){
					let rootMenu =  $(data.data);
					rootMenu.find('a.nav-link').removeAttr('data-toggle').removeClass('dropdown-toggle');
					$(megamenu).append(rootMenu);
					reloadMegamenu();
					loadAllWidget();
				}else{
					add_group_menu = true;
					$("#progress-menu .progress-bar").css("width", "100%" );
					$("#progress-menu .progress-bar").attr('aria-valuenow',100);
					$("#progress-menu .progress-bar .percentage").html("100%");
					$(".megamenu-wrap .alert").delay(600).fadeIn();
					$("#progress-menu").delay(600).fadeOut();
				}

				$('#page-header-desc-deomegamenu-save').live('click', function(e) {
					saveMenuData($(this));
				});
			});
		});
		
		return this;
	};
	
})(jQuery);