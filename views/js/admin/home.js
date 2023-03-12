/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (typeof address_token === "undefined") {
	var match = RegExp('[?&]' + 'token' + '=([^&]*)').exec(window.location.search);
	var address_token = match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}

imgId = null; // using for store object image select a source in function select image
function log(message) {
	console.log(message);
}
function htmlentities(str) {
	let textarea = document.createElement("textarea");
	textarea.innerHTML = str;
	return textarea.innerHTML;
}
function htmlentitiesDecode(str) {
	let textarea = document.createElement("textarea");
	textarea.innerHTML = str;
	return textarea.value;
}
(function ($) {
	$.fn.deotemplate = function () {
		this.currentElement = null;
		this.ajaxShortCodeUrl = null;
		this.ajaxHomeUrl = null;
		this.shortCodeField = null;
		this.shortcodeInfos = null;
		this.languages = null;
		this.lang_id = 0;
		this.classWidget = 'ui-widget ui-widget-content ui-helper-clearfix ui-corner-all';
		this.classWidgetHeader = 'ui-widget-header ui-corner-all';
		this.classNotAllow = 'hidden-xxl hidden-xl hidden-lg hidden-md hidden-sm hidden-xs hidden-sp';
		this.widthSupport = null;
		this.arrayCol = null;
		this.windowWidth = 0;
		this.imgController = null;
		this.parentId = null;
		this.profileId = null;
		this.positions = new Object();
		this.widgets_modules = new Array();
		this.elements = new Array();
		this.product_lists = new Array();

		this.process = function () {
			let $globalthis = this;
			$globalthis.windowWidth = $(window).width();
			$globalthis.shortcodeInfos = jQuery.parseJSON(deo_shortcode_infos);
			$globalthis.languages = jQuery.parseJSON(deo_languages);
			$globalthis.initDataFrom(deo_data_form);
			$globalthis.widthSupport = ["1", "2", "2.4", "3", "4", "5", "4.8", "6", "7", "7.2", "8", "9", "9.6", "10", "11", "12"];
			$globalthis.arrayCol = ["sp", "xs", "sm", "md", "lg", "xl", "xxl"];
			$globalthis.initColumnSetting();
			$globalthis.initIsotopAction($('#list-widgets'));

			$globalthis.setGroupAction();
			$globalthis.sortable();
			$globalthis.setButtonAction();
			$globalthis.submitForm();
			$globalthis.initDragWidget();


			// Load form after come back from live edit mode
			let type = window.location.href.split('#');
			let hash = '';
			if (type.length > 1) {
				hash = type[1];
				let btn = $("." + hash).find(".btn-edit");
				//$(btn).trigger("click");
			}
			//$globalthis.setRowAction();
			
			$('.group-row,.column-row,.widget-row').removeClass($globalthis.classNotAllow);
		};

		this.initDragWidget = function() {
			$('#list-widgets .widget-row').draggable({
				connectToSortable: ".column-content",
				revert: "true",
				helper: "clone",
				appendTo: '#wrapper-page-builder',
				cursor: 'move',
				zIndex: 10000,
				scroll: false,
				handle: ".waction-drag",
				stop: function(event,ui) {
					$globalthis.changeDataFromWhenDragWidget($(this));
				}
			});
		};

		this.changeDataFromWhenDragWidget = function($element) {
			data_form = $element.data('form');
			let form_id = "form_" + $globalthis.getRandomNumber();
			data_form.form_id = form_id;

			if ($element.hasClass('DeoAccordions')){
				let accIdWraper = 'accordion_'+$globalthis.getRandomNumber();
				$element.find('.panel-group').attr('id', accIdWraper);
				$element.find('.accordion-panel').each(function(key,item){
					let collapse_id = "collapse_" + $globalthis.getRandomNumber();
					let collapse_form_id = "form_" + $globalthis.getRandomNumber();
					let titleAccordion = $.trim($(this).find('.panel-title > a').html());

					$(this).find('.panel-collapse').attr('id', collapse_id);
					$(this).find('.panel-title > a').attr('class', collapse_form_id);
					$(this).find('.panel-title > a').attr('href', '#'+collapse_id);

					$(this).find('.panel-title > a').data('parent', accIdWraper);
					let ObjectAccordion = {form_id: "form_" + $globalthis.getRandomNumber()};
					ObjectAccordion.id = collapse_id;
					ObjectAccordion.parent_id = accIdWraper;
					ObjectAccordion.active_accordion = (key == 0) ? "1" : "0";
					ObjectAccordion.active_type = "set";
					Object.keys($globalthis.languages).forEach(function (key) {
						ObjectAccordion["title_" + $globalthis.languages[key]] = titleAccordion;
						ObjectAccordion["sub_title_" + $globalthis.languages[key]] = '';
					});
					$(this).find('.panel-title > a').data("form", ObjectAccordion);
				});
			}else if ($element.hasClass('DeoTabs')){
				$element.find('.nav-tabs li:not(.tab-button) > a').each(function(key,item){
					let tab_id = "tab_" + $globalthis.getRandomNumber();
					let tab_form_id = "form_" + $globalthis.getRandomNumber();
					let titleTab = $.trim($(this).html());

					$element.find('.tab-pane:nth-child('+(key+1)+')').attr('id', tab_id);
					$(this).attr('class', tab_form_id);
					$(this).attr('href', '#'+tab_id);
					$(this).attr('id', tab_id);

					let ObjectTab = {form_id: tab_form_id};
					ObjectTab.id = tab_id;
					ObjectTab.css_class = "";
					ObjectTab.image = "";
					ObjectTab.override_folder = "";
					ObjectTab.active_tab = (key == 0) ? "1" : "0";
					Object.keys($globalthis.languages).forEach(function (key) {
						ObjectTab["title_" + $globalthis.languages[key]] = titleTab;
						ObjectTab["sub_title_" + $globalthis.languages[key]] = '';
					});
					$(this).data("form", ObjectTab);
				});
			}

			$element.data('form', data_form);
		}

		this.initDataFrom = function (data) {
			let $globalthis = this;
			if (data != '{}') {
				dataObj = jQuery.parseJSON(data);

				Object.keys(dataObj).forEach(function (key) {
					$('.' + key).data('form', dataObj[key]);
					// install data animation for column and group
					if (typeof dataObj[key].animation != 'undefined'){					
						if ($('.' + key).find('.animation-button').first().length){							
							let animation_bt = $('.' + key).find('.animation-button').first();
							let animation_type = dataObj[key].animation ? dataObj[key].animation : 'none';
							let animation_delay = dataObj[key].animation_delay ? dataObj[key].animation_delay : 1;
							let animation_duration = dataObj[key].animation_duration ? dataObj[key].animation_duration : 1;
							let animation_iteration_count = dataObj[key].animation_iteration_count ? dataObj[key].animation_iteration_count : 1;
							let animation_infinite = dataObj[key].animation_infinite ? dataObj[key].animation_infinite : 0;	
							
							$globalthis.assignConfigAnimation(animation_bt, animation_type, animation_delay, animation_duration, animation_iteration_count, animation_infinite);
						}
					}
					
				});
				
				// fix can't click tab 1 when create new widget tab				
				$('.DeoTabs:not(#default_DeoTabs)').each(function(){	
					if ($(this).data('form')){
						let activeTabId = $(this).data('form').active_tab;					
						if (activeTabId != '' && parseInt(activeTabId)){
							$(this).find('.nav-tabs a').eq(parseInt(activeTabId)-1).tab('show');
						}
					}				
				});
			}
		};
		
		this.getColDefault = function () {
			return {xxl:12, xl:12, lg: 12, md: 12, sm: 12, xs: 12, sp: 12};
		};
		//set action for group
		this.setGroupAction = function () {

			//duplicate group
			$('.gaction-duplicate').click(function () {
				let duplicate = $(this).closest('.group-row').clone(1);
				//remove tooltip because wrong position
				$('.tooltip', $(duplicate)).remove();
				$('.label-tooltip', $(duplicate)).tooltip('disable');
				$('.hook-content-footer', $(this).closest('.hook-content')).before(duplicate);
			});

			$('.number-column').click(function () {
				column = $(this).data('cols');
			});

			$('.gaction-toggle').click(function () {
				$(this).closest('.group-row').find('.group-content').first().toggle('clip');
			});
		};
		//sort group
		this.sortable = function () {
			let $globalthis = this;

			$(".hook-content").sortable({
				// cursor: 'move',
				connectWith: ".hook-content",
				handle: ".gaction-drag"
			});
			$(".group-row").addClass($globalthis.classWidget).find(".gaction-drag").addClass($globalthis.classWidgetHeader);

			$(".hook-content .group-content").sortable({
				// cursor: 'move',
				connectWith: ".group-content",
				handle: ".caction-drag"
			});
			$(".column-row").addClass($globalthis.classWidget).find(".caction-drag").addClass($globalthis.classWidgetHeader);

			$(".group-content .column-content").sortable({
				// cursor: 'move',
				connectWith: ".column-content",
				handle: ".waction-drag"
			});
			$(".widget-row").addClass($globalthis.classWidget).find(".waction-drag").addClass($globalthis.classWidgetHeader);

			$(".subwidget-content").sortable({
				// cursor: 'move',
				connectWith: ".subwidget-content",
				handle: ".waction-drag"
			});
			// $( ".widget-row" ).addClass( $globalthis.classWidget )
			//    .find( ".waction-drag" ).addClass( $globalthis.classWidgetHeader );    

		};
		this.downloadFile = function (filename, result) {
			// csvData = 'data:application/xml;charset=utf-8,' + result;
			// console.log(result);
			$("#export_process")
				.attr({
					'download': filename,
					'href': result,
					'target': '_blank'
				});
			$("#export_process").get(0).click();
		};
		//general action
		this.setButtonAction = function () {
			let $globalthis = this;
			$globalthis.initControllInRow();
			this.createColumn = function (obj, currentId) {
				let widthCol = $(obj).data('width');
				let classActive = $globalthis.returnWidthClass();
				let col = $(obj).data('col');
				let realValue = widthCol.toString().replace('.', '-');
				for (let i = 1; i <= col; i++) {
					wrapper = currentId;///$($globalthis.currentElement).find('.group-content').first();
					column = $('#default_column').clone(1);
					column.find('.column-content').sortable({
						// cursor: 'move',
						connectWith: ".column-content",
						handle: ".waction-drag"
					});
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
					objColumn = {form_id: "form_" + $globalthis.getRandomNumber()};
					
					objColumn.sm = widthCol;
					objColumn.xs = widthCol;
					objColumn.sp = widthCol;
					if (classActive == "md" || classActive == "lg" || classActive == "xl" || classActive == "xxl") {
						objColumn.md = widthCol;
						objColumn.lg = widthCol;
						objColumn.xl = widthCol;
						objColumn.xxl = widthCol;
					}
					//jQuery.extend(objColumn, $globalthis.getColDefault());
					$(column).data("form", objColumn);

					let columnClass = '';
					Object.keys($globalthis.getColDefault()).forEach(function (key) {
						columnClass += ' col-' + key + '-' + objColumn[key].toString().replace('.', '-');
					});
					$(column).data("class", columnClass);

					column.removeAttr('id');
					if (wrapper.children('.group-content').length){
						wrapper.children('.group-content').append(column);
					}else{
						wrapper.append(column);
					}
					
					$globalthis.getNumberColumnInClass(column, classActive);
					$(".label-tooltip").tooltip();
				}
			}
			$(document).on("click", ".column-add", function () {
				$globalthis.createColumn(this, $globalthis.currentElement);
				$('.popover').hide();
			});
			$(document).on("click", ".group-add", function () {
				let item = $(this).data("col");
				currentE = $globalthis.currentElement;
				// Create a group blank
				if (item == 0) {
					group = $("#default_row").clone(1);
					group.removeAttr('id');
					group.children('.group-content').sortable({
						// cursor: 'move',
						connectWith: ".group-content",
						handle: ".caction-drag",
					});
					//let html = $(group).find(".group-controll-right").html();
					//$(group).find(".group-controll-right").html(html);
					$(group).data("form", {form_id: "form_" + $globalthis.getRandomNumber(), 'class': 'row', 'container' : 'container'});
					$(currentE).before(group);
					$globalthis.initControllInRow();
				}
				// Display popup list Widget for add new a widget
				else if (item == 1) {
					// This code similar event click to button:
					// $(".btn-new-widget").trigger("click");
					let url = $globalthis.ajaxHomeUrl + '&ajax=1&action=renderList';
					let data = '';
					$("#deo_loading").show();

					$.ajax({
						type: 'POST',
						headers: {"cache-control": "no-cache"},
						url: url,
						async: true,
						data: data,
						dataType: 'json',
						cache: false,
						success: function (json) {
							$("#deo_loading").hide();
							if (json && json.hasError == true){
								alert(json.errors);
							}else{
								$("#modal_form .txt-search").show();
								$('#myModalLabel').html($('#myModalLabel').data('addnew'));
								$('#modal_form .modal-body').html(json.result);
								$('#modal_form .modal-footer').hide();
								$('#modal_form').modal('show');
								$('#modal_form').removeClass('modal-edit').addClass('modal-new');
								$globalthis.setFormAction();
								$globalthis.initControllInRow();
								$globalthis.initIsotopAction($('#modal_form'));
								$("#modal_form .txt-search").focus();
							}
						},
						error: function (XMLHttpRequest, textStatus, errorThrown) {
							$("#deo_loading").hide();
							alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
						}
					});
				} else {
					group = $("#default_row").clone(1);
					group.children('.group-content').sortable({
						// cursor: 'move',
						connectWith: ".group-content",
						handle: ".caction-drag",
					});
					group.removeAttr('id');
					//let html = $(group).find(".group-controll-right").html();
					//$(group).find(".group-controll-right").html(html);
					$(group).data("form", {form_id: "form_" + $globalthis.getRandomNumber(), 'class': 'row', 'container' : 'container'});
					$(currentE).before(group);
					$globalthis.createColumn(this, group);
					$globalthis.initControllInRow();
				}
			});
			$(document).on("click", ".btn-select-profile", function () {
				if (!confirm($("#form_content").data('select')))
					return false;
			});

			$(document).on("click", ".btn-back-to-list", function () {
				currentElement = $globalthis.currentElement;
				//add new in column
				if ($(currentElement).hasClass('column-content') || $(currentElement).hasClass('subwidget-content')) {
					$(currentElement).parent().find('.btn-new-widget').first().trigger('click');
				}
				//add new in group
				else {
					$(currentElement).parent().find('.hook-content-footer .btn-new-widget').trigger('click');
				}
				$("#modal_form").removeClass('modal-new modal-edit new-short-code');
			});

			//save widget
			$(document).on("click", ".btn-savewidget", function (e) {
				if (!$(".row-level2").hasClass("hide") && $(".row-level2").length){
					showErrorMessage('Please save or close child slide first!');
					scrollToModal($('#modal_form .modal-body'), $('#frm-level2'));
					$('#frm-level2').animate({ 'zoom': 1.2 }, 400, function(){$('#frm-level2').animate({ 'zoom': 1 }, 400)});

					return false;
				}

				hideFormLevel2();
				currentElement = $globalthis.currentElement;
				//add new widget
				if ($("#modal_form").hasClass("modal-new")) {
					//add new widget in column
					if ($(currentElement).hasClass('column-content')) {
						$globalthis.saveWidget('column');
					}
					else if ($(currentElement).hasClass('subwidget-content')) {
						$globalthis.saveWidget('column');
					}
					//add new widget in hook
					else {
						$globalthis.saveWidget('hook');
					}
				} else {
					$globalthis.saveWidget('update');
				}

				// set active sub
				if ($($globalthis.currentElement).hasClass('DeoTabs')) {
					// Tab
					$globalthis.setActiveSub($(currentElement));
				}else if ($($globalthis.currentElement).attr('data-toggle') == 'tab') {
					// sub Tab
					$globalthis.setActiveSub($(currentElement).closest('.widget-row'));
				}if ($($globalthis.currentElement).hasClass('DeoAccordions')) {
					// Accordions
					$globalthis.setActiveSub($(currentElement));
				}else if ($($globalthis.currentElement).attr('data-toggle') == 'collapse') {
					// sub Accordions
					$globalthis.setActiveSub($(currentElement).closest('.widget-row'));
				}

				$globalthis.currentElement = null;
				$(".label-tooltip").tooltip();
				$('#modal_form').modal('hide');
				$globalthis.initControllInRow();
			});

			$(document).on("click", ".btn-fwidth", function () {
				$('#home_wrapper').css('width', $(this).data('width'));

				btnElement = $(this);
				$('.btn-fwidth').removeClass('active');
				$(this).addClass('active');
				//reset    
				if ($(this).hasClass('width-default')) {
					$globalthis.windowWidth = $(window).width();
					$('#home_wrapper').attr('class', 'default');
				} else {
					$('#home_wrapper').attr('class', 'col-' + $globalthis.returnWidthClass(parseInt($(this).data('width'))));
					$globalthis.windowWidth = $(this).data('width');
				}
				classVal = $globalthis.returnWidthClass();
				$(".column-row", $('#home_wrapper')).each(function () {
					valueFra = $(this).data("form")[classVal];
					$(".deo-btn-width .width-val", $(this)).attr("class", "width-val deo-w-" + valueFra.toString().replace(".", "-"));
				});
				$globalthis.initColumnSetting();
			});

			$(document).on("click", ".btn-import", function () {
				$("#deo_loading").show();
				let url = $globalthis.ajaxHomeUrl + '&ajax=1&action=showImportForm&idProfile=' + $globalthis.profileId;
				let data = '';
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: url,
					async: true,
					data: data,
					dataType: 'json',
					cache: false,
					success: function (json){
						$("#deo_loading").hide();
						if (json && json.hasError == true){
							alert(json.errors);
						}else{
							$('#modal_import .modal-body').html(json.result);
							$('#modal_import .modal-footer').hide();
							$('#modal_import').modal('show');
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						$("#deo_loading").hide();
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			});

			$(document).on("click", ".btn-export", function () {
				let objects = new Object();
				type = $(this).data("type");
				let position = '';
				if (type == "group") {
					objHook = {};
					objHook.groups = {};
					objHook.groups[0] = $globalthis.getHookSubmit($(this).closest('.group-row'));
					objects[0] = objHook;
				} else if (type == "position") {
					position = $(this).data("position");
					type = "position-" + position;
					let id = "#position-" + $(this).data("position") + " .hook-wrapper";
					$(id).each(function (iHook) {
						//hook object contain group
						let objHook = {};
						objHook.name = $(this).data('hook');
						objHook.position = $(this).data('hook');
						objHook.groups = {};
						$('.group-row', $(this)).each(function (iGroup) {
							objHook.groups[iGroup] = $globalthis.getHookSubmit(this);
						});

						objects[iHook] = objHook;
					});
				} else if (type == "all") {
					$('.hook-wrapper').each(function (iHook) {
						//hook object contain group
						let objHook = {};
						objHook.name = $(this).data('hook');
						objHook.position = $(this).data('hook');
						objHook.groups = {};
						$('.group-row', $(this)).each(function (iGroup) {
							objHook.groups[iGroup] = $globalthis.getHookSubmit(this);
						});

						objects[iHook] = objHook;
					});
				} else {
					objHook = {};
					objHook.groups = {};
					$('.group-row', $('.' + type)).each(function (iGroup) {
						objHook.groups[iGroup] = $globalthis.getHookSubmit(this);
					});
					objects[0] = objHook;
				}

				data = 'dataForm=' + JSON.stringify(objects);

				$("#deo_loading").show();
				url = $globalthis.ajaxHomeUrl + '&action=export&type=' + type;

				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: url,
					async: true,
					cache: false,
					data: data,
					dataType: 'json',
					cache: false,
					success: function (json)
					{
						$("#deo_loading").hide();
						if (json && json.hasError == true){
							alert(json.errors);
						}else{
							if (type == 'all')
								type = 'home';
							$globalthis.downloadFile(type + '.xml', json.result);
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						$("#deo_loading").hide();
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			});

			//delete group
			$(document).on("click", ".btn-delete", function () {
				if (!confirm($("#form_content").data("delete")))
					return false;

				let widget = $(this).closest('.widget-row');
				
				// Deleta tab
				if ($(this).hasClass("tab")) {
					// Check this must be have greater than 2 tabs
					let tabcontent = $(this).closest(".tab-content");
					let limit = $(tabcontent).find("#default_tabcontent").length;
					if ($(tabcontent).find(".tab-pane").length == 2) {
						showErrorMessage("Can not delete when have 2 tabs");
						return;
					}

					// remove
					tabId = $(this).closest(".tab-pane").attr('id');
					$('a[href$="' + tabId + '"]:first()').closest("li").remove();
					$("#" + tabId).remove();
				}

				// Deleta accordion
				if ($(this).hasClass("accordions")) {
					let accordionscontent = $(this).closest(".panel-group");
					let limit = $(accordionscontent).find(".panel-default").length;
					if (limit == 1) {
						showErrorMessage("Can not delete when have 1 accordion");
						return;
					}

					// remove
					if ($(this).closest('.panel-default').length) {
						$(this).closest('.panel-default').remove();
					}
				}

				if (typeof $(this).data("for") == "undefined") {
					if ($(this).hasClass("group-action")) {
						$(this).closest(".group-row").remove();
					} else if ($(this).hasClass("column-action")) {
						$(this).closest(".column-row").remove();
					} else {
						// Delete group of tag, accordion
						$(this).closest(".widget-row").remove();
					}
				}
				else {
					$(this).closest($(this).data("for")).remove();
				}

				// set active sub
				if ($(this).hasClass('tab') || $(this).hasClass('accordions')) {
					$globalthis.setActiveSub(widget);
				}

			});

			//edit group
			$(document).on("click", ".btn-edit", function () {
				let type = $(this).data("type");
				if (typeof $(this).data('type') == "undefined") {
					type = $(this).closest('.widget-row').data("type");
				}

				if (type.indexOf("DeoSub") == 0) {
					if (type == "DeoSubAccordions") {
						idContainer = $(this).closest('.accordion-panel').find('.widget-container-content').attr("id");
					} else {
						idContainer = $(this).closest('.widget-wrapper-content').attr("id");
					}
					type = type.replace("Sub", "") + "&subTab";
					$globalthis.currentElement = $('a[href*="' + idContainer + '"]', $(this).closest(".widget-row"));
				} else {
					if (typeof $(this).data('for') == "undefined") {
						if (type == "DeoRow") {
							$globalthis.currentElement = $(this).closest(".group-row");
						} else if (type == "DeoColumn") {
							$globalthis.currentElement = $(this).closest(".column-row");
						} else {
							$globalthis.currentElement = $(this).closest(".widget-row");
						}
					}else{
						$globalthis.currentElement = $(this).closest($(this).data('for'));
					}
				}
				// console.log($globalthis.currentElement);
				let url = $globalthis.ajaxShortCodeUrl;
				if (type === "DeoModule") {
					url += '&ajax=1&edit&type_shortcode=any&type=module';
				} else if (type === "DeoRow") {
					let hook_name = $(this).closest("[data-hook]").attr('data-hook');
					url += '&ajax=1&edit&type_shortcode=' + type + "&type=widget" + "&id_deotemplate_profiles=" + $globalthis.profileId + "&hook_name=" + hook_name;
				} else {
					url += '&ajax=1&edit&type_shortcode=' + type + "&type=widget";
				}
				let obj = $($globalthis.currentElement).data("form");
				// console.log(obj);
				let data = '';
				if (obj){
					Object.keys(obj).forEach(function (key) {
						data += (data ? "&" : "") + key + "=" + obj[key];
					});
				}

				$("#modal_form .txt-search").hide();
				$("#deo_loading").show();

				// Store parent id
				if (type == "DeoSubAccordions" || type == "DeoAccordions&subTab") {
					$globalthis.parentId = $(this).closest(".panel-group").attr("id");
				}
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: url,
					async: true,
					cache: false,
					data: data,
					success: function (data) {
						data = data.replace(/_APNEWLINE_/g, "&#10;");
						$("#deo_loading").hide();
						$('#modal_form .modal-footer').show();
						$('#modal_form .modal-body').html(data);
						$('#myModalLabel').html($('#myModalLabel').data('edit') + " " + (($('#modal_form #name-module').length) ? $('#modal_form #name-module').val() : type.replace('deo_', '')));
						$('#modal_form').removeClass('modal-new').addClass('modal-edit');
						
						//$('#modal_form').modal('show');
						$("#modal_form").modal({
							"backdrop": "static"
						});
						if (type == "DeoBlockCarousel") {
							initFullSlider("edit");
						}
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
						hideFormLevel2();
						$globalthis.setFormAction();
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						$("#deo_loading").hide();
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			});
			
			$(document).on("click", ".btn-new-widget", function () {
				let btn = $(this);
				let url = $globalthis.ajaxHomeUrl + '&ajax=1&action=renderList';
				let reloadModule = false;
				if ($(this).hasClass('tabcontent-action')){
					url += '&subTab=1';
				}
				if ($(this).hasClass('reload-module')){
					url += '&reloadModule=1';
					reloadModule = true;
				}
				let data = '';
				if ($(this).hasClass('column-action')) {
					$globalthis.currentElement = $(this).closest('.column-row').find('.column-content').first();
				} else if ($(this).hasClass('tabcontent-action')) {
					if ($(this).hasClass('accordion'))
						$globalthis.currentElement = $(this).closest('.accordion-panel').find('.subwidget-content').first();
					else if ($(this).hasClass('popup'))
						$globalthis.currentElement = $(this).closest('.DeoPopup').find('.subwidget-content').first();
					else
						$globalthis.currentElement = $(this).closest('.tab-pane').find('.subwidget-content').first();
				} else {
					$globalthis.currentElement = $(this).closest('.hook-content-footer');
				}
				$("#deo_loading").show();

				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: url,
					async: true,
					data: data,
					dataType: 'json',
					cache: false,
					success: function (json) {
						$("#deo_loading").hide();
						if (json && json.hasError == true){
							alert(json.errors);
						}else{
							if (reloadModule){
								$('#modal_form .module_container').html(json.result);
								$('#list-widgets .module_container').html(json.result_sidebar);
								$('.module_container').each(function(){
									let col = $(this).data('col');
									let item = $(this).find('.item');
									item.attr("class", col);
								});

								if (btn.closest('#list-widgets').length){
									$globalthis.initIsotopAction($('#list-widgets'), true);
								}else{
									$globalthis.initIsotopAction($('#modal_form'), true);
								}

								$globalthis.initDragWidget();
							}else{
								$("#modal_form .txt-search").show();
								$('#myModalLabel').html($('#myModalLabel').data('addnew'));
								$('#modal_form .modal-body').html(json.result);
								$('#modal_form .modal-footer').hide();
								$('#modal_form').modal('show');
								$('#modal_form').removeClass('modal-edit').addClass('modal-new');
								$globalthis.setFormAction();
								$globalthis.initIsotopAction($('#modal_form'));
								$("#modal_form .txt-search").focus();
							}
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						$("#deo_loading").hide();
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			});
			$("#modal_form").on('shown.bs.modal', function () {
				$("#modal_form .txt-search").focus();
			});
			$("#modal_form").on('hidden.bs.modal', function () {
				$(this).removeClass('modal-new modal-edit new-short-code');

				// remove MCE from windown
				$('#modal_form textarea.autoload_rte').each((index, textarea) => {
				    if (window.tinyMCE) {
						const editor = window.tinyMCE.get(textarea.id);

						if (editor) {
							// Reset content to force refresh of editor
							tinyMCE.execCommand('mceRemoveEditor', false, textarea.id);
						}
				    }
				});
			});

			$(document).on("click", ".btn-status", function () {
				if (typeof $(this).data("for") == "undefined") {
					if ($(this).hasClass("group-action")) {
						$globalthis.currentElement = $(this).closest(".group-row");
					} else if ($(this).hasClass("column-action")) {
						$globalthis.currentElement = $(this).closest(".column-row");
					} else {
						$globalthis.currentElement = $(this).closest(".widget-row");
					}
				}else{
					$globalthis.currentElement = $(this).closest($(this).data("for"));
				}

				objForm = $globalthis.currentElement.data("form");

				if ($(this).hasClass("deactive")) {
					$(this).removeClass("deactive").addClass("active");
					objForm.active = 1;
					$(this).children().removeClass("icon-remove");
					$(this).children().addClass("icon-ok");
				} else {
					$(this).removeClass("active").addClass("deactive");
					objForm.active = 0;
					$(this).children().addClass("icon-remove");
					$(this).children().removeClass("icon-ok");
					// icon-remove
				}
				
				if ($($globalthis.currentElement).hasClass("deactive")) {
					$($globalthis.currentElement).removeClass("deactive").addClass("active");
				}else{
					$($globalthis.currentElement).removeClass("active").addClass("deactive");
				}
				
				objForm = $globalthis.currentElement.data('form', objForm);
			});

			$(document).on("click", ".btn-change-colwidth", function () {
				cla = $globalthis.returnWidthClass();
				elementColumn = $(this).closest('.column-row');
				objColumn = $(elementColumn).data('form');

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
				//get index of current width
				indexW = jQuery.inArray(valueColToNum.toString(), $globalthis.widthSupport);
				indexW = parseInt(indexW) + val;
				//get new width
				objColumn[cla] = $globalthis.widthSupport[indexW];
				//set class again
				classColumn = $globalthis.getClassColumn(objColumn);

				$(elementColumn).attr("class", classColumn);
				$(".deo-btn-width .width-val", $(elementColumn)).attr("class", "width-val deo-w-" + objColumn[cla].toString().replace(".", "-"));
				$(elementColumn).data("form", objColumn);
				$globalthis.getNumberColumnInClass(elementColumn, $globalthis.returnWidthClass());
				$globalthis.updateClassWidget(elementColumn);

				return false;
			});

			$(document).on("click", ".change-colwidth", function () {
				cla = $globalthis.returnWidthClass();
				width = $(this).data('width');
				elementColumn = $(this).closest('.column-row');
				objColumn = $(elementColumn).data('form');
				//get new width
				objColumn[cla] = width;
				//set class again
				classColumn = $globalthis.getClassColumn(objColumn);

				$(elementColumn).attr("class", classColumn);
				$(".deo-btn-width .width-val", $(elementColumn)).attr("class", "width-val deo-w-" + objColumn[cla].toString().replace(".", "-"));
				$(elementColumn).data("form", objColumn);
				$(this).closest("ul").find("li").removeClass("selected");
				$(this).closest("li").addClass("selected");
				$globalthis.getNumberColumnInClass(elementColumn, $globalthis.returnWidthClass());
				$globalthis.updateClassWidget(elementColumn);

				return false;
			});


			$(document).on("click", ".btn-add-tab", function () {
				//nav-tabs tab-content
				let widget = $(this).closest('.widget-row');
				let tabID = "tab_" + $globalthis.getRandomNumber();
				let form_id = "form_" + $globalthis.getRandomNumber();
				let tab = $("#default_DeoTabs").find('.nav-tabs > li:not(.tab-button)').first().clone(1);
				tab.removeClass('active');
				$(tab).find('a').attr('href', '#' + tabID);
				$(tab).find('a').attr('class', form_id);
				$(tab).find('a').html('New Tab');
				$(this).parent().before(tab);

				let ObjectTab = {form_id: form_id};
				ObjectTab.id = tabID;
				ObjectTab.css_class = "";
				ObjectTab.image = "";
				ObjectTab.override_folder = "";
				ObjectTab.active_tab = "0";
				titleTab = $.trim($(tab).find('a').html());
				Object.keys($globalthis.languages).forEach(function (key) {
					ObjectTab["title_" + $globalthis.languages[key]] = titleTab;
					ObjectTab["sub_title_" + $globalthis.languages[key]] = "";
				});
				$(tab).find('a').data("form", ObjectTab);

				let tabContent = $("#default_DeoTabs").find('.tab-content > .tab-pane').first().clone(1);
				tabContent.removeClass('active');
				tabContent.attr('id', tabID);
				tabContent.find('.subwidget-content').html('');
				$('.tab-pane', $(widget)).removeClass('active');
				$(tabContent).addClass('active');
				$('.tab-content', $(widget)).append(tabContent);

				$(tab).tab('show');
				$(tab).trigger('click');
				$(tab).addClass('active');

				$globalthis.setActiveSub(widget, false);

				return false;
			});

			$(document).on("click", ".btn-add-accordion", function () {
				let widget = $(this).closest('.widget-row');
				//nav-tabs tab-content
				let panel = $(this).closest('.panel-group');
				//$('.panel-collapse', $(panel)).collapse();
				let panelDefault = $("#default_DeoAccordions").find('.panel-default').first().clone(1);
				// let parent = $(panel).find('.panel-default').first().find(".panel-title a").data("parent");
				let collapseID = "collapse-" + $globalthis.getRandomNumber();
				let form_id = "form_"+$globalthis.getRandomNumber();
				$('.panel-title a', $(panelDefault)).attr('href', "#" + collapseID);
				$('.panel-title a', $(panelDefault)).attr('class', form_id);
				$('.panel-title a', $(panelDefault)).html('New Accordion');
				// $('.panel-title a', $(panelDefault)).data("parent", parent.replace("#", ""));
				$('.panel-collapse', $(panelDefault)).attr('id', collapseID);
				$('.panel-collapse .subwidget-content', $(panelDefault)).html('');

				// ObjectForm = $globalthis.assignDataForm($(panel).find('.panel-default').first().find(".panel-title a"), collapseID);
				// ObjectForm = $globalthis.assignDataForm($('.panel-title a',$(panelDefault)), collapseID);
				let ObjectAccordion = {form_id : form_id};
				ObjectAccordion.parent_id = $globalthis.getRandomNumber();
				ObjectAccordion.id = collapseID;
				ObjectAccordion.active_accordion = "0";
				ObjectAccordion.active_type = "set";
				// ObjectForm['title_1'] = 'New Accordion';
				let titleAccordion = $.trim($(panelDefault).find('.panel-title a').html());
				Object.keys($globalthis.languages).forEach(function (key) {
					ObjectAccordion["title_" + $globalthis.languages[key]] = titleAccordion;
					ObjectAccordion["sub_title_" + $globalthis.languages[key]] = "";
				});
				// ObjectForm['title_' + $globalthis.lang_id] = "New Accordion";
				$('.panel-title a', $(panelDefault)).data('form', ObjectAccordion);
				$(this).closest('.accordion-content-control').before(panelDefault);

				$globalthis.setActiveSub(widget, false);
			});

			$(document).on("click", ".btn-duplicate", function () {
				parent = $(this).parent().parent();
				//dublicate widget
				if ($(parent).hasClass('widget-row')) {
					if ($(this).hasClass('widget-action')) {
						duplicate = $(parent).clone(1);
						ObjectForm = $globalthis.assignDataForm(duplicate);
						$(duplicate).data('form', ObjectForm);
						$(parent).parent().append(duplicate);
					}
				}

				//duplicate accordion
				if ($(parent).hasClass('accordion-panel')) {
					panel = $(parent).closest('.panel').clone(1);
					panelGroup = $(parent).closest('.panel-group');
					$globalthis.changWidgetFormID(panel);
					$globalthis.changeAccordionPanel(panel);

					$(panelGroup).parent().find('.btn-add-accordion').before(panel);
				}

				//duplicate accordions
				if ($(parent).hasClass("DeoAccordions")) {
					widgetRow = $(parent).clone(1);
					accId = "accordion_" + $globalthis.getRandomNumber();
					ObjectForm = $globalthis.assignDataForm(widgetRow, accId);

					$(widgetRow).data('form', ObjectForm);
					$(widgetRow).attr('id', accId);
					$(widgetRow).attr('class', 'widget-row DeoAccordions ' + $globalthis.classWidget + ' ' + ObjectForm.form_id);

					$globalthis.changWidgetFormID(widgetRow);
					$globalthis.changeAccordionPanel(widgetRow, accId);

					$(parent).closest('.column-content').append(widgetRow);
				}

				//duplicate popup
				if ($(parent).hasClass("DeoPopup")) {
					widgetRow = $(parent).clone(1);
					accId = "popup_" + $globalthis.getRandomNumber();
					ObjectForm = $globalthis.assignDataForm(widgetRow, accId);

					$(widgetRow).data('form', ObjectForm);
					$(widgetRow).attr('id', accId);
					$(widgetRow).attr('class', 'widget-row DeoPopup ' + $globalthis.classWidget + ' ' + ObjectForm.form_id);

					$globalthis.changWidgetFormID(widgetRow);
					$globalthis.changeAccordionPanel(widgetRow, accId);

					$(parent).closest('.column-content').append(widgetRow);
				}

				//duplicate tab
				if ($(parent).hasClass('tab-pane')) {
					widgetRow = $(parent).closest('.widget-row');
					//duplicate tab content
					tabContent = $(parent).clone(1);
					tabId = "tab_" + $globalthis.getRandomNumber();
					$globalthis.changWidgetFormID(tabContent);
					hrefOld = "#" + tabContent.attr('id');
					$(tabContent).attr('id', tabId);
					$(parent).closest('.tab-content').append(tabContent);
					$('.tab-pane', $(parent).removeClass('active'));
					$(tabContent).addClass('active');
					$(parent).parent().append(tabContent);

					//duplicate a
					tabTile = $(widgetRow).find('a[href*="' + hrefOld + '"]').parent().clone(1);
					tab = $(tabTile).find('a').first();
					$(tab).attr('href', '#' + tabId);
					ObjectForm = $globalthis.assignDataForm(tab, tabId);
					$(tab).data('form', ObjectForm);

					$(parent).closest('.widget-row').find('.tab-button').before(tabTile);

					$(tab).tab('show');
					$(tab).trigger('click');
					$(tab).addClass('active');
				}

				//duplicate tabs
				if ($(parent).hasClass('DeoTabs')) {
					widgetRow = $(parent).clone(1);
					ObjectForm = $globalthis.assignDataForm(widgetRow);
					$(widgetRow).data('form', ObjectForm);
					$(widgetRow).attr('class', 'widget-row DeoTabs ' + $globalthis.classWidget + ' ' + ObjectForm.form_id);
					$globalthis.changWidgetFormID(widgetRow);

					$globalthis.changeTabs(widgetRow);

					$(parent).closest('.column-content').append(widgetRow);
				}
				//duplicate column
				if ($(parent).hasClass('column-controll-top')) {
					let parentColumn = $(parent).closest(".column-row");
					column = $(parentColumn).clone(1);
					column = $globalthis.changeDatacolumn(column);
					$(parentColumn).parent().append(column);
				}
				//duplicate group
				if ($(parent).hasClass('group-row')) {
					let parentGroup = $(parent).closest(".group-row");
					group = $(parentGroup).clone(1);
					ObjectForm = $globalthis.assignDataForm(group);
					$(group).data('form', ObjectForm);
					$('.column-row', $(group)).each(function () {
						$globalthis.changeDatacolumn(this);
					});

					$(parentGroup).parent().find('.hook-content-footer').before(group);
				}
				$('.label-tooltip', $($(parent).parent())).tooltip('disable');
				$('.tooltip', $($(parent).parent())).remove();
			});

			$(document).on("click", ".choose-img-extend", function (e) {
				e.preventDefault();
				let link = $(this);
				// Store object image for hold the destination after select back
				imgId = $(link).data("for");
				$.ajax({
					url: $(link).attr("href"),
					beforeSend: function () {
						$("#deo_loading").show();
					},
					success: function (response) {
						$("#modal_select_image .modal-body").html(response);
						$("#modal_select_image .modal-body").css('min-height', $(window).height() * 0.8);
						$("#modal_select_image").modal('show');
						$(".image-manager").addClass('extend');
						$(".img-link").tooltip();
					},
					complete: function () {
						$("#deo_loading").hide();
					}
				});
				return false;
			});
			$(document).on("click", ".selectImg.lang .reset-img", function (e) {
				e.preventDefault();

				$(this).closest('.translatable-field').find('.img-thumbnail').attr('src', '');
				$(this).closest('.translatable-field').find('.img-value').attr('value', '_JS_EMPTY_VALUE_');
				$(this).closest('.translatable-field').find('.img-thumbnail').hide();
				$('.calculate-rate-image').trigger('click');
				return false;
			});

			$('#modal_select_image').on('hidden.bs.modal', function () {
				$(".image-manager.extend").removeClass('extend');
			});
			
			$(document).on("click", ".image-manager.extend .img-link", function (e) {
				e.stopPropagation();
				let img = $(this).find("img");
				$("#s-image").removeClass("hidden");
				let name = $(img).attr("src");
				$(imgId).val($(img).attr("data-folder")+'/'+$(img).attr("data-name"));
				

				let div = $(imgId).closest("div");
				imgDest = $(div).find("img");
				
				let widget = $(img).attr("data-widget");
				if(widget == "DeoImage360"){
					// ADD code Image 360 : insert image to form
					let idRow = 1;
					let arr = $("#total_slider").val().split("|");
					arr.sort(function(a, b) { return a - b; });
					for(let i = 0; i < arr.length; i++) {
						if(idRow != arr[i]) {
							break;
						}
						idRow++;
					}

					let image_name = "image360_" +  idRow;
					let html = '';
					html += '<div class="col-lg-9">';
					html += '    <div class="col-lg-5"><img data-position="" data-name="' +$(img).attr("data-name")+ '" class="img-thumbnail" src="' + name + '">';
					html += '    <input type="hidden" value="' +$(img).attr("data-name")+ '" class="DeoImage360" id="'+image_name+'" name="'+image_name+'"></div>';
					html += '<div class="col-lg-4">'+$(img).attr("data-name")+'</div>';
					html += '</div>';
					html += '<div class="col-lg-3" style="text-align: right;">';
					html += '    <button type="button" class="btn-delete-fullslider btn btn-danger"><i class="icon-trash"></i> Delete</button>';
					html += '</div>';
					$("#list-slider").append("<li id='" + idRow + "'>" + html + "</li>");
					updateListIdFullSlider();
				}else{

					if (imgDest.length > 0){
						$(imgDest).attr("src", $(img).attr("src"));
						$(imgDest).data("img", $(img).data("name"));
						$(imgDest).show();
						if ($(imgDest).attr("widget") === "DeoCategoryImage"){
							$(imgDest).closest(".list-image").find(".remove-img").removeClass("hidden");
							$(imgDest).removeClass("hidden");
							$(imgDest).attr("src-url", $(img).attr("data-folder")+'/'+$(img).attr("data-name"));
							$(imgDest).data('img', $(img).attr("data-folder")+'/'+$(img).attr("data-name"));
							updateStatusCheck(imgDest);
						}else{
							
						}
					}else{
						$(div).prepend("<img src='" + $(img).attr("src") + "' class='img-thumbnail' data-img='" + $(img).attr("data-name") + "'/>");
					}
				}

				
				$("#modal_select_image").modal('hide');
				setTimeout(function(){
					$('.calculate-rate-image').trigger('click');
				}, 1000);
				
				return false;
			});
			$(document).on("click", ".remove-img", function (e) {
				e.stopPropagation();
				let img = $(this).closest(".list-image").find("img");
				$(img).attr("src-url", "");
				$(img).attr("src", "");
				$(img).addClass("hidden");

				updateStatusCheck(img);
			});
			$(".tree-folder-name input:checkbox").change(function () {
				$(this).find("input:checkbox").removeAttr("checked");
			});
			// add event for section select animation to group and column
			$(document).on("click", ".animation-button", function (e) {
				let animation_wrapper = $(this).siblings('.animation-wrapper');
				if (!$(this).hasClass('active')){				
					$(".animation-button.active").siblings('.animation-wrapper').hide();
					$(".animation-button.active").removeClass('active');
					// load config by data				
					$(this).addClass('active');
					let animation_type = $(this).data('animation-type');
					let animation_delay = $(this).data('animation-delay');					
					let animation_duration = $(this).data('animation-duration');
					let animation_iteration_count = $(this).data('animation-iteration-count');
					let animation_infinite = $(this).data('animation-infinite');
					
					if (typeof animation_delay != 'undefined'){
						animation_wrapper.find('.animation_delay').val(animation_delay);
					}else{
						animation_wrapper.find('.animation_delay').val(1);
					}
					
					if (typeof animation_duration != 'undefined'){
						animation_wrapper.find('.animation_duration').val(animation_duration);
					}else{
						animation_wrapper.find('.animation_duration').val(1);
					}
					
					if (typeof animation_iteration_count != 'undefined'){
						animation_wrapper.find('.animation_iteration_count').val(animation_iteration_count);
					}else{
						animation_wrapper.find('.animation_iteration_count').val(1);
					}
									
					if (animation_infinite == 1){
						animation_wrapper.find('.animation_infinite').attr( 'checked', 'checked' );
					}else{
						animation_wrapper.removeAttr('checked');
					}
					// change offset to right with column small					
					if ($(window).width()-$(this).offset().left < animation_wrapper.width()){
						animation_wrapper.addClass('offset-right');
					}
					animation_wrapper.show();
					
					if (typeof animation_type != 'undefined'){
						animation_wrapper.find('.animation_select').val(animation_type).trigger('change');	
					}else{
						animation_wrapper.find('.animation_select').val('none').trigger('change');	
					}
					
					// animation_wrapper.find('.animate-it').trigger('click');
										
				}else{
					$(this).removeClass('active');
					animation_wrapper.hide();
					animation_wrapper.removeClass('offset-right');
					animation_wrapper.find('.animationSandbox').removeClass().removeAttr('style').addClass('animationSandbox');
				}
				
			});
			
			// save config of animation to data form of column/group
			$(document).on("click", ".btn-save-animation", function (e) {
				let obj_parent = $(this).parents('.animation-wrapper');
				let animation_bt = obj_parent.siblings('.animation-button');
				let animation_type = obj_parent.find('.animation_select').val();
				let animation_delay = obj_parent.find('.animation_delay').val();
				let animation_duration = obj_parent.find('.animation_duration').val();
				let animation_iteration_count = obj_parent.find('.animation_iteration_count').val();
				let animation_infinite = obj_parent.find('.animation_infinite').is(':checked')? 1 : 0;
				
				$globalthis.assignConfigAnimation(animation_bt, animation_type, animation_delay, animation_duration, animation_iteration_count, animation_infinite);
				
				// update data form for group/column
				if (obj_parent.hasClass('column-animation-wrapper')){				
					let main_obj = obj_parent.parents('.column-row');
				}
				if (obj_parent.hasClass('group-animation-wrapper')){					
					let main_obj = obj_parent.parents('.group-row');
				}
				if (typeof main_obj != 'undefined'){
					main_obj.data('form').animation = animation_type;
					main_obj.data('form').animation_delay = animation_delay;
					main_obj.data('form').animation_duration = animation_duration;
					main_obj.data('form').animation_iteration_count = animation_iteration_count;
					main_obj.data('form').animation_infinite = animation_infinite;
				}
				
				animation_bt.trigger('click');
			});
			
			// hide section select animation for column and group when click out
			$(document).on("click", function (e) {
				if ($('.animation-button.active').length){
					e.stopPropagation();
					let container = $('.animation-wrapper');
					let container2 = $('.animation-button');
										
					if (container.length && container.has(e.target).length === 0 && container2.has(e.target).length === 0 && !$(e.target).hasClass('animation-button') && !$(e.target).hasClass('animation-wrapper')) {						
						// container.hide();						
						// $('.animation-button.active').siblings('.animation-wrapper').find('.animationSandbox').removeClass().removeAttr('style').addClass('animationSandbox');
						// $('.animation-button.active').removeClass('active');	
						$('.animation-button.active').trigger('click');
					}
				}			
			});
			
			// active button for section select animation for column and group
			$(document).on("change", '.animation_select', function (e) {
				let wrapper_obj = $(this).parents('.animation-wrapper');
				if ($(this).val() == "none") {
					wrapper_obj.find('.animate_sub').hide();
				} else {
					wrapper_obj.find('.animate_sub').show();
					let duration_time = wrapper_obj.find('.animation_duration').val();
					let delay_time = wrapper_obj.find('.animation_delay').val();
					if (wrapper_obj.find('.animation_infinite').is(':checked')){
						let iteration_number = 'infinite';
					}else{
						let iteration_number = wrapper_obj.find('.animation_iteration_count').val();
					}					
					
					wrapper_obj.find('.animationSandbox').removeClass().removeAttr('style').attr('style','animation-duration: '+duration_time+'s; animation-delay: '+delay_time+'s; animation-iteration-count: '+iteration_number).addClass($(this).val() + ' animated animationSandbox').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
						$(this).removeClass().removeAttr('style').addClass('animationSandbox');
					});
				}
			});
			
			// run demo with current config
			$(document).on("click", '.animate-it', function (e) {
				let wrapper_obj = $(this).parents('.animation-wrapper');
				wrapper_obj.find('.animation_select').trigger('change');
			});
			
			// copy to clipboard
			$(document).on("click", '.bt_copy_clipboard', function (e) {							
				let text_copy = '';
				
				if ($(this).hasClass('shortcode_key')){
					text_copy = $('#shortcode_key').val();
				};
				if ($(this).hasClass('shortcode_embedded_hook')){
					text_copy = $('#shortcode_embedded_hook').val();
				};
				if ($(this).hasClass('shortcode_embedded_code')){
					text_copy = $('#shortcode_embedded_code').val();
				};
				
				if (text_copy != ''){
					let $temp = $("<input>");
					$("body").append($temp);			
					$temp.val(text_copy).select();
					document.execCommand("copy");
					showSuccessMessage('Copy successful');
					$temp.remove();
				}
			});		
				
		};
		
		// assign config to data form column/group
		this.assignConfigAnimation = function (obj_bt, data_type, data_delay, data_duration, data_iteration, data_infinite) {		
			obj_bt.data('animation-type', data_type);
			obj_bt.data('animation-delay', data_delay);
			obj_bt.data('animation-duration', data_duration);
			obj_bt.data('animation-iteration-count', data_iteration);
			obj_bt.data('animation-infinite', data_infinite);
			let txt_default = obj_bt.find('.animation-status').data('text-default');
			if (data_type != 'none'){				
				obj_bt.addClass('btn-success');							
				let txt_infinite = obj_bt.find('.animation-status').data('text-infinite');
				obj_bt.find('.animation-status').text(data_type + (data_infinite == 1 ? ' ('+txt_infinite+')' : ''));
			}else{
				obj_bt.removeClass('btn-success');
				obj_bt.find('.animation-status').text(txt_default);
			}
		};

		this.changeDatacolumn = function (column) {
			let $globalthis = this;
			ObjectForm = $globalthis.assignDataForm(column);
			$(column).data('form', ObjectForm);
			$('.widget-row', $(column)).each(function () {
				widgetRow = $(this);
				if ($(this).hasClass('DeoAccordions')) {
					accId = "accordion_" + $globalthis.getRandomNumber();

					ObjectForm = $globalthis.assignDataForm(widgetRow, accId);

					$(widgetRow).data('form', ObjectForm);
					$(widgetRow).attr('id', accId);
					$(widgetRow).attr('class', 'widget-row DeoAccordions ' + $globalthis.classWidget + ' ' + ObjectForm.form_id);

					$globalthis.changeAccordionPanel(widgetRow, accId);
				} else {
					ObjectForm = $globalthis.assignDataForm(widgetRow);
					$(widgetRow).data('form', ObjectForm);

					if ($(this).hasClass('DeoTabs')) {
						$(widgetRow).attr('class', 'widget-row DeoTabs ' + $globalthis.classWidget + ObjectForm.form_id);
						$globalthis.changeTabs(widgetRow);
					}
				}
			});

			return column;
		};
		this.returnWidthClass = function (width) {
			$globalthis = this;
			if (!width)
				width = $globalthis.windowWidth;
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
		this.getClassColumn = function (objCol) {
			$globalthis = this;
			classColumn = 'column-row ' + $globalthis.classWidget;
			for (ic = 0; ic < $globalthis.arrayCol.length; ic++) {
				if (objCol[$globalthis.arrayCol[ic]]) {
					valueCol = objCol[$globalthis.arrayCol[ic]];
					if (valueCol.toString().indexOf(".") != -1) {
						valueCol = valueCol.toString().replace(".", "-");
					}
					classColumn += " col-" + $globalthis.arrayCol[ic] + "-" + valueCol;
				}
			}
			return classColumn;
		};
		this.changWidgetFormID = function (panel) {
			let $globalthis = this;
			$('.widget-row', $(panel)).each(function () {
				let ObjectForm = {form_id: "form_" + $globalthis.getRandomNumber()};
				dataForm = $(this).data("form");
				Object.keys(dataForm).forEach(function (key) {
					if (key != 'form_id')
						ObjectForm[key] = dataForm[key];
				});

				$(this).data('form', ObjectForm);
			});
		};
		this.assignDataForm = function (element, id) {
			let $globalthis = this;
			dataForm = $(element).data("form");
			let ObjectForm = {form_id: "form_" + $globalthis.getRandomNumber()};
			Object.keys(dataForm).forEach(function (key) {
				if (key != 'form_id') {
					if (id && key == 'id')
						ObjectForm[key] = id;
					else
						ObjectForm[key] = dataForm[key];
				}
			});
			return ObjectForm;
		};
		this.changeTabs = function (widget) {
			let $globalthis = this;
			$('.widget-container-heading li a[data-toggle="tab"]', $(widget)).each(function () {
				if ($(this).parent().attr("id") != "default_tabnav" && !$(this).parent().hasClass("tab-button")) {
					OldHref = $(this).attr('href').replace('#', '');
					tabID = "tab_" + $globalthis.getRandomNumber();
					$(this).attr('href', "#" + tabID);
					ObjectForm = $globalthis.assignDataForm(this, tabID);
					$(this).data('form', ObjectForm);
					$(widget).find('.tab-pane').each(function () {
						if ($(this).attr('id') == OldHref) {
							$(this).attr('id', tabID);
							return false;
						}
					});

					accId = "accordion_" + $globalthis.getRandomNumber();
					ObjectForm = $globalthis.assignDataForm(widgetRow, accId);

					$(widgetRow).data('form', ObjectForm);
					$(widgetRow).attr('id', accId);
					$(widgetRow).attr('class', 'widget-row DeoAccordions ' + $globalthis.classWidget + ' ' + ObjectForm.form_id);

					$globalthis.changWidgetFormID(widgetRow);
					$globalthis.changeAccordionPanel(widgetRow, accId);
				}
			});
		};
		this.changeAccordionPanel = function (panel, accId) {
			let $globalthis = this;
			$('.panel-title a', $(panel)).each(function () {
				newHref = "collapse_" + $globalthis.getRandomNumber();
				ObjectForm = $globalthis.assignDataForm($(this), newHref);
				if (accId) {
					ObjectForm.parent_id = accId;
					$(this).data('parent', '#' + accId);
				}
				$(this).data('form', ObjectForm);
				$(this).attr('class', ObjectForm.form_id);
				oldHref = $(this).attr('href').replace('#', '');

				$(this).attr('href', '#' + newHref);

				$(panel).find('.panel-collapse').each(function () {
					if ($(this).attr('id') == oldHref) {
						$(this).attr('id', newHref);
						return false;
					}
				});
			});
		};
		this.getRandomNumber = function () {
			return (+new Date() + (Math.random() * 10000000000000000)).toString().replace('.', '');
		};
		this.testAnim = function (x) {
			$('#animationSandbox').removeClass().addClass(x + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
				$(this).removeClass();
			});
		};
		// AJAX LOAD FORM, LOAD WIDGET
		this.setFormAction = function () {
			let $globalthis = this;

			$('.form-action').change(function(){
				let elementName = $(this).attr('name');
				let sub_hide = '.' + elementName + '_sub';
				let sub_show = '.' + elementName + '-' + $(this).val();
				$(sub_hide).hide(400);
				$(sub_show).show(400);
				toogle_carousel(sub_show);
			});

			toogle_logo();
			toogle_popup();
			toogle_video();
			toogle_image_360();
			toogle_background_row();
			toogle_limit_twitter();
			toogle_google_map();
			toogle_link_viewall_category_image();
			$(".image-choose").DeoImageSelector();

			// Show tool tip, hint of label
			$("#modal_form .label-tooltip").tooltip();

			if ($('select[name="bg_config"]').length) {
				$('select[name="bg_config"]').change(function () {
					if ($(this).val() == "fullwidth") {
						if ($("#container").val() == "") {

							bgType = $('select[name="bg_type"] option');
							bgType.prop('selected', false);
							bgType.find('option[value="normal"]').prop('selected', true);

							$('select[name="bg_type"] option').each(function () {
								if ($(this).val() != "normal" && $(this).val() != "fixed")
									$(this).attr('disabled', 'disabled');
							});
						} else {
							$('select[name="bg_type"] option').each(function () {
								if ($(this).val() != "normal" && $(this).val() != "fixed")
									$(this).removeAttr('disabled', 'disabled');
							});
						}
					} else {
						$('select[name="bg_type"] option').each(function () {
							if ($(this).val() != "normal" && $(this).val() != "fixed")
								$(this).removeAttr('disabled', 'disabled');
						});
					}
				});
				$("#container").change(function () {
					$('select[name="bg_config"]').trigger("change");
				})
				$('select[name="bg_config"]').trigger("change");
			}

			$('.checkbox-group').change(function () {
				$globalthis.showOrHideCheckBox($(this));
			});

			$('.width-select').click(function () {
				btnGroup = $(this).closest('.btn-group');
				spanObj = $('.width-val', $(this));
				width = $(spanObj).data('width');
				$('.col-val', $(btnGroup)).val(width);
				$('.deo-btn-width .width-val', $(btnGroup)).html($(spanObj).html());
				$('.deo-btn-width .width-val', $(btnGroup)).attr('class', $(spanObj).attr('class'));
			});
			if ($('.deo-tab-config').length > 0) {
				//set tab aciton
				$('.deo-tab-config').each(function () {
					if (!$(this).parent().hasClass('active')) {
						element = $(this).attr('href').toString().replace("#", ".");
						$(element).hide();
					}
				});

				$('.deo-tab-config').click(function () {
					divElement = $(this).attr('href').toString().replace("#", ".");
					aElement = $(this);
					$('.deo-tab-config').each(function () {
						if ($(this).parent().hasClass('active')) {
							element = $(this).attr('href').toString().replace("#", ".");
							$(this).parent().removeClass('active');
							$(element).hide();
							return false;
						}
					});
					$(divElement).show();
					$(aElement).parent().addClass('active');

					$('.form-action', $(divElement)).each(function () {
						$(this).trigger("change");
					});

					$('.checkbox-group', $(divElement)).each(function () {
						$globalthis.showOrHideCheckBox($(this));
					});

					// if ($(this).attr('href') == "#aprow_animation" && $('#animation').length > 0)
						// $('#animation').trigger("change");

				});
			}

			$('textarea.autoload_rte').each((index, textarea) => {
		    if (window.tinyMCE) {
		      const editor = window.tinyMCE.get(textarea.id);

		      if (editor) {
		      	console.log(textarea.id)
		        // Reset content to force refresh of editor
		        editor.setContent(editor.getContent());
		      }
		    }
		  });

			if ($('.em_text').length > 0) {
				//page in column form
				$('.em_text').change(function () {
					let list = $(this).closest('.well').find('.em_list');
					let values = "";
					if ($(this).val())
						values = $(this).val().split(',');
					let len = values.length;

					list.find('option').prop('selected', false);
					for (let i = 0; i < len; i++)
						list.find('option[value="' + $.trim(values[i]) + '"]').prop('selected', true);
				});
				$('.em_list').change(function () {
					if ($(this).val()) {
						let str = $(this).val().join(', ');
						let text = $(this).closest('.well').find('.em_text');
						$(text).val(str);
					}
				});
			}

			if ($('#animation').length > 0) {
				$('#animation').wrap('<div class="input-group fixed-width-xl"></div>');
				$('#animation').after('<span class="input-group-btn"><button type="button" class="btn btn-default animate-it animate_sub">Try</button></span>');
				$('.animate-it').click(function () {
					$('#animation').trigger("change");
				});
				if ($('#animation').val() == "none") {
					$('.animate_sub').attr("disabled", true);
				}
				$('#animation').change(function () {
					if ($(this).val() == "none") {
						$('.animate_sub').attr("disabled", true);
					} else {
						$('.animate_sub').removeAttr("disabled");
						$globalthis.testAnim($(this).val());
					}
				}); 
			}

			$('.calculate-rate-image').click(function () {
				$('.virtual-image').empty();
				let lengthLang = Object.keys($globalthis.languages).length;

				if ($(this).data('widget') === 'DeoCategoryImage'){
					let array_rate_image = {};
					$(".list-image", $(".form-select-icon")).each(function() {
						let checkbox = $(this).closest("span").find("input[type='checkbox']").first();
						let img = $(this).find('img').first();
						if(img.attr("src-url") != '') {
							let rate_image = calculate_rate_image(img.prop('naturalWidth'),img.prop('naturalHeight'));
							rate_image = rate_image ? rate_image : 0;
							array_rate_image[$(checkbox).val()] = rate_image;
						}
					});
					$("#rate_image").val(JSON.stringify(array_rate_image));
				}else{
					if (lengthLang > 1) {
						if($('.select_image').hasClass('no_lang')){
							$('#rate_image').val(calculate_rate_image($('.select_image.no_lang .selectImg .img-thumbnail').prop('naturalWidth'),$('.select_image.no_lang .selectImg .img-thumbnail').prop('naturalHeight')));
						}else{
							$('.select_image .selectImg .translatable-field').each(function() {
								let id_lang = $(this).data('lang');
								let image = $(this).find('.img-thumbnail');
								let src = image.attr('src');
								if(src != ''){
									image.clone().attr('data-lang',id_lang).appendTo($('.btn_calculate_rate_image .virtual-image'));
									let virtual_image = $('.btn_calculate_rate_image .virtual-image .img-thumbnail[data-lang="' + id_lang +'"]');
									let rate_image = calculate_rate_image(virtual_image.prop('naturalWidth'),virtual_image.prop('naturalHeight'));
									rate_image = rate_image ? rate_image : 0;

									$('#rate_image_' + id_lang).val(rate_image);
								}else{
									$('#rate_image_' + id_lang).val(0);
								}
							});
							$('.select_image_temp .selectImg .translatable-field').each(function() {
								let id_lang = $(this).data('lang');
								let image = $(this).find('.img-thumbnail');
								let src = image.attr('src');
								if(src != ''){
									image.clone().attr('data-lang',id_lang).appendTo($('.btn_calculate_rate_image_temp .virtual-image'));
									let virtual_image = $('.btn_calculate_rate_image_temp .virtual-image .img-thumbnail[data-lang="' + id_lang +'"]');
									let rate_image = calculate_rate_image(virtual_image.prop('naturalWidth'),virtual_image.prop('naturalHeight'));
									rate_image = rate_image ? rate_image : 0;

									$('#temp_rate_image_' + id_lang).val(rate_image);
								}else{
									$('#temp_rate_image_' + id_lang).val(0);
								}
							});
						}
					} else {
						let image = $('.select_image .selectImg .img-thumbnail');
						let imageTemp = $('.select_image_temp .selectImg .img-thumbnail');
						let src = image.attr('src');
						let srcTemp = imageTemp.attr('src');
						if(src != '' || srcTemp != ''){
							let rate_image = calculate_rate_image(image.prop('naturalWidth'),image.prop('naturalHeight'));
							rate_image = rate_image ? rate_image : 0;

							$('#rate_image').val(rate_image);
							$('#temp_rate_image').val(rate_image);
						}else{
							$('#rate_image,#temp_rate_image').val(0);
						}
					}
				}
			});

			if ($('.form-action').length > 0 || $('.checkbox-group').length) {
				if ($("#configuration_form .nav-tabs").length)
					$("#configuration_form .nav-tabs li.active a").trigger("click");
				else {
					$('.form-action').trigger("change");
					$('.checkbox-group').each(function () {
						$globalthis.showOrHideCheckBox($(this));
					});
				}
			}

			if ($(".select-class").length) {
				$(".select-class").change(function () {
					let classChk = $(this).attr("name");
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
					let elementChk = $(this).closest('.form-group').find('input[type=checkbox]');
					let classText = $(this).val();

					$(elementChk).each(function () {
						let classChk = $(this).attr("name");
						let regex =  new RegExp("\\b"+classChk+"\\b\(\?\!\-\)", "g"); 
						if (regex.test(classText)) {
							if (!$(this).is(':checked'))
								$(this).prop("checked", true);
						} else {
							$(this).prop("checked", false);
						}
					});

					if (classText.indexOf($(".chk-row").data('value')) != -1) {
						if (!$(".chk-row").is(':checked'))
							$(".chk-row").prop("checked", true);
					} else {
						$(".chk-row").prop("checked", false);
					}
				});
				$(".element_class").trigger("change");
			}

			//$('.new-shortcode').click(function() {
			// $(".cover-short-code").click(function () {
			$('#modal_form').on("click", ".cover-short-code", function () {
				let a = $(this).find(".new-shortcode");
				let tab = $(a).hasClass("module") ? "module" : "widget";
				$(".btn-back-to-list").attr("tab", tab);
				// Add widget
				url = $globalthis.ajaxShortCodeUrl + "&addnew&type_shortcode="
						+ $(a).data("type") + "&type=" + tab;

				data = "";
				$("#deo_loading").show();
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: url,
					async: true,
					cache: false,
					data: data,
					success: function (data) {
						$("#modal_form .txt-search").hide();
						$('#myModalLabel').html($('#myModalLabel').data('addnew'));
						$("#deo_loading").hide();
						$('#modal_form').addClass('new-short-code');
						$('#modal_form .modal-footer').show();
						$('#modal_form .modal-body').html(data);
						$('#myModalLabel').html($('#myModalLabel').html() + ' : ' + $('.modal-widget-title').html());
						resetSelectedImage();
						if ($(a).data("type") == "DeoBlockCarousel") {
							initFullSlider("add");
						}
						hideFormLevel2();
						$globalthis.setFormAction();
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						$("#deo_loading").hide();
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			});
			if ($("#list-slider").length > 0) {
				$("#list-slider").sortable({
					accept: "div",
					// cursor: 'move',
					update: function () {
						let listId = "";
						let sep = "";
						$("#list-slider li").each(function () {
							let id = (typeof $(this).attr("id") != "undefined") ? $(this).attr("id") : "";
							if (id) {
								listId += sep + id;
								sep = "|";
							}
						});
						$("#total_slider").val(listId);
					}
				});
			}
		};
		this.initControllInRow = function () {
			$globalthis = this;
			$('.btn-custom').popover({
				html: true,
				content: function () {
					$globalthis.currentElement = $('.group-content', $(this).closest('.group-row'));
					return $('#addnew-group-form').html();
				}
			});
			$('.btn-custom').on('shown.bs.popover', function () {
				$('.number-column').click(function () {
					widthCol = $(this).data('width');
					classActive = $globalthis.returnWidthClass();
					realValue = widthCol.toString().replace('.', '-');
					$('.column-row', $($globalthis.currentElement)).each(function () {
						ObjColumn = $(this).data('form');
						oldClass = ObjColumn[classActive].toString().replace('.', '-');
						if (classActive == "md" || classActive == "lg" || classActive == "xl" || classActive == "xxl") {
							classColumn = $(this).attr('class').replace('col-xxl-' + oldClass, 'col-xxl-' + realValue).replace('col-xl-' + oldClass, 'col-xl-' + realValue).replace('col-lg-' + oldClass, 'col-lg-' + realValue).replace('col-md-' + oldClass, 'col-md-' + realValue);
							ObjColumn.md = ObjColumn.lg = ObjColumn.xl = ObjColumn.xxl = widthCol;
						} else {
							classColumn = $(this).attr('class').replace('col-' + classActive + '-' + oldClass, 'col-' + classActive + '-' + realValue);
							ObjColumn[classActive] = widthCol;
						}

						$(this).attr('class', classColumn);
						$(this).data('form', ObjColumn);
						$globalthis.getNumberColumnInClass(this, classActive);
					});
				});
			});

			let popOverAddColumnSettings = {
				placement: 'left',
				html: true,
				container: 'body',
				trigger: 'focus',
				selector: '.btn-add-column',
				content: function () {
					$globalthis.currentElement = $('.group-content', $(this).closest('.group-row'));
					return $('#addnew-column-form').html();
				}
			};
			$('body').popover(popOverAddColumnSettings);

			$('.btn-add-column').on('shown.bs.popover', function () {
			});

			btn_new_widget_group('.btn-new-widget-group');

		}
		this.initIsotopAction = function ($parent = null, $reloadModule = false) {
			let $containerWidget = $parent.find(".widget_container");
			let $containerModule = $parent.find(".module_container");
			
			if ($reloadModule){
				$containerModule.isotope('destroy');
			}else{
				$parent.find('.nav-tabs .widget a').tab('show');
			}

			// function searchWidget(search) {
			// 	let tab = $parent.find('.tab-list li.active a').data('controls');
			// 	$parent.find(".for-" + tab + " a").removeClass("is-checked");
			// 	$parent.find(".for-" + tab + " a.all").addClass("is-checked");

			// 	// Detect and search by name
			// 	let container = (tab === "widget") ? $containerWidget : $containerModule;
			// 	container.isotope({
			// 		filter: function () {
			// 			if (search === "") {
			// 				return true;
			// 			} else {
			// 				let label = $(this).find(".name").text().toLowerCase() + " " + $(this).find(".desc").text().toLowerCase();
			// 				return label.search(search) !== -1;
			// 			}
			// 		}
			// 	});
			// }

			// if ($parent.find('.txt-search').length){
			// 	$parent.find('.txt-search').val('');
			// 	$parent.find('.txt-search').trigger('keyup');
			// 	searchWidget($parent.find('.txt-search').val().toLowerCase());
			// }

			$('.nav-container').on("keyup", ".txt-search", function () {
				let nav_container = $(this).closest('.nav-container');
				let search = $(this).val().toLowerCase();
				// searchWidget($(this));
				let activeTab = nav_container.find('.tab-list li.active a');
				let controls = activeTab.data('controls');
				let container = nav_container.find(".tab-pane-" + controls).find('.container-fillter');
				// let filterValue = nav_container.find(".for-" + controls + " .is-checked").data("filter");
				// let tab = $parent.find('.tab-list li.active a').data('controls');
				// $parent.find(".for-" + tab + " a").removeClass("is-checked");
				// $parent.find(".for-" + tab + " a.all").addClass("is-checked");
				if (nav_container.find(".for-" + controls + " a.all").length == 0){
					nav_container.find(".for-" + controls + " a").removeClass("is-checked");
					nav_container.find(".for-" + controls + " a.all").addClass("is-checked");
				}

				container.isotope({
					filter: function () {
						if (search === "") {
							return true;
						} else {
							let label = $(this).find(".widget-name").text().toLowerCase() + " " + $(this).find(".widget-desc").text().toLowerCase() + " " + $(this).data("tag").toLowerCase();
							return label.search(search) !== -1;
						}
					}
				});

			});

			$(".filters").on("click", "a", function () {
				let nav_container = $(this).closest('.nav-container');
				let tab = $(this).closest("ol").data("for");
				let filterValue = $(this).data("filter");
				let container = $(this).closest('.tab-pane').find('.container-fillter');
				$(this).closest(".filters").find("a").removeClass("is-checked");
				$(this).addClass("is-checked");
				
				nav_container.find('.txt-search').val('');
				// nav_container.find('.txt-search').trigger('keyup');
				container.isotope({
					filter: function () {
						if (filterValue === "*") {
							return true;
						} else {
							return $(this).data("tag").search(filterValue) >= 0;
						}
					}
				});
			});

			$('.tab-list a[data-toggle="tab"]').on('shown.bs.tab', function(e){
				let activeTab = $(this);
				let controls = $(this).data('controls');
				let nav_container = $(this).closest('.nav-container');
				let container = $(e.target.hash).find('.container-fillter');
				let search = nav_container.find('.txt-search').val().toLowerCase();
				let filterValue = nav_container.find(".for-" + controls + " .is-checked").data("filter");
				
				if (typeof container.data('isotope') == 'undefined'){
					container.isotope({
						itemSelector: ".item",
						layoutMode: "fitRows"
					});
				}

				// Priority is action search, in the case text search is not empty 
				// will search and reset sub category is Show all
				// if (filterValue !== "*") {
				// 	container.find(".for-" + controls + " a").removeClass("is-checked");
				// 	container.find(".for-" + controls + " a.all").addClass("is-checked");
				// }

				nav_container.find('.txt-search').val('');
				nav_container.find('.txt-search').trigger('keyup');
				nav_container.find(".for-" + controls + " a").removeClass("is-checked");
				nav_container.find(".for-" + controls + " a.all").addClass("is-checked");
				nav_container.find(".for-" + controls + " a.is-checked").trigger('click');
				// setTimeout(function () {
				// 	container.isotope({
				// 		// options
				// 		itemSelector: ".item",
				// 		layoutMode: "fitRows",
				// 		filter: function () {
				// 			if (search === "") {
				// 				// Check selected other category
				// 				if (filterValue === "*") {
				// 					return true;
				// 				} else {
				// 					return $(this).data("tag") === filterValue;
				// 				}
				// 			} else {
				// 				let label = $(this).find(".label").text().toLowerCase() + " " + $(this).find("small i").text().toLowerCase();
				// 				return label.search(search) !== -1;
				// 			}
				// 		}
				// 	});
				// 	nav_container.find('.txt-search').trigger('keyup');
				// }, 300);
			});
		};
		this.hideSomeElement = function () {
			$('body', $('.fancybox-iframe').contents()).find("#header").hide();
			$('body', $('.fancybox-iframe').contents()).find("#footer").hide();
			$('body', $('.fancybox-iframe').contents()).find(".page-head, #nav-sidebar ").hide();
		};
		this.showOrHideCheckBox = function (checkbox) {
			id = $(checkbox).attr('id');
			if ($(checkbox).is(':checked'))
				$('.' + id).show();
			else
				$('.' + id).hide();
		};
		this.copyLang = function (element) {
			let $globalthis = this;
			let reg = new RegExp("_" + $globalthis.lang_id, "g");
			//if(typeof $(element) != "undefined" && !$(element).hasClass("ignore-lang") && typeof $(element).attr("id") != "undefined") {
			if (typeof $(element) != "undefined" && !$(element).hasClass("ignore-lang") && $(element).attr("id")) {
				idTemp = $(element).attr("id").replace(reg, "");

				Object.keys($globalthis.languages).forEach(function (key) {
					lang = $globalthis.languages[key];
					if (lang != $globalthis.lang_id && $("#" + idTemp + "_" + lang).val() == "") {
						$("#" + idTemp + "_" + lang).val($("#" + idTemp + "_" + $globalthis.lang_id).val());
					}
				});
			}
		};
		this.updateClassWidget = function (element) {
			let type = $(element).data('type');
			let data_form = $(element).data('form');
			let data_class = $.trim($(element).data('class'));
			let data_class_form;
			if (type == 'DeoColumn'){
				let columnClass = '';
				Object.keys($globalthis.getColDefault()).forEach(function (key) {
					columnClass += ' col-' + key + '-' + data_form[key].toString().replace('.', '-');
				});
				data_class_form = $.trim(data_form.class)+' '+columnClass;
			}else{
				data_class_form = $.trim(data_form.class);
			}

			if ($(element).hasClass('new-shortcode')){
				$(element).removeClass('new-shortcode');
				$(element).removeClass(data_class);
				$(element).addClass(data_class_form);
				$(element).removeClass($globalthis.classNotAllow);
				$(element).data('class',data_class_form);
			}else{
				if (data_class_form != data_class){
					$(element).removeClass(data_class);
					$(element).addClass(data_class_form);
					$(element).removeClass($globalthis.classNotAllow);
					$(element).data('class',data_class_form);
				}
			}
		};
		this.setActiveSub = function (currentE, show = true) {
			if (currentE.hasClass('DeoTabs')){
				let count = currentE.find(".tab-pane").length;
				let tabs = currentE.find('.widget-container-heading a[data-toggle="tab"]');
				let active_tab = currentE.data('form')['active_tab'];
				if (active_tab > count){
					active_tab = count;
				}else if (active_tab <= 0){
					active_tab = 1;
				}

				tabs.each(function(key, tab){
					let data_form = $(tab).data('form');
					data_form['active_tab'] = "0";
					if (active_tab - 1 == key){
						data_form['active_tab'] = "1";
						if (show){
							currentE.find('a[href="' + $(tab).attr('href') + '"]').first().tab('show');
						}
					}

					$(tab).data('form', data_form);
					// console.log($(tab).data('form'));
				});
			}else if (currentE.hasClass('DeoAccordions')){
				let active_type = currentE.data('form')['active_type'];
				let count = currentE.find(".panel-default").length;
				let accordion = currentE.find('.widget-container-heading a[data-toggle="collapse"]');
				let active_accordion = currentE.data('form')['active_accordion'];
				if (active_accordion > count){
					active_accordion = count;
				}else if (active_accordion <= 0){
					active_accordion = 1;
				}

				accordion.each(function(key, accordion){
					let data_form = $(accordion).data('form');
					data_form['active_accordion'] = "0";
					if (active_accordion - 1 == key){
						data_form['active_accordion'] = "1";
						// if (show && active_type == 'set'){
						// 	currentE.find('.widget-container-content' + $(accordion).attr('href')).first().collapse('show');
						// }
					}

					data_form['active_type'] = active_type;

					$(accordion).data('form', data_form);
					// console.log($(accordion).data('form'));
				});
			}
			
		};
		this.saveWidget = function (type) {
			let $globalthis = this;
			currentE = $globalthis.currentElement;

			let ObjectForm = {form_id: "form_" + $globalthis.getRandomNumber()};
			contentHtml = "";

			widgetType = '';
			
			// FIX : widget RAW_HTML always get content of HTML which created before
			$($("#configuration_form").serializeArray()).each(function (i, field) {
				if ((field.name.substring(0, 2).toLowerCase() == 'ap' || field.name.substring(0, 3).toLowerCase() == 'deo') && field.value == '1') {
					widgetType = field.name;
				}
			});
			
			if (typeof tinymce != "undefined" && widgetType != 'DeoOriginalHtml') {
				// let mce = tinymce.activeEditor.getContent();
				// log(tinymce.activeEditor.settings.id);
				// $("#" + tinymce.activeEditor.settings.id).val(mce);
				
				// tinymce.triggerSave();
				$("#configuration_form textarea.autoload_rte").each(function(){
					$(this).val(tinymce.get($(this).attr('id')).getContent());
				});
			}

			//update language for other field
			$("#configuration_form .lang-" + $globalthis.lang_id).each(function () {
				$(this).find('input[type="text"]').each(function () {
					$globalthis.copyLang($(this));
				});
				$(this).find('textarea').each(function () {
					$globalthis.copyLang($(this));
				});
			});

			$($("#configuration_form").serializeArray()).each(function (i, field) {
				// SET EMPTY VALUE AFTER UPDATE LANGUAGE FOR OTHER FIELD
				if ($("#configuration_form [name='"+field.name+"']").hasClass('no-save') || (field.name.includes("temp_") && $("#configuration_form [name='"+field.name+"']").closest('.row-level2').length)){
					return;
				}

				if( field.value == '_JS_EMPTY_VALUE_'){
					field.value = '';
				}
				
				if ((field.name.substring(0, 2).toLowerCase() == 'ap' || field.name.substring(0, 3).toLowerCase() == 'deo') && field.value == '1') {
					widgetType = field.name;
				} else {
					if (field.name == "content_html_" + $globalthis.lang_id) {
						contentHtml = field.value.replace(/[\n]/g, "").replace(/[\r]/g, "");
						if (type == "update") {
							//$(currentE).find('.html-code').html(contentHtml);
						}
					}

					let fName = field.name;
					if (fName.indexOf('[]') != -1) {
						fName = fName.replace('[]', '');
						if (ObjectForm[fName]) {
							ObjectForm[fName] += ',' + field.value;
						}
						else {
							ObjectForm[fName] = field.value;
						}
					} else {
						//ObjectForm[fName] = field.value.replace(/\&/g,'_APAMP_').replace(/\'/g,'_APAPOST_').replace(/\"/g,'_APQUOT_').replace(/[\t]/g, "_APTAB_").replace(/[\r]/g, "_APNEWLINE_").replace(/[\n]/g, "_APENTER_").replace(/\[/g, "_APOBRACKET_").replace(/\]/g, "_APCBRACKET_");
						let valTemp = field.value.replace(/\&/g, '_APAMP_')
								.replace(/\'/g, '_APAPOST_')
								.replace(/\"/g, '_APQUOT_')
								.replace(/[\t]/g, "_APTAB_")
								.replace(/\[/g, "_APOBRACKET_")
								.replace(/[\n]/g, "_APENTER_")
								.replace(/[\r]/g, "")
								.replace(/[+]/g, "_APPLUS_")
								.replace(/\{/g, "_APOCBRACKET_")
								.replace(/\}/g, "_APCCBRACKET_")
								.replace(/\]/g, "_APCBRACKET_");
						ObjectForm[fName] = valTemp;
					}
				}
			});


			//for sub tab
			if (widgetType.indexOf('DeoSub') == 0) {
				// tmpObjectForm = {};
				// tmpObjectForm.form_id = ObjectForm.form_id;
				// tmpObjectForm.id = ObjectForm.id;
				// Object.keys($globalthis.languages).forEach(function (key) {
				// 	tmpObjectForm["title_" + $globalthis.languages[key]] = ObjectForm["title_" + $globalthis.languages[key]];
				// });
				// ObjectForm = tmpObjectForm;

				oldHref = $(currentE).attr("href").toString().replace('#', '');
				panelFind = '.panel-collapse';
					console.log($(currentE).closest('.accordion-panel').find('.widget-container-heading'));
				if (widgetType == 'DeoSubAccordion') {
					ObjectForm.parent_id = $(currentE).data('form').parent_id;
					panelFind = '.panel-collapse';
					$(currentE).closest('.accordion-panel').find('.widget-container-heading a[data-toggle="collapse"]').html(ObjectForm['title_' + $globalthis.lang_id]);
				} else {
					panelFind = '.tab-pane';
					$(currentE).html(ObjectForm['title_' + $globalthis.lang_id]);
				}
				
				$(currentE).closest('.widget-row').find(panelFind).each(function () {
					if ($(this).attr('id') == oldHref) {
						$(this).attr('id', ObjectForm.id);
						return false;
					}
				});

				$(currentE).attr("href", "#" + ObjectForm.id);
			}

			if (type == "update") {
				// SAVE ACTIVE				
				// fix can't save tab after update
				if (widgetType != "DeoSubTabs"){					
					if ($(currentE).find('.btn-status').first().hasClass("deactive")) {
						ObjectForm.active = 0;
					} else {
						ObjectForm.active = 1;
					}
				}

				if (widgetType == "DeoColumn") {
					// $globalthis.changeColumnClass(currentE, ObjectForm);
				}
				if (widgetType == "DeoOriginalHtml") {
					$(currentE).data("form", ObjectForm);
					$(currentE).find(".html-code").html(htmlentities(contentHtml));
				} else if (widgetType == "DeoSubAccordion") {
					ObjectForm["parent_id"] = $globalthis.parentId;
					$(currentE).data("form", ObjectForm);
				} else {
					$(currentE).data("form", ObjectForm);
				}

				// update name of tab after change
				if (widgetType == "DeoSubTabs"){
					$(currentE).text(ObjectForm['title_' + $globalthis.lang_id]);
				}
				$(".label-tooltip").tooltip();
				$globalthis.updateClassWidget(currentE);

				return true;
			}
			dataInfo = $globalthis.shortcodeInfos[widgetType];

			let widget;
			if (widgetType == "DeoTabs") {
				widget = $("#default_DeoTabs").clone(1);
				// remove default tab and default content from tab clone
				$(widget).find('li#default_tabnav').remove();
				$(widget).find('div#default_tabcontent').remove();
				widget.removeAttr('id');
				$('.widget-container-heading a[data-toggle="tab"]', $(widget)).each(function () {
					if ($(this).parent().attr("id") != "default_tabnav" && !$(this).parent().hasClass("tab-button")) {
						let ObjectTab = {form_id: "form_" + $globalthis.getRandomNumber()};
						tabID = "tab_" + $globalthis.getRandomNumber();
						ObjectTab.id = tabID;
						ObjectTab.css_class = "";
						ObjectTab.image = "";
						ObjectTab.override_folder = "";
						ObjectTab.active_tab = "0";
						//set href for tab a
						titleTab = $.trim($(this).html());
						Object.keys($globalthis.languages).forEach(function (key) {
							ObjectTab["title_" + $globalthis.languages[key]] = titleTab;
							ObjectTab["sub_title_" + $globalthis.languages[key]] = '';
						});

						OldHref = $(this).attr('href').replace('#', '');
						$(this).attr("href", "#" + tabID);
						$(this).data("form", ObjectTab);

						$(widget).find('.tab-pane').each(function () {
							if ($(this).attr('id') == OldHref) {
								$(this).attr('id', tabID);
								return false;
							}
						});
					}
				});
			} else if (widgetType == "DeoAccordions") {
				widget = $("#default_DeoAccordions").clone();
				widget.removeAttr('id');
				accIdWraper = "accordion_" + $globalthis.getRandomNumber();
				ObjectForm.id = accIdWraper;
				$('.panel-group', $(widget)).attr('id', accIdWraper);
				$(".panel-title a", $(widget)).each(function () {
					$(this).data('parent', accIdWraper);
					accIdSub = "collapse_" + $globalthis.getRandomNumber();
					OldHref = $(this).attr('href').replace('#', '');
					$(this).attr('href', "#" + accIdSub);
					$('.panel-collapse', $(this).closest('.panel-default')).attr('id', accIdSub);
					let ObjectAccordion = {form_id: "form_" + $globalthis.getRandomNumber()};
					ObjectAccordion.parent_id = accIdWraper;
					ObjectAccordion.id = accIdSub;
					ObjectAccordion.active_accordion = "0";
					ObjectAccordion.active_type = "set";

					titleAccordion = $(this).html();
					Object.keys($globalthis.languages).forEach(function (key) {
						ObjectAccordion["title_" + $globalthis.languages[key]] = titleAccordion;
						ObjectAccordion["sub_title_" + $globalthis.languages[key]] = titleAccordion;
					});
					$(widget).find('.panel-collapse').each(function () {
						if ($(this).attr('id') == OldHref) {
							$(this).attr('id', accIdSub);
							return false;
						}
					});

					$(this).data("form", ObjectAccordion);
				});
				//$('.panel-collapse', $(widget)).last().collapse();
			} else if (widgetType == "DeoModule") {
				widget = $("#default_module").clone(1);
				widget.removeAttr('id');
			} else {
				if ($("#default_" + widgetType).length)
					widget = $("#default_" + widgetType).clone(1);
				else
					widget = $("#default_widget").clone(1);
				if (widgetType == "DeoOriginalHtml") {
					$('.widget-title', $(widget)).remove();
					if ($(widget).find('.html-code').first().length == 0) {
						$(".widget-content", $(widget)).append("<pre><code class='html-code'>" + htmlentities(contentHtml) + "</code></pre>");
					} else {
						$(widget).find('.html-code').first().html(htmlentities(contentHtml));
					}
				}
				widget.removeAttr('id');
			}

			//add new widget in column
			if (type == 'column') {
				widget.removeAttr('id');
				$(currentE).append(widget);
			} else {
				column = $("#default_column").clone(1);
				column.find('.column-content').sortable({
					// cursor: 'move',
					connectWith: ".column-content",
					handle: ".waction-drag"
				});
				column.removeAttr('id');
				objColumn = {form_id: "form_" + $globalthis.getRandomNumber()};
				jQuery.extend(objColumn, $globalthis.getColDefault());
				$(column).data("form", objColumn);

				$('.column-content', $(column)).append(widget);

				group = $("#default_row").clone(1);
				group.children('.group-content').sortable({
					// cursor: 'move',
					connectWith: ".group-content",
					handle: ".caction-drag",
				});
				group.removeAttr('id');
				// let html = $(group).find(".group-controll-right").html();
				// $(group).find(".group-controll-right").html(html);
				$(group).data("form", {form_id: "form_" + $globalthis.getRandomNumber(), 'class': 'row', 'container' : 'container'});
				$('.group-content', $(group)).append(column);
				$(currentE).before(group);
			}

			// add informations
			if (widgetType == "DeoModule") {
				$('.widget-name', $(widget)).html(ObjectForm.name_module);
				$('.w-img', $(widget)).attr('src','../modules/'+ObjectForm.name_module+'/logo.png');
			}else if (widgetType) {
				$('.widget-name', $(widget)).html(dataInfo.label);
				if (typeof dataInfo.image != 'undefined'){
					$('.w-img', $(widget)).attr('src', moduleDir+'/deotemplate/views/img/icons/'+dataInfo.image);
				}
				if (typeof dataInfo.icon_class != 'undefined'){
					$(widget).addClass('widget-icon');
					$('.w-icon', $(widget)).addClass(dataInfo.icon_class).addClass(widgetType);
				}
			}

			$(widget).data("form", ObjectForm);
			$(widget).data("type", widgetType);
				
			if (["DeoAccordions","DeoTabs","DeoPopup"].indexOf(widgetType) < 0){
				$(widget).find('.btn-edit').data("type", widgetType);
			}
			$(widget).find(".label-tooltip").tooltip();
			$globalthis.updateClassWidget(widget);

			$globalthis.sortable();

		};
		this.returnColValue = function (colNumber, finalVal) {
			$globalthis = this;
			widthVal = $globalthis.returnWidthClass();

			startSet = 0;
			let colDefault = $globalthis.getColDefault();
			for (j = 0; j < $globalthis.arrayCol.length; j++) {
				if ($globalthis.arrayCol[j] == widthVal) {
					startSet = 1;
					colDefault[$globalthis.arrayCol[j]] = finalVal;
					continue;
				}

				//default xs = 6-> 2 cols.but we set 2 cols, we have to assign again 
				if (startSet && ((12 / parseInt(colDefault[$globalthis.arrayCol[j]])) < colNumber)) {
					colDefault[$globalthis.arrayCol[j]] = finalVal;
				}
			}
			return colDefault;
		};
		this.changeColumnClass = function (element, dataObj) {
			let $globalthis = this;
			columnClass = 'column-row ' + $globalthis.classWidget;
			Object.keys($globalthis.getColDefault()).forEach(function (key) {
				columnClass += ' col-' + key + '-' + dataObj[key].toString().replace('.', '-');
			});
			$(element).attr('class', columnClass);
		};
		this.getSubWidget = function (container, position_name = null, position_id = null) {
			let $globalthis = this;
			let widgetList = new Object();

			$(container).children().each(function (iWidget) {
				let objWidget = new Object();
				objWidget.params = $(this).data('form');
				if ($.isEmptyObject( objWidget.params )){
					$(this).css('background-color', '#ff6f6f');
					// Dont have param -> dont save
					$globalthis.isValid = false;
				}else{
					$(this).css("background-color", "");
				}
				objWidget.type = $(this).data('type');

				if (typeof position_name != 'null' && typeof $globalthis.positions[position_name] == 'undefined'){
					$globalthis.positions[position_name] = {
						id : position_id,
						position_name : position_name,
						widgets_modules : new Array(),
						elements : new Array(),
						product_lists : new Array(),
					};
				}

				if (objWidget.type == 'DeoModule'){
					// if ($globalthis.widgets_modules.indexOf(objWidget.params.name_module) == -1){
					// 	$globalthis.widgets_modules.push(objWidget.params.name_module);
					// }

					if (typeof position_name != 'null' && $globalthis.positions[position_name].widgets_modules.indexOf(objWidget.params.name_module) == -1){
						$globalthis.positions[position_name].widgets_modules.push(objWidget.params.name_module);
					}
				}else{
					// if ($globalthis.widgets_modules.indexOf(objWidget.type) == -1){
					// 	$globalthis.widgets_modules.push(objWidget.type);
					// }

					if (typeof position_name != 'null' && $globalthis.positions[position_name].widgets_modules.indexOf(objWidget.type) == -1){
						$globalthis.positions[position_name].widgets_modules.push(objWidget.type);
					}

					let classes = objWidget.params.class;
					if (typeof classes != 'undefined'){
						let arr_class = classes.split(" ");
						$.each(arr_class, function(key, value) {
							// if (css_files_available.elements.indexOf(value) >= 0 && $globalthis.elements.indexOf(value) == -1){
							// 	$globalthis.elements.push(value);
							// }

							if (css_files_available.elements.indexOf(value) >= 0 && typeof position_name != 'null' && $globalthis.positions[position_name].elements.indexOf(value) == -1){
								$globalthis.positions[position_name].elements.push(value);
							}

							// exceptions
							$.each(css_files_available.exceptions, function(key_exceptions, value_exceptions) {
								// if (key_exceptions.indexOf(value) >= 0 && $globalthis.elements.indexOf(value_exceptions) == -1){
								// 	$globalthis.elements.push(value_exceptions);
								// }

								if (key_exceptions.indexOf(value) >= 0 && typeof position_name != 'null' && $globalthis.positions[position_name].elements.indexOf(value_exceptions) == -1){
									$globalthis.positions[position_name].elements.push(value_exceptions);
								}
							});
						});
					}

					if (objWidget.type == 'DeoImageHotspot'){
						if (typeof objWidget.params.total_slider != 'undefined'){
							let arr_total_slider = objWidget.params.total_slider.split("|");
							if (arr_total_slider.length > 0){
								$.each(arr_total_slider, function(key_total_slider, value_total_slider) {
									let temp_profile = 'temp_profile_'+value_total_slider;
									// if ($globalthis.product_lists.indexOf(objWidget.params[temp_profile]) == -1 && objWidget.params[temp_profile] != 'default'){
									// 	$globalthis.product_lists.push(objWidget.params[temp_profile]);
									// }

									if (typeof position_name != 'null' && $globalthis.positions[position_name].product_lists.indexOf(objWidget.params[temp_profile]) == -1 && objWidget.params[temp_profile] != 'default'){
										$globalthis.positions[position_name].product_lists.push(objWidget.params[temp_profile]);
									}
								});
							}
						}

					}else if (objWidget.type == 'DeoProductCarousel' || objWidget.type == 'DeoProductList' || objWidget.type == 'DeoProductTabs'){
						// if ($globalthis.product_lists.indexOf(objWidget.params.profile) == -1 && objWidget.params.profile != 'default'){
						// 	$globalthis.product_lists.push(objWidget.params.profile);
						// }

						if (typeof position_name != 'null' && $globalthis.positions[position_name].product_lists.indexOf(objWidget.params.profile) == -1 && objWidget.params.profile != 'default'){
							$globalthis.positions[position_name].product_lists.push(objWidget.params.profile);
						}
					}else if (objWidget.type == 'DeoMegamenuTabs'){
						$globalthis.megamenu_group_active = (typeof objWidget.params.megamenu_group_active != 'null' && objWidget.params.megamenu_group_active) ? objWidget.params.megamenu_group_active : '';
					}
				}

				//if it is special widget - load sub widget
				if ($(this).find('.subwidget-content').length) {
					objWidget.widgets = new Object();
					iSubWidget = 0

					$(this).find('.widget-container-heading a').each(function () {
						if ($(this).parent().attr("id") != "default_tabnav" && !$(this).parent().hasClass("tab-button")) {

							let objSubWidget = new Object();
							objSubWidget.params = $(this).data('form');
							if(objWidget.type == 'DeoPopup'){
								element = $(this).closest('.panel-group').find('.subwidget-content').first();
								objWidget.widgets = $globalthis.getSubWidget(element, position_name, position_id);
							}else{
								element = $($(this).attr('href')).find('.subwidget-content').first();
								objSubWidget.widgets = $globalthis.getSubWidget(element, position_name, position_id);
								objWidget.widgets[iSubWidget] = objSubWidget;
								iSubWidget++;
							}
						}
					});
				}
				widgetList[iWidget] = objWidget;
			});
			return widgetList;
		};
		this.getHookSubmit = function (group, isEscape, position_name = null, position_id = null) {
			let $globalthis = this;
			//group object - contain column
			let objGroup = new Object();
			objGroup.params = $(group).data('form');
			let group_has_module_cache = false;
			$('.DeoModule', $(group)).each(function(){
				let data_form_module = $(this).data('form');
				if (typeof data_form_module.disable_cache != 'undefined' && data_form_module.disable_cache == 1){
					group_has_module_cache = true;

					return true;
				}
			});
			objGroup.params.disable_cache = (group_has_module_cache) ? 1 : 0;
			// console.log(objGroup.params);

			objGroup.columns = new Object();
			//find column in this group
			$('.column-row', $(group)).each(function (iColumn) {
				let column = $(this);
				let objColumn = new Object();
				objColumn.params = $(this).data('form');

				let column_has_module_cache = false;
				$('.DeoModule', column).each(function(){
					let data_form_module = $(this).data('form');
					if (typeof data_form_module.disable_cache != 'undefined' && data_form_module.disable_cache == 1){
						column_has_module_cache = true;

						return true;
					}
				});
				objColumn.params.disable_cache = (column_has_module_cache) ? 1 : 0;
				// console.log(objColumn.params);

				//pass widget for each column
				objColumn.widgets = $globalthis.getSubWidget($(this).find('.column-content').first(), position_name, position_id);
				//pass column for each group
				objGroup.columns[iColumn] = objColumn;
			});

			// console.log(objGroup);

			//pass group for each hook
			return objGroup;
		};
		this.submitForm = function () {
			let $globalthis = this;

			// SUBMIT FORM - Normal
			$("#save-homepage").removeAttr("onclick");
			$(document).on("click", "#save-homepage", function () {
				let objects = new Object();
				$globalthis.isValid = true;
				$('.hook-wrapper').each(function (iHook) {
					//hook object contain group
					let objHook = new Object();
					objHook.name = $(this).data("hook");

					// Get position id
					let select = $(this).closest(".position-cover").find(".dropdown ul");
					objHook.position = $(select).data("position");
					objHook.position_id = $(select).data("id");
					// comment this code because In new bank profile doen't have position_id
					//if (!objHook.position_id) {
						//$globalthis.isValid = false;
					//}

					let position_id = $(this).closest(".position-cover").data('position-id');
					let position_name = $(this).closest(".position-cover").data('position-name');

					let hook_has_module_cache = false;
					objHook.groups = {};
					$('.group-row', $(this)).each(function (iGroup) {
						objHook.groups[iGroup] = $globalthis.getHookSubmit(this, true, position_name, position_id);

						let data_row = objHook.groups[iGroup].params;
						if (typeof data_row.disable_cache != 'undefined' && data_row.disable_cache == 1){
							// console.log(data_row);
							hook_has_module_cache = true;
						}

					});

					objHook.disable_cache = (hook_has_module_cache) ? 1 : 0;
					// console.log(objHook);

					//set hook to object
					objects[iHook] = objHook;
				});
				
				$('#data_profile').val(JSON.stringify(objects));
				$('#data_id_profile').val($('#current_profile').data('id'));
				// $('#data_widgets_modules').val(JSON.stringify($globalthis.widgets_modules));
				$('#data_position').val(JSON.stringify($globalthis.positions));
				// $('#data_elements').val(JSON.stringify($globalthis.elements));
				// $('#data_product_lists').val(JSON.stringify($globalthis.product_lists));
				$('#data_megamenu_group_active').val($globalthis.megamenu_group_active);

				// console.log(objects);
				// return;

				if ($globalthis.isValid == true){
					$("#form_data_profile button").click();
				}else{
					showErrorMessage('A widget has error, please reload this profile.');
				}
			});
			
			
			// submit shortcode
			$(document).on("click", ".shortcode_save_btn, .shortcode_save_stay_btn", function () {			
				
				if ($(this).hasClass('shortcode_save_stay_btn')){
					$('#stay_page').val(1);
				}else{
					$('#stay_page').val(0);
				}
				// console.log($globalthis);
				// $globalthis.isValid = true;
				let objHook = new Object();
				objHook.groups = {};
				objHook.name = $('.hook-wrapper.deoshortcode').data("hook");

				// console.log($('.group-row'));
				$('.hook-wrapper .group-row').each(function (iGroup) {
					objHook.groups[iGroup] = $globalthis.getHookSubmit(this, true, objHook.name);
				});

				// console.log(objHook);
				$('#shortcode_content').val(JSON.stringify(objHook));

				// $('#data_widgets_modules').val(JSON.stringify($globalthis.widgets_modules));
				$('#data_position').val(JSON.stringify($globalthis.positions));
				// $('#data_elements').val(JSON.stringify($globalthis.elements));
				// $('#data_product_lists').val(JSON.stringify($globalthis.product_lists));
				$('#data_megamenu_group_active').val($globalthis.megamenu_group_active);
				
				// console.log($globalthis.positions);
				$('#deotemplate_shortcode_form').submit();
				return false;
			});
			
			$(document).on("click", ".position-cover .list-position .position-name", function () {
				let select = $(this).closest("ul");
				let isRunning = (typeof $(select).attr("isRunning") != "undefined") ? $(select).attr("isRunning") : "";
				if (isRunning.length > 0) {
					return;
				}
				$(select).attr("isRunning", "running");

				let id = parseInt($(this).data("id"));
				let cover = $(select).closest(".position-cover");
				$("#deo_loading").show();
				$.ajax({
					type: "POST",
					headers: {"cache-control": "no-cache"},
					url: $globalthis.ajaxHomeUrl,
					async: true,
					dataType: 'json',
					cache: false,
					data: {
						"id": id,
						"action": "selectPosition",
						"position": $(select).data("position"),
						"id_profile": $('#current_profile').data('id')
					},
					success: function (json) {
						$("#deo_loading").hide();
						if (json && json.hasError == true){
							alert(json.errors);
						}else{
							$(cover).html(json.html);
							$globalthis.reInstallEvent(json.data);
							btn_new_widget_group('.btn-new-widget-group');
							$('.position-cover .show-sidebar').trigger('change');
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						$("#deo_loading").hide();
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					},
					complete: function () {
						$(select).attr("isRunning", "");
					}
				});
				return false;
			});

			$(document).on("click", ".box-edit-position .btn-save", function () {
				let btn = $(this);
				let mode = $(this).closest(".box-edit-position").data("mode");
				let position = $(this).closest(".box-edit-position").data("position");
				let name = $.trim($(this).closest(".box-edit-position").find(".edit-name").val());
				let id = $(this).closest(".box-edit-position").data("id");
				let cover = $(this).closest(".position-cover");
				$("#deo_loading").show();
				$.ajax({
					type: "POST",
					dataType: "Json",
					headers: {"cache-control": "no-cache"},
					url: $globalthis.ajaxHomeUrl,
					async: true,
					cache: false,
					data: {
						"id": id,
						"name": name,
						"mode": mode,
						"action": "processPosition",
						"position": position,
						"id_profile": $('#current_profile').data('id')
					},
					success: function (json) {
						$("#deo_loading").hide();
						if (json && json.hasError == true){
							alert(json.errors);
						}else{
							if (mode == "new" || mode == "duplicate") {
								$(cover).html(json.html);
								$globalthis.reInstallEvent(json.data);
							}
							// Update name after changed
							else {
								$(cover).find(".dropdown .lbl-name").text(name);
								$(btn).closest(".box-edit-position").addClass("hide");
							}
							btn_new_widget_group('.btn-new-widget-group');
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						$("#deo_loading").hide();
						alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					},
					complete: function () {
						$("#deo_loading").hide();
					}
				});
				//$(this).closest(".box-edit-position").addClass("hide");
			});

			$(document).on("click", ".position-cover .list-position .icon-edit, .add-new-position", function (e) {
				let boxEdit = $(this).closest(".dropdown").find(".box-edit-position");
				let input = $(boxEdit).find(".edit-name");
				$(boxEdit).removeClass("hide");
				$(boxEdit).attr("data-mode", $(this).hasClass("add-new-position") ? "new" : "edit");
				$(boxEdit).attr("data-position", $(this).closest("ul").data("position"));
				$(boxEdit).attr("data-id", $(this).data("id"));
				$(this).closest(".dropdown").removeClass("open");

				let span = $(this).closest("a").find("span");
				input.val(span.text());
				input.focus();
				e.stopPropagation();
				return false;
			});
			//icon-edit
			$(document).on("click", ".box-edit-position .btn-default", function () {
				$(this).closest(".box-edit-position").addClass("hide");
				//let id = "#dropdown-" + $(this).closest(".box-edit-position").data("position");
				//log(id);
				$("#dropdown-header").trigger("click");
			});

			$(document).on("click", ".position-cover .list-position .icon-paste", function (e) {
				let boxEdit = $(this).closest(".dropdown").find(".box-edit-position");
				let input = $(boxEdit).find(".edit-name");
				$(boxEdit).removeClass("hide");
				$(boxEdit).attr("data-mode", "duplicate");
				$(boxEdit).attr("data-position", $(this).closest("ul").data("position"));
				$(boxEdit).attr("data-id", $(this).data("id"));
				$(this).closest(".dropdown").removeClass("open");

				let span = $(this).closest("a").find("span");
				input.val($(this).data("temp") + " " + span.text());
				input.focus();
				e.stopPropagation();
				return false;

				// let boxEdit = $(this).closest(".dropdown").find(".box-edit-position");
				// let input = $(boxEdit).find(".edit-name");
				// $(boxEdit).removeClass("hide");
				// $(boxEdit).attr("mode", "duplicate");
				// $(boxEdit).attr("id", $(this).data("id"));
				// $(this).closest(".dropdown").removeClass("open");

				// let span = $(this).closest("a").find("span");
				// input.val(span.text());
				// input.focus();
				// e.stopPropagation();

				// return false;
			});
		};
		this.reInstallEvent = function (dataForm) {
			let $globalthis = this;
			$globalthis.initDataFrom(dataForm);
			$globalthis.setGroupAction();
			$globalthis.sortable();
			$(".label-tooltip").tooltip();
			//$globalthis.setButtonAction();
			//$globalthis.submitForm();
		}
		this.initColumnSetting = function () {
			let $globalthis = this;
			let classActive = $globalthis.returnWidthClass();
			$(".column-row").each(function () {
				$globalthis.getNumberColumnInClass(this, classActive);
			});
		}
		this.getNumberColumnInClass = function (obj, type) {
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
		//THIS IS VERY IMPORTANT TO KEEP AT THE END
		return this;
	};
})(jQuery);

/**
 * FIX : cant focus to textbox of popup tinymce
 */
$(document).on('focusin', function (e) {
	if ($(e.target).closest(".mce-window").length) {
		e.stopImmediatePropagation();
	}
});

/**
 * Fixed case : ajax load html, doesnt have event popover
 */
function btn_new_widget_group() {
	$('.btn-new-widget-group').popover({
		html: true,
		content: function () {
			$globalthis.currentElement = $(this).closest('.hook-content-footer');
			//$globalthis.currentElement = $('.group-content',$(this).closest('.group-row'));
			return $('#addnew-widget-group-form').html();
		}
	});
}

function toogle_limit_twitter(){
	let duration = 400;
	let group = $('.limit-twitter');
	let element = $('input[name="show_scrollbar"]');
	if (!element.length && !group.length) return false;
	
	element.change(function(){
		if ($(this).val() == 0){
			group.show(duration);
		}else{                   
			group.hide(duration);
		}
	});
	$(element.selector+':checked').trigger('change');
}

function toogle_carousel(sub_show){
	//slick carousel
	toogle_switch($(sub_show + ' input[name="slick_lazyload"]'),$('.group_lazyload_slick'),400);
	toogle_switch($(sub_show + ' input[name="slick_centermode"]'),$('.group-slick_centermode'),400);
	toogle_switch($(sub_show + ' input[name="slick_autoplay"]'),$('.group_slick_autoplay'),400);

	//owl carousel
	toogle_switch($(sub_show + ' input[name="lazyload"]'),$('.group_lazyload_owl'),400);
	toogle_switch($(sub_show + ' input[name="pagination"]'),$('.group-pagination'),400);
	toogle_switch($(sub_show + ' input[name="scrollperpage"]'),$('.group-scroll-per-page'),400);
}

function toogle_logo(){
	toogle_switch($('input[name="use_other_image"]'),$('.group-image_logo'),400);
}

function toogle_popup(){
	// toogle_switch($('input[name="simple_popup"]'),$('.group-config-simple_popup'),400,true);
	// toogle_switch($('input[name="simple_popup"]'),$('.group-config-off_simple_popup'),400);
	toogle_switch($('input[name="hide_popup_when_close"]'),$('.group-display-show_text_again'),400);
	toogle_switch($('input[name="show_btn_open_popup"]'),$('.group-button-popup'),400);
	toogle_switch($('input[name="overlay_popup"]'),$('.group-config-overlay'),400);
	toogle_switch($('input[name="background_css"]'),$('.group-config-background'),400);

	toogle_switch($('#position_popup'),$('.group-config-position'),400);
	toogle_switch($('#position_popup_simple'),$('.group-config-position-simple'),400);
	
	if (!$('input[name="simple_popup"]').length && !$('.group-config-simple_popup').length && !$('.group-config-off_simple_popup').length) return false;
	$('input[name="simple_popup"]').change(function(){
		if ($(this).val() == 1){
			$('.group-config-simple_popup').show(400, function(){
				$('.group-config-off_simple_popup').hide(400, function(){
					$('#position_popup_simple').trigger('change');
				});
			});
		}else{     
			$('.group-config-off_simple_popup').show(400, function(){
				$('.group-config-simple_popup').hide(400, function(){
					$('#position_popup').trigger('change');
				});
			});
		}
	});
	$('input[name="simple_popup"]:checked').trigger('change');
}

function toogle_image_360(){
	toogle_switch($('input[name="multiple_row"]'),$('.use-multiple-row'),400);
	toogle_switch($('input[name="use_large_image"]'),$('.use-large-image'),400);
	toogle_switch($('input[name="magnify"]'),$('.use-magnify'),400);

	// toogle spin
	let duration = 400;
	let group = $('.use-autospin');
	let element = $('#autospin');
	if (!element.length && !group.length) return false;

	element.change(function(){
		if (element.val() == 'off'){
			group.hide(duration);
		}else{     
			group.show(duration);
		}
	});
	$(element.selector+':checked').trigger('change');
}
function toogle_google_map(){
	toogle_switch($('.show_select_store input[type="radio"]'),$('.group_show_select_store'),400);
}

function toogle_link_viewall_category_image(){
	toogle_switch($('.show_link_viewall input[type="radio"]'),$('.group_show_link_viewall'),400);
	$(document).on('change', '#carousel_type', function(){
		if($(this).val() == 'normal-image-category'){
			$('.show_link_viewall').removeClass('hide-config').show(400,function(){
				$('input[name="viewall"]:checked').trigger('change');
				$('.group_normal-image-category').removeClass('hide-config').show(400);
				
			});
		}else{
			$('.group_viewall').addClass('hide-config').hide(400);
			$('.group_normal-image-category').addClass('hide-config').hide(400);
		}
	});
	$('#carousel_type').trigger('change');
}

function toogle_background_row(){
	$(document).on('change', '#bg_config_type', function(){
		if ($(this).val() == 'image'){
			$('.group-config-background-video-youtube').addClass('hide-config');
			$('.group-config-background-video-link').addClass('hide-config');
			$('.group-config-background-image').removeClass('hide-config');
		}else if ($(this).val() == 'video_link'){
			$('.group-config-background-video-youtube').addClass('hide-config');
			$('.group-config-background-video-link').removeClass('hide-config');
			$('.group-config-background-image').addClass('hide-config');
		}else if ($(this).val() == 'video_youtube'){
			$('.group-config-background-video-youtube').removeClass('hide-config');
			$('.group-config-background-video-link').addClass('hide-config');
			$('.group-config-background-image').addClass('hide-config');
		}
	});
	$(document).on('change', '#bg_config', function(){
		if ($(this).val() == 'none'){
			$('.group-config-background').addClass('hide-config');
		}else{
			$('.group-config-background').removeClass('hide-config');
		}
		$('#bg_config_type').trigger('change');
	});

	$('#bg_config').trigger('change');
}

function toogle_video(){
	$(document).on('change', '[name="autoplay"]:checked', function(){
		if ($('[name="autoplay"]:checked').val() == 1 && $('[name="mute"]:checked').val() == 0){
			$('[name="mute"][value="1"]').prop('checked', true);
		}
	});
	

	$('[name="autoplay"]:checked').trigger('change');
}

function scrollToModal(container,scrollTo){
	if (scrollTo.length){
		container.animate({
			scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()
		}, 1000);
	}else{
		return false;
	}
}


//sticky-menu
$(document).ready(function(){
	// let lastScrollTop = 0;
	let deo_sticky_offset_top;
	// $('#top_wrapper').append($('#content .page-head'));
	$('#content .page-head').append($('#top_wrapper'));

	if ($('#wrapper-page-builder').length == 0){
		return;
	}

	$('#btn-show-list-widgets').click(function(){
		let btn = $(this);
		let wrapper = $('#wrapper-page-builder');
		if (wrapper.hasClass('hidden-list-widgets')){
			$('body').removeClass('hidden-sidebar-widgets');
			wrapper.removeClass('hidden-list-widgets');
		}else{
			$('body').addClass('hidden-sidebar-widgets');
			wrapper.addClass('hidden-list-widgets');
		}
	});

	function sticky_list_widgets() {
		let heightHeaderInfos = $('#header_infos').outerHeight();
		let heightPageHeader = $('.page-head').outerHeight();
		let heightTopWrapper = $('#top_wrapper').outerHeight();

		let heightListWidgets = $(window).height() - (heightHeaderInfos + heightPageHeader + heightTopWrapper);
		$('#list-widgets').height(heightListWidgets);
	}


	function modal_form_set_height(modal) {
		if (typeof modal.data('bs.modal') != 'undefined' && modal.data('bs.modal').isShown){
			let margin = parseInt(modal.find('.modal-dialog').css('marginTop')) + parseInt(modal.find('.modal-dialog').css('marginBottom'));
			modal.find('.modal-body').css({
				'max-height' : $(window).height() - (modal.find('.modal-footer').outerHeight() + modal.find('.modal-header').outerHeight() + margin),
				'overflow' : 'auto',
			});
		}
	}
	$('#modal_form').on('shown.bs.modal', function(){
	    modal_form_set_height($(this));
	});

	sticky_list_widgets();
	$(window).scroll(function(){
		sticky_list_widgets();
	});
	$(window).resize(function() {
		sticky_list_widgets();
		modal_form_set_height($('#modal_form'));
	});


	$('.nav-scroll').click(function(e){
		e.preventDefault();
		let heightHeaderInfos = $('#header_infos').outerHeight();
		let heightPageHeader = $('.page-head').outerHeight();
		let heightTopWrapper = $('#top_wrapper').outerHeight();
		let scrollTo = $($(this).attr('href'));

		if (scrollTo.length){
			$('html, body').animate({
				scrollTop: scrollTo.offset().top - (heightHeaderInfos + heightPageHeader + heightTopWrapper)
			}, 1000);
		}else{
			return false;
		}
	});	
});