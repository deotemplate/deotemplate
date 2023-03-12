/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
$(document).ready(function(){
	$('#panelTab a').click(function(e) {
		e.preventDefault();
		$(this).tab('show');
	});
	let expiresThemConfigDay = Date.now() + (86400 * 30);
	$('#panelTab a:first').tab('show');
	$(".bg-config").hide();
	let $MAINCONTAINER = $("html");
	
	$("#deo-paneltool .paneltool-title").click(function() {
		$(this).parent().toggleClass("active");
		$.cookie('paneltool_demo_cookie', '1', {expires: expiresThemConfigDay});
	});
	
	$('.colorpicker-skin').colorpicker({
		format: 'hex',
		popover: {
			animation: true,
			placement: (prestashop.language.is_rtl == 1) ? 'right' : 'left',
			fallbackPlacement: 'flip'
		},
	}).on('colorpickerUpdate', function(event) {
		$(this).closest('.group-input').removeClass('error');
	});

	$('.colorpicker-customize-css').colorpicker({
		format: 'rbga',
		popover: {
			animation: true,
			placement: (prestashop.language.is_rtl == 1) ? 'right' : 'left',
			fallbackPlacement: 'flip'
		},
	}).on('colorpickerUpdate', function(event) {
		let input = $(this).find('.color-picker');
		let color = input.val();
		let selector = input.data('selector');
		let selectorColorType = input.data('type');
		let responsive = input.data('responsive');
		let special = input.data('special');
		let media = input.data('media');
		let id = input.data('name');

		if (color){
			let css_responsive = '';
			if (responsive){
				$.each(responsive, function(key, breakpoint){
					css_responsive += breakpoint.media+'{';
						css_responsive += breakpoint.selector+'{';
							css_responsive += selectorColorType+': '+color+';';
							css_responsive += (special) ? special+';' : '';
						css_responsive += '}';
					css_responsive += '}' ;
				});
			}

			let css = '';
			css += '<style type="text/css" id="customize-'+id+'">';
				css += (media) ? media+'{' : '';
					css += selector+'{';
						css += selectorColorType+': '+color+';';
						css += (special) ? special+';' : '';
					css += '}';
				css += (media) ? media+'}' : '';
				css += css_responsive;
			css += '</style>';
			
			if ($('head style#customize-'+id).length) {
				$('head style#customize-'+id).remove();
			}

			$('head').append($(css));
		}else{
			if ($('head style#customize-'+id).length) {
				$('head style#customize-'+id).remove();
			}
		}
		// if ($(selector).length){
		// 	$(selector).css(selectorColorType, color);
		// }
	}).on('colorpickerCreate', function(event) {
		let input = $(this).find('.color-picker');
		let value = input.val();

		if (value){
			$(this).trigger('colorpickerUpdate');
		}
	});

	$('.text-input').keyup(function(){
		let value = $(this).val();
		let selector = $(this).data('selector');
		let selectorType = $(this).data('type');
		let responsive = $(this).data('responsive');
		let special = $(this).data('special');
		let media = $(this).data('media');
		let id = $(this).data('name');

		if (value){
			let css_responsive = '';
			if (responsive){
				$.each(responsive, function(key, breakpoint){
					css_responsive += breakpoint.media+'{';
						css_responsive += breakpoint.selector+'{';
							css_responsive += selectorType+': '+value+';';
							css_responsive += (special) ? special+';' : '';
						css_responsive += '}';
					css_responsive += '}' ;
				});
			}

			let css = '';
			css += '<style type="text/css" id="customize-'+id+'">';
				css += (media) ? media+'{' : '';
					css += selector+'{';
						css += selectorType+': '+value+';';
						css += (special) ? special+';' : '';
					css += '}';
				css += (media) ? media+'}' : '';
				css += css_responsive;
			css += '</style>';
			
			if ($('head style#customize-'+id).length) {
				$('head style#customize-'+id).remove();
			}

			$('head').append($(css));
		}else{
			if ($('head style#customize-'+id).length) {
				$('head style#customize-'+id).remove();
			}
		}
		// if ($(selector).length) {
		// 	$(selector).css(selectorType, value);
		// }
	});

	$('.reset-customize').click(function(){
		let group = $(this).closest('.group-inputs');
		let inputs = group.find('.form-control');
		
		$.each(inputs, function(key, input){
			$(input).val('');
			if ($(input).hasClass('color-picker')){
				$(input).trigger('change');
			}else{
				$(input).trigger('keyup');
			}

			let id = $(input).data('name');
			if ($('head style#customize-'+id).length) {
				$('head style#customize-'+id).remove();
			}
		});
	});

	$('.paneltool.theme-customize .text-input').each(function(){
		if ($(this).val()){
			$(this).trigger('keyup');
		}
	});
	
	/** Panel tool code */
	let second_color = $('.colorpicker-skin .second-color');
	let primary_color = $('.colorpicker-skin .primary-color');
	let configNameSkin = $('#deo-paneltool').data('cname')+'_DEFAULT_SKIN';
	let configName_primary_color = $('#deo-paneltool').data('cname')+'_PRIMARY_CUSTOM_COLOR_SKIN';
	let configName_second_color = $('#deo-paneltool').data('cname')+'_SECOND_CUSTOM_COLOR_SKIN';
	if (primary_color.val() != '' && second_color.val() != ''){
		if (typeof check_init_paneltool != 'undefined'){
			clearInterval(check_init_paneltool);
		}
		
		check_init_paneltool = setInterval(function(){
			clearInterval(check_init_paneltool);
			$('.deo-theme-skin.skin-custom.current-theme-skin').trigger('click');
		}, 100);
	}

	$('.deo-theme-skin').click(function(){
		let btn = $(this);
		let selectedSkin = $(this).data('theme-skin-id');
		if (selectedSkin == 'custom-skin'){
			if (second_color.val() == '' || primary_color.val() == ''){
				if (primary_color.val() == ''){
					primary_color.closest('.group-input').addClass('error');
				}

				if (second_color.val() == ''){
					second_color.closest('.group-input').addClass('error');
				}

				return false;
			}

			$('head #deo-dynamic-skin-css').remove();
			$('head #deo-custom-dynamic-skin-css').remove();
			
			btn.addClass('loading');
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: deo_url_ajax,
				async: true,
				cache: false,
				dataType: "json",
				data: {'load-custom-skin' : 1, 'primary_color' : primary_color.val(), 'second_color' : second_color.val()},
				success: function (jsonData) {
					if (jsonData.success) {
						$('head').append($('<style/>').attr({'id' : 'deo-custom-dynamic-skin-css', 'type' : 'text/css'}).html(jsonData.filecontent));
						
						$.cookie(configName_primary_color, primary_color.val(), {expires: expiresThemConfigDay});
						$.cookie(configName_second_color, second_color.val(), {expires: expiresThemConfigDay});

						$.cookie(configNameSkin, 'custom-skin', {expires: expiresThemConfigDay});
						btn.removeClass('loading');
					}
				},
				error: function () {
				}
			});
		}else{
			if (!$(this).hasClass('current-theme-skin')){

				if (selectedSkin == 'default'){
					$('head #deo-dynamic-skin-css').remove();
					$('head #deo-custom-dynamic-skin-css').remove();
				}else{
					let skinFileUrl = $(this).data('theme-skin-css');

					if ($('head #deo-dynamic-skin-css').length){
						$('head #deo-dynamic-skin-css').attr('href',skinFileUrl+'skin.css');
					}else{
						$('head').append('<link rel="stylesheet" id="deo-dynamic-skin-css" href="'+skinFileUrl+'skin.css" type="text/css" media="all" />');
					}
				}
				$.cookie(configNameSkin, selectedSkin, {expires: expiresThemConfigDay});

				$.cookie(configName_primary_color, '', {expires: expiresThemConfigDay});
				$.cookie(configName_second_color, '', {expires: expiresThemConfigDay});
				
				second_color.val('');
				primary_color.val('');
				second_color.trigger('change');
				primary_color.trigger('change');
			}
		}



		// add class when select skin
		if (!btn.hasClass('current-theme-skin')){
			$('.deo-theme-skin').removeClass('current-theme-skin');
			btn.addClass('current-theme-skin');
		}

		// add class to html when selec skin
		$('.deo-theme-skin').each(function(){
			$('html').removeClass($(this).data('theme-skin-id'));
		});
		$('html').addClass(selectedSkin);
	});

	$('.reset-color').click(function(){
		$('.deo-theme-skin').removeClass('current-theme-skin');
		$('.deo-theme-skin.skin-default').addClass('current-theme-skin');
		
		$('head #deo-dynamic-skin-css').remove();
		$('head #deo-custom-dynamic-skin-css').remove();

		second_color.val('');
		primary_color.val('');
		second_color.trigger('change');
		primary_color.trigger('change');
		$.cookie(configNameSkin, 'default', {expires: expiresThemConfigDay});
		$.cookie(configName_primary_color, '', {expires: expiresThemConfigDay});
		$.cookie(configName_second_color, '', {expires: expiresThemConfigDay});
	});

	// Fonts custom
	let primary_font = $('.deo-custom-font.primary-font');
	let second_font = $('.deo-custom-font.second-font');
	let configName_primary_font = $('#deo-paneltool').data('cname')+'_PRIMARY_CUSTOM_FONT';
	let configName_second_font = $('#deo-paneltool').data('cname')+'_SECOND_CUSTOM_FONT';

	initFontList();
	function initFontList() {
		if ($(".deo-custom-font").length == 0){
			return false;
		}

		if (primary_font.val() != '' && second_font.val() != ''){
			if (typeof check_init_font != 'undefined'){
				clearInterval(check_init_font);
			}
			
			check_init_font = setInterval(function(){
				clearInterval(check_init_font);
				$('.apply-custom-font').trigger('click');
				if (primary_font.val() != ''){
					loadGoogleFont(primary_font.val());
				}
				if (second_font.val() != ''){
					loadGoogleFont(second_font.val());
				}
			}, 100);
		}

		$(".apply-custom-font").click(function(){
			let btn = $(this);
			if (primary_font.val() == '' || second_font.val() == ''){
				if (primary_font.val() == ''){
					primary_font.closest('.group-input').addClass('error');
				}

				if (second_font.val() == ''){
					second_font.closest('.group-input').addClass('error');
				}

				return false;
			}

			$('head #deo-dynamic-font-css').remove();

			btn.addClass('loading');
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: deo_url_ajax,
				async: true,
				cache: false,
				dataType: "json",
				data: {'load-custom-font' : 1, 'primary_font' : primary_font.val(), 'second_font' : second_font.val()},
				success: function (jsonData) {
					if (jsonData.success) {
						$('head').append($('<style/>').attr({'id' : 'deo-dynamic-font-css', 'type' : 'text/css'}).html(jsonData.filecontent));

						$.cookie(configName_primary_font, primary_font.val(), {expires: 1});
						$.cookie(configName_second_font, second_font.val(), {expires: 1});

						btn.removeClass('loading');
					}
				},
				error: function () {
				}
			});

		});

		$('.reset-font').click(function(){
			second_font.val('');
			primary_font.val('');
			$('head #deo-dynamic-font-css').remove();

			$.cookie(configName_primary_font, '', {expires: expiresThemConfigDay});
			$.cookie(configName_second_font, '', {expires: expiresThemConfigDay});
		});

		$.ajax({
			url: deo_url_ajax,
			type: 'POST',
			data: {'load-sample-google' : 1},
			dataType: 'json',
			success: function (jsonData) {
				if (jsonData.success){
					$(".deo-custom-font").each(function(index,item){
						let comboplete = new Awesomplete(item, {
							list: JSON.parse(jsonData.filecontent),
							minChars: 1,
							maxItems: 20,
							autoFirst: false,
							filter: Awesomplete.FILTER_STARTSWITH,
							item: function (text, input) {
								let html = input.trim() === "" ? text : text.replace(RegExp(input.trim().replace(/[-\\^$*+?.()|[\]{}]/g, "\\$&"), "gi"), "<mark>$&</mark>");
								loadGoogleFont(text.value);

								return createLi("li", {
									innerHTML: html,
									"aria-selected": "false",
									"style": "font-family: " + text.value + ";"
								});
							}
						});


						let more_font_family = $(item).closest('.input-group').find('.more-font-family').first();
						more_font_family.click(function(){
							if (comboplete.ul.childNodes.length === 0) {
								comboplete.minChars = 0;
								comboplete.evaluate();
								more_font_family.addClass('show');
							}else if (comboplete.ul.hasAttribute('hidden')) {
								more_font_family.addClass('show');
								comboplete.open();
							}else {
								comboplete.close();
								more_font_family.removeClass('show');
							}
						});

						$(item).on('awesomplete-selectcomplete',function(){
							let keyup = jQuery.Event("keyup");
							keyup.keyCode = 40;
							$(this).focus().trigger(keyup);
							more_font_family.removeClass('show');
							comboplete.close();
						});

						$(item).focus(function(){
							if (comboplete.ul.childNodes.length === 0) {
								comboplete.minChars = 0;
								comboplete.evaluate();
								more_font_family.addClass('show');
							}else if (comboplete.ul.hasAttribute('hidden')) {
								more_font_family.addClass('show');
								comboplete.open();
							}else {
								more_font_family.removeClass('show');
								comboplete.close();
							}
						});

						$(item).blur(function(){
							more_font_family.removeClass('show');
						});
					});
				}
			},
			error: function() {
				console.log('load list font sample error');
			}
		});
	}

	function loadGoogleFont(font){
		WebFont.load({
			google: {
				families: [font]
			}
		});
	}

	function createLi(tag, o) {
		let element = document.createElement(tag);

		for (let i in o) {
			let val = o[i];

			if (i === "inside") {
				$(val).appendChild(element);
			}else if (i === "around") {
				let ref = $(val);
				ref.parentNode.insertBefore(element, ref);
				element.appendChild(ref);

				if (ref.getAttribute("autofocus") != null) {
					ref.focus();
				}
			}else if (i in element) {
				element[i] = val;
			}else {
				element.setAttribute(i, val);
			}
		}

		return element;
	}

	function redirect_checkout(btn, checkbox){
		if (checkbox.length){
			if (checkbox.is(':checked')){
				window.location.href = btn.attr('href')+'?checkout_with_opc';
			}else{
				window.location.href = btn.attr('href');
			}
		}else{
			window.location.href = btn.attr('href');
		}
	}
	$(document).on('click', '.deo-content-cart .cart-buttons .checkout', function(e){
		e.preventDefault();
		let btn = $(this);
		let checkbox = $(this).closest('.cart-buttons').find('[name="use_onepagecheckout"]');
		redirect_checkout(btn, checkbox);
	});

	$(document).on('click', '.cart-grid-right .checkout .btn', function(e){
		e.preventDefault();
		let btn = $(this);
		let checkbox = $(this).closest('.checkout').find('[name="use_onepagecheckout"]');
		redirect_checkout(btn, checkbox);
	});

	// infinite scroll
	$('.deo-switch .infinite_scroll').change(function(){
		let configName = $('#deo-paneltool').data('cname')+'_INFINITE_SCROLL';
		if ($(this).val() == 1){
			$.cookie(configName, 1, {expires: expiresThemConfigDay});
		}else{
			$.cookie(configName, 0, {expires: expiresThemConfigDay});
		}
		window.location.href = deo_demo_category_link;
	});

	// mobile friendly
	$('.deo-switch .mobile_friendly').change(function(){
		let configName = $('#deo-paneltool').data('cname')+'_MOBILE_FRIENDLY';
		if ($(this).val() == 1){
			$.cookie(configName, 1, {expires: expiresThemConfigDay});
		}else{
			$.cookie(configName, 0, {expires: expiresThemConfigDay});
		}
		window.location.href = window.location.pathname;
	});

	// lazyload
	$('.deo-switch .deo_lazyload').change(function(){
		let configName = $('#deo-paneltool').data('cname')+'_LAZYLOAD';
		if ($(this).val() == 1){
			$.cookie(configName, 1, {expires: expiresThemConfigDay});
		}else{
			$.cookie(configName, 0, {expires: expiresThemConfigDay});
		}
		window.location.href = window.location.pathname;
	});

	// stickey menu
	$('.deo-switch .stickey_menu').change(function(){
		let configName = $('#deo-paneltool').data('cname')+'_STICKEY_MENU';
		if ($(this).val() == 1){
			let deo_sticky_offset_top = $('.header-top').offset().top;
			let scrollTop = $(window).scrollTop();
			if (scrollTop > deo_sticky_offset_top) {
				$('#header').addClass('sticky-menu-active');
			} else {
				$('#header').removeClass('sticky-menu-active');
			}
			$('body').addClass('keep-header');
			$.cookie(configName, 1, {expires: expiresThemConfigDay});
		}else{
			$('#header').removeClass('sticky-menu-active');
			$('body').removeClass('keep-header');
			$.cookie(configName, 0, {expires: expiresThemConfigDay});
		}
	});

	// change paneltool
	$('.deo-dropdown .deo-radio-dropdown.change-paneltool').change(function(){
		let dropdown = $(this).closest('.deo-dropdown');
		let selected_value = dropdown.find('.selected-value');
		if ($(this).is(':checked')){
			let href,param = '';
			let path_name = window.location.pathname;
			let string_param = window.location.search.substring(1);
			let array_param = string_param.split('&');
			let value = $(this).val();
			let name = $(this).attr('name');
			if (typeof GetURLParameter(name) == 'undefined'){
				if (array_param.length){
					param = array_param.join('&')+'&'+name+'='+value;
					href = path_name+'?'+param;
				}else{
					param = name+'='+value;
					href = path_name+'?'+param;
				}
			}else{
				for (let i = 0; i < array_param.length; i++){
					let name_check = array_param[i].split('=');
					if (name_check[0] == name){
						// not load if old ID
						if (name_check[1] == value){
							dropdown.removeClass('expanded');
							return false;
						}
						array_param[i] = name+'='+value;
					}
				}
				param = array_param.join('&');
				href = path_name+'?'+param;
			}
			window.location.href = href;
		}
		dropdown.removeClass('expanded');
	});

	function GetURLParameter(sParam) {
		let sPageURL = window.location.search.substring(1);
		let sURLVariables = sPageURL.split('&');
		for (let i = 0; i < sURLVariables.length; i++){
			let sParameterName = sURLVariables[i].split('=');
			if (sParameterName[0] == sParam){
				return sParameterName[1];
			}
		}
	}
	
	function getBodyClassByMenu(){
		if ($('body').hasClass('sidebar-hide') || $('body').hasClass('header-hide-topmenu'))
		   $('body').removeClass('double-menu'); 
		else
			if(!$('body').hasClass('double-menu')) $('body').addClass('double-menu'); 
	}

	// click out to close paneltool
	// $(document).click(function (e) {
	// 	e.stopPropagation();		
	// 	let container = $(".paneltool.active");	
	// 	// fix click colorpicker close panel
	// 	let container_colorpicker = $('.colorpicker-bs-popover');		
	// 	//check if the clicked area is in container or not
	// 	if (container.length && container.has(e.target).length === 0 
	// 		&& container_colorpicker.length && container_colorpicker.has(e.target).length === 0 
	// 		&& !$(e.target).hasClass('panelbutton')) {			
	// 		container.toggleClass("active");			
	// 	}
	// })
});