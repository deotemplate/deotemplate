/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
$(document).ready(function() {
	// load all data + html widget
	if ($('.megamenu .dropdown-widget .mega-col').length){
		let widget_data = new Array();
		$.ajax({
			headers: {"cache-control": "no-cache"},
			url: deo_url_megamenu,
			async: true,
			cache: true,
			dataType: "Json",
			data: {
				getListWidgets: 1,
				backoffice: 0,
			},
			type: 'POST',
			success: function(response){
				if (!response.success){
					return false;
				}
				widget_data = response.data;
				if (!Object.keys(widget_data).length){
					return false;
				}
				$(".megamenu .dropdown-widget").each(function(index) {
					let mega_col = $(this).find('.mega-col');
					mega_col.each(function(index) {
						let col = $(this);
						if (typeof (col.data('widgets')) != 'undefined'){
							let widgets = col.data('widgets').toString().split('|');
							$.each(widgets, function(i, widget){
								if (typeof widget_data[widget] !== 'undefined') {
									let widget_element = $(widget_data[widget].html);
									col.children('.mega-col-inner').append(widget_element);
								}
							});
						}
					});
					$(this).removeClass('loading');
				});

				$('.deo-horizontal-menu').each(function(index){
					let megamenu_element = $(this);
					let megamenu_id = megamenu_element.data('megamenu-id');
					let show_mobile_menu = megamenu_element.data('show-mobile-menu');
					megamenu_element.find('.block-toggler').each(function(key){
						let collapse = $(this).find('.collapse').first();
						let toogle = $(this).find('.collapse-icons').first();
						let id = 'menu-toggle-'+index+'-'+key+'-'+collapse.attr('id');

						collapse.attr('id', id);
						toogle.data('target', '#'+id).attr('data-target', '#'+id);
					});
					if (show_mobile_menu == 1){
						$(".deo-megamenu-mobile[data-megamenu-id="+megamenu_id+"]").find('.block-toggler').each(function(key){
							let collapse = $(this).find('.collapse').first();
							let toogle = $(this).find('.collapse-icons').first();
							let id = 'mobile-'+index+'-'+key+'-'+collapse.attr('id');

							collapse.attr('id', id);
							toogle.data('target', '#'+id).attr('data-target', '#'+id);
						});
					}
				});
			}
		});
	}

	if ($('.DeoMegamenuTabs').length){
		let megamenu_tab = $(this);
		let megamenu_tab_link = megamenu_tab.find('.tab-megamenu-item a');


		megamenu_tab_link.click(function(e) {	
			e.preventDefault();
			if (!$(this).hasClass('active')){
				let id = $(this).attr('href');

				megamenu_tab_link.removeClass('active');
				$(this).addClass('active');

				$('.DeoMegamenu.use-tab-style').removeClass('active');
				$(id).addClass('active');
			}
		});
		megamenu_tab.find('.tab-megamenu-item a.active').trigger('click');


		// menu tab mobile
		let menu_tab_mobile = $('<section id="deo-megamenu-tab-mobile"><div class="deo-megamenu-mobile-button-toogle"><span class="deo-megamenu-mobile-close"></span>'+deo_menu_txt+'</div></nav></section>');
		let tab_megamenu_mobile = megamenu_tab.find('.tab-megamenu');
		menu_tab_mobile.append(megamenu_tab.find('.tab-megamenu').clone());
		let megamenu_tab_mobile_link = $('#deo-megamenu-tab-mobile .tab-megamenu-item a');


		$('.use-tab-style .deo-horizontal-menu').each(function(index){
			let $menucontent = $(this).find('.megamenu').clone(1);
			let id = $(this).closest('.use-tab-style').attr('id');
			$menucontent.attr('data-id', '#'+id);

			if ($(this).closest('.use-tab-style').hasClass('active')){
				$menucontent.addClass('active');
			}

			menu_tab_mobile.append($menucontent);
		});
		$("body").append(menu_tab_mobile);
		$('body').append('<div class="megamenu-tab-overlay"></div>');

		$(".megamenu-tab-overlay").click(function(){
			$(".DeoMegamenuTabs .navbar-toggler").trigger('click');
		});

		$("#deo-megamenu-tab-mobile .deo-megamenu-mobile-button-toogle").click( function(){							
			toogle_menu_tab_mobile();
		});

		$(".DeoMegamenuTabs .navbar-toggler").click(function(){
			toogle_menu_tab_mobile();
		});

		$("#deo-megamenu-tab-mobile .caret").click(function(){
			let li_parent = $(this).closest('.nav-item');
			let dropdown = li_parent.children('.dropdown-menu');
			if (li_parent.hasClass('open-sub')){
				li_parent.removeClass('open-sub');
				dropdown.slideUp('fast');
			}else{
				li_parent.addClass('open-sub');
				dropdown.slideDown('fast');
			}
		});
		
		$(window).resize(function() {
			if ($(window).width() > 992){
				$("body").removeClass(".active-deo-megamenu-tab-mobile");
				$("#deo-megamenu-tab-mobile").removeClass('active');
				$("#deo-megamenu-tab-mobile .navbar-header > .navbar-toggler").removeClass('is-active');
			}
		});

		$(document).on("click", '#deo-megamenu-tab-mobile .tab-megamenu-item a', function(e) {
			e.preventDefault();
			if (!$(this).hasClass('active')){
				let id = $(this).attr('href');

				$('#deo-megamenu-tab-mobile .tab-megamenu-item a').removeClass('active');
				$(this).addClass('active');

				$('#deo-megamenu-tab-mobile .megamenu').removeClass('active');
				$('#deo-megamenu-tab-mobile .megamenu[data-id="'+ id +'"]').addClass('active');
			}
		});
		$('#deo-megamenu-tab-mobile .tab-megamenu-item a.active').trigger('click');
	}

	// horizontal menu
	$('.deo-horizontal-menu').each(function(index){
		// let callback = function(elem){
			// let megamenu_element = elem;
			let megamenu_element = $(this);
			let megamenu_id = megamenu_element.data('megamenu-id');
			let show_mobile_menu = megamenu_element.data('show-mobile-menu');

			// if ()

			// check active link
			if ($("body").attr("id")=="index") isHomeMenu = 1;
			megamenu_element.find(".megamenu > li > a").each(function() {
				menuURL = $(this).attr("href").replace("https://","").replace("http://","").replace("www.","").replace( /#\w*/, "" );
				if ((currentURL == menuURL) || (currentURL.replace(current_link,"") == menuURL) || isHomeMenu){
					$(this).parent().addClass("active");
					return false;
				}
			});

			megamenu_element.find(".nav-item > .nav-link").each(function(){
				if ((this).hasAttribute('data-toggle')){
					$(this).removeAttr('data-toggle');
				}
			});
			
			// check target
			if ($(window).width() <= 767){
				set_target_blank(false, megamenu_element); // set cavas NO
			}else{
				set_target_blank(true, megamenu_element); // set cavas Yes
			}

			if (show_mobile_menu == 1){
				// init menu mobile
				let $btn = megamenu_element.find('.navbar-toggler');
				if (!$btn.length) return;
				
				let $nav = $("<section class='deo-megamenu-mobile' data-megamenu-id="+megamenu_id+"><div class='deo-megamenu-mobile-button-toogle'><span class='deo-megamenu-mobile-close'></span>"+deo_menu_txt+"</div></nav></section>");
				let $menucontent = $($btn.data('target')).find('.megamenu').clone(1);
				$("body").append($nav);
				
				$('body').append("<div class='megamenu-overlay' data-megamenu-id="+megamenu_id+"></div>");

				$(".deo-megamenu-mobile[data-megamenu-id="+megamenu_id+"]").append($menucontent);

				if ($btn.is(':visible')) {
					$("body").removeClass("active-deo-megamenu-mobile");
				}

				$(".megamenu-overlay[data-megamenu-id="+megamenu_id+"]").click(function(){
					$btn.trigger('click');
				});
				
				$(".deo-megamenu-mobile[data-megamenu-id="+megamenu_id+"]").find(".deo-megamenu-mobile-button-toogle").click( function(){							
					toogle_menu_mobile(megamenu_id);
				});
				
				$btn.click(function(){
					toogle_menu_mobile(megamenu_id);
				});
				
				$(".deo-megamenu-mobile[data-megamenu-id="+megamenu_id+"] .caret").click(function(){
					let li_parent = $(this).closest('.nav-item');
					let dropdown = li_parent.children('.dropdown-menu');
					if (li_parent.hasClass('open-sub')){
						li_parent.removeClass('open-sub');
						dropdown.slideUp('fast');
					}else{
						li_parent.addClass('open-sub');
						dropdown.slideDown('fast');
					}
				});
				
				$(window).resize(function() {
					if ($(window).width() > 992){
						$("body").removeClass("active-deo-megamenu-mobile");
						$(".deo-megamenu-mobile[data-megamenu-id="+megamenu_id+"]").removeClass('active');
						$(".deo-horizontal-menu[data-megamenu-id="+megamenu_id+"] .navbar-header > .navbar-toggler").removeClass('is-active');
					}
				});
			}else{
				let $bt = megamenu_element.find('.navbar-toggler');
				let $menu = megamenu_element.find('.megamenu-content');
				// add class for menu element when click button to show menu at mobile, tablet
				$bt.click(function(){				
					if ($menu.hasClass('in')){
						megamenu_element.removeClass('active');
					}else{
						if (!megamenu_element.hasClass('active')){
							megamenu_element.addClass('active');
						}
					}
				});
				megamenu_element.find('.megamenu-content .dropdown-toggle').removeAttr("disabled");
				megamenu_element.find(".dropdown-toggle").click(function() {
					if ($(window).width() < 768){
						if ($(this).parent("li").find("div:first").hasClass("level2"))
							return false;
						else
							return true;
					}
				});
				
				megamenu_element.find(".megamenu-content li a").each(function(){
					if ((this).hasAttribute('data-toggle')){
						$(this).removeAttr('data-toggle');
					}
				});
				megamenu_element.find(".megamenu-content li a.dropdown-toggle").click(function(){			
					if (!$(this).parent().hasClass('open') && this.href && this.href != '#'){
						window.location.href = this.href;
					}
				})
				
				megamenu_element.find(".megamenu-content .caret").click(function(){
					if ($(this).parent('li').hasClass('open-sub')){
						$(this).parent('li').find('.dropdown-menu').first().slideUp('fast', function(){
							auto_height_off(megamenu_element);
						});
						$(this).parent('li').removeClass('open-sub');
					}else{
						$(this).parent('li').siblings('.open-sub').find('.dropdown-menu').first().slideUp('fast');
						$(this).parent('li').siblings().removeClass('open-sub');
						$(this).parent('li').find('.dropdown-menu').first().slideDown('fast', function(){
							auto_height_off(megamenu_element);
						});
						$(this).parent('li').addClass('open-sub');
					}
				});
				
				if ($(document).width() > 543){				
					megamenu_element.find('.megamenu-content .dropdown-menu').css('display', '');						
				}
				auto_height_off(megamenu_element);
				$(window).resize(function(){
					auto_height_off(megamenu_element);
					if ($(document).width() >543){
						megamenu_element.find('.megamenu-content .dropdown').removeClass('open-sub');
						megamenu_element.find('.megamenu-content .dropdown-submenu').removeClass('open-sub');
						megamenu_element.find('.megamenu-content .dropdown-menu').css('display', '');	
					}
				});
			}
		// }

		// DeoLazy(callback, this);
	});
	
	// $(".megamenu-overlay").click(function(){
	// 	let megamenu_id = $(this).data('megamenu-id');
	// 	let $btn = $(".deo-horizontal-menu[data-megamenu-id="+megamenu_id+"] .navbar-toggler");
	// 	$btn.trigger('click');
	// });
	// $(".deo-megamenu-mobile .deo-megamenu-mobile-button-toogle").click(function(){
	// 	let megamenu_id = $(this).closest('.deo-megamenu-mobile').data('megamenu-id');
	// 	toogle_menu_mobile(megamenu_id);
	// });
	// $(".deo-horizontal-menu .navbar-toggler").click(function(){
	// 	let megamenu_id = $(this).closest('.deo-horizontal-menu').data('megamenu-id');
	// 	toogle_menu_mobile(megamenu_id);
	// });
	// $(".deo-megamenu-mobile .caret").click(function(){
	// 	let megamenu_id = $(this).closest('.deo-megamenu-mobile').data('megamenu-id');
	// 	let li_parent = $(this).closest('.nav-item');
	// 	let dropdown = li_parent.children('.dropdown-menu');
	// 	if (li_parent.hasClass('open-sub')){
	// 		li_parent.removeClass('open-sub');
	// 		dropdown.slideUp('fast');
	// 	}else{
	// 		li_parent.addClass('open-sub');
	// 		dropdown.slideDown('fast');
	// 	}
	// });
	
	// vertical menu
	$('.deo-vertical-menu').each(function(index){
		// let callback = function(elem){
			// let megamenu_element = elem;
			let megamenu_element = $(this);
			let megamenu_id = megamenu_element.data('megamenu-id');

			megamenu_element.find('.nav-link.dropdown-toggle').removeAttr("disabled");
			megamenu_element.find('.nav-link.dropdown-toggle').click(function() {
				if ($(window).width() <= 767){
					if ($(this).parent("li").find("div:first").hasClass("level2"))
						return false;
					else
						return true;
				}		
			});
			megamenu_element.find('.nav-link.dropdown-toggle').removeAttr('data-toggle');
			megamenu_element.find('.caret').click(function(){
				if (megamenu_element.hasClass('active-button')){
					let $parent  = $(this).parent('li');
					if ($parent.hasClass('open-sub')){
						$parent.find('.dropdown-menu').first().slideUp('fast',function(){
							$parent.removeClass('open-sub');
						});
					}else{
						if ($parent.siblings('.open-sub').length > 0){
							$parent.siblings('.open-sub').find('.dropdown-menu').first().slideUp('fast',function(){								
								$parent.siblings('.open-sub').removeClass('open-sub');								
							});
							$parent.find('.dropdown-menu').first().slideDown('fast',function(){
								$parent.addClass('open-sub');
							});
						}else{
							$parent.find('.dropdown-menu').first().slideDown('fast',function(){
								$parent.addClass('open-sub');
							});
						}
					}
					return false;
				}
			});
			if ($(window).width() > 991){
				megamenu_element.addClass('active-hover');
				megamenu_element.removeClass('active-button');
				megamenu_element.find('.dropdown-menu').css('display', '');
				megamenu_element.removeClass('active');
			}else{
				megamenu_element.removeClass('active-hover');
				megamenu_element.addClass('active-button');
			}
			$(window).resize(function(){
				if ($(window).width() > 991){
					megamenu_element.find('.dropdown').removeClass('open-sub');
					megamenu_element.find('.dropdown-submenu').removeClass('open-sub');
					megamenu_element.addClass('active-hover');
					megamenu_element.removeClass('active-button');
					megamenu_element.find('.dropdown-menu').css('display', '');
					megamenu_element.removeClass('active');
				}else{
					megamenu_element.removeClass('active-hover');
					megamenu_element.addClass('active-button');
				}
			});
			scrollSliderBarMenu(megamenu_element);
		// }

		// DeoLazy(callback, this);
	});

	// js for widget image gallery product
	$(".fancybox").fancybox({
		openEffect	: 'none',
		closeEffect	: 'none'
	});

	// auto calculate height of off canvas menu off
	function auto_height_off(menu_object){	
		wrapper_height = $("#page").innerHeight();
		ul_height = menu_object.find(".megamenu-content ul").innerHeight();
		ul_offset_top = menu_object.find(".megamenu-content ul").offset().top;
		if (ul_offset_top + ul_height > wrapper_height){
			if (!$("#page").hasClass('megamenu-autoheight')){
				$("#page").addClass('megamenu-autoheight');
			}
		}else{
			$("#page").removeClass('megamenu-autoheight');
		}
	}

	function toogle_menu_mobile(megamenu_id){
		if ($('body').hasClass('active-deo-megamenu-mobile') && $(".deo-megamenu-mobile[data-megamenu-id="+megamenu_id+"]").hasClass('active')){
			$("body").removeClass("active-deo-megamenu-mobile");
			$(".deo-megamenu-mobile[data-megamenu-id="+megamenu_id+"]").removeClass('active');
			$(".deo-horizontal-menu[data-megamenu-id="+megamenu_id+"] .navbar-header > .navbar-toggler").removeClass('is-active');
		}else{
			$("body").addClass("active-deo-megamenu-mobile");
			$(".deo-megamenu-mobile[data-megamenu-id="+megamenu_id+"]").addClass('active');
			$(".deo-horizontal-menu[data-megamenu-id="+megamenu_id+"] .navbar-header > .navbar-toggler").addClass('is-active');
		}
	}

	function toogle_menu_tab_mobile(){
		if ($('body').hasClass('active-deo-megamenu-tab-mobile') && $("#deo-megamenu-tab-mobile").hasClass('active')){
			$("body").removeClass("active-deo-megamenu-tab-mobile");
			$("#deo-megamenu-tab-mobile").removeClass('active');
			$("#deo-megamenu-tab-mobile .navbar-header > .navbar-toggler").removeClass('is-active');
		}else{
			$("body").addClass("active-deo-megamenu-tab-mobile");
			$("#deo-megamenu-tab-mobile").addClass('active');
			$("#deo-megamenu-tab-mobile .navbar-header > .navbar-toggler").addClass('is-active');
		}
	}

	function set_target_blank( show, megamenu_element){
		if (show){
			megamenu_element.find(".megamenu-content li a").each(function(){
				if ($(this).hasClass('has-category') && (this).hasAttribute('data-toggle') && $(this).attr('target')== '_blank'){
					let value = $(this).attr('data-toggle');
					$(this).removeAttr('data-toggle');
					$(this).attr('remove-data-toggle', value);
				}
			})
		}else{
			megamenu_element.find(".megamenu-content li a").each(function(){	
				if ($(this).hasClass('has-category') && (this).hasAttribute('remove-data-toggle') && $(this).attr('target')== '_blank'){
					let value = $(this).attr('remove-data-toggle');
					$(this).removeAttr('remove-data-toggle');
					$(this).attr('data-toggle', value);
				}
			})
		}
	}

	function scrollSliderBarMenu(megamenu_element){
		let menuElement = megamenu_element;
		let columnElement = null;
		let maxWindowSize = 991;
		
		//auto display slider bar menu when have left or right column
		if ($(columnElement).length && $(window).width()>=maxWindowSize) 
			showOrHideSliderBarMenu(columnElement, menuElement, 1);
			megamenu_element.find(".vertical-menu-button").click(function(){
			if ($(menuElement).hasClass('active')){
				showOrHideSliderBarMenu(columnElement, menuElement, 0);
			}else{
				showOrHideSliderBarMenu(columnElement, menuElement, 1);
			}
		});

		let lastWidth = $(window).width();
		$(window).resize(function() {
			if ($(window).width()!=lastWidth){
				if($(window).width()<maxWindowSize) {
					if($(menuElement).hasClass('active')) showOrHideSliderBarMenu(columnElement, menuElement, 0);
				}else{
					if($(columnElement).length && !$(menuElement).hasClass('active')) showOrHideSliderBarMenu(columnElement, menuElement, 1);
				}
				lastWidth = $(window).width();
			}
		});
	}

	function showOrHideSliderBarMenu(columnElement, menuElement, active){
		if (active){
			$(menuElement).addClass('active');
			if ($(columnElement).length && $(window).width()>=991) 
				columnElement.css('padding-top',($('.box-content',$(menuElement)).height())+'px');
		}else{
			$(menuElement).removeClass('active');
			if ($(columnElement).length) columnElement.css('padding-top','');
		}
	}
});

