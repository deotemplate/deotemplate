/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
/**
 * Start block functions common for front-end
 */
(function ($) {
	$.DeoTemplate = function () {
		let object = this;
		$('.slick-carousel').each(function() {
			if (!$(this).hasClass('list-thumbs')){
				object.callInitSlickCarousel($(this));
			}
		});
		$('.carousel-slider').each(function(index) {
			object.callInitOwlCarousel($(this));
		});

		this.initAnimation();
		this.eventHoverThumbnailImage();
		this.eventProductListLoadMore();
		this.eventProductTabs();
		this.initShadownHover();
		this.eventProductShadown();
		this.initCountdown();
		this.initMoreProductImg();
		this.initDeoCartQty();
		this.initTooltip();
	};
	$.DeoTemplate.prototype = {
		processAjax: function () {
			let object = this;

			object.data_send = 'load-ajax=1';
			if (!deoAjaxConfigures.ajax_enable){
				return false;
			}

			if (deoAjaxConfigures.qty_category && $(".deo-qty-category:not(.processed-qty-category)").length)
				object.getQtyCategory();
			else if ($(".deo-qty-category:not(.processed-qty-category)").length)
				$(".deo-qty-category:not(.processed-qty-category)").remove();
			
			// if (deoAjaxConfigures.more_product_img && $(".deo-more-product-img:not(.processed-more-product-img):not(.pro)").length)
			//     object.getMoreProductImg();
			// else if ($(".deo-more-product-img:not(.processed-more-product-img):not(.pro)").length)
			//     $(".deo-more-product-img:not(.processed-more-product-img):not(.pro)").remove();
			
			// if (deoAjaxConfigures.second_img && $(".deo-second-img:not(.processed-second-img):not(.pro)").length)
			//     object.getSecondImg();
			// else if ($(".deo-second-img:not(.processed-second-img):not(.pro)").length)
			//     $(".deo-second-img:not(.processed-second-img):not(.pro)").remove();
			
			// if (deoAjaxConfigures.countdown && $(".deo-countdown:not(.processed-countdown):not(.pro)").length)
			//     object.getCountdown();
			// else if ($(".deo-countdown:not(.processed-countdown):not(.pro)").length)
			//     $(".deo-countdown:not(.processed-countdown):not(.pro)").remove();
			
			// if (deoAjaxConfigures.color && $(".deo-color-product:not(.processed-color-product)").length)
			//     object.getProductColorInfo();
			// else if ($(".deo-color-product:not(.processed-color-product)").length)
			//     $(".deo-color-product:not(.processed-color-product)").remove();
			
			// find class deo-count-wishlist-compare
			object.getCountWishlistCompare();

			if (object.data_send != "load-ajax=1") {
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: deo_url_ajax,
					async: true,
					cache: false,
					dataType: "json",
					data: object.data_send,
					success: function (jsonData) {
						if (jsonData) {
							// qty_category
							if (jsonData.category) {
								let listCategory = new Array();
								for (i = 0; i < jsonData.category.length; i++) {
									listCategory[jsonData.category[i].id_category] = jsonData.category[i].total;
								}

								$('.deo-qty-category:not(.processed-qty-category)').each(function(){
									if ($(this).html() == ''){
										let total = (listCategory[$(this).data("id")]) ? listCategory[$(this).data("id")] : 0;
										let label = (typeof $(this).data("str") != "undefined") ? "<span>" + $(this).data("str") + "</span>" : '';
										$(this).html(total+label);
									}
									$(this).addClass('processed-qty-category');
								});
							}

							// more_product_img
							if (jsonData.more_product_img) {
								let listProduct = new Array();
								for (i = 0; i < jsonData.more_product_img.length; i++) {
									listProduct[jsonData.more_product_img[i].id] = jsonData.more_product_img[i].content;
								}

								$(".deo-more-product-img:not(.processed-more-product-img):not(.pro)").each(function (index) {
									if ($(this).find('.slick-slider').length == 0) {
										// let date = new Date();
										// let time = date.getTime();
										$(this).html(listProduct[$(this).data("idproduct")]);
										// $(this).attr('data-element',time+index);
									}
									$(this).addClass('processed-more-product-img');
								});
							}

							// countdown
							if (jsonData.countdown) {
								let listProduct = new Array();
								for (i = 0; i < jsonData.countdown.length; i++) {
									listProduct[jsonData.countdown[i].id] = jsonData.countdown[i].content;
								}

								$(".deo-countdown:not(.processed-countdown):not(.pro)").each(function () {
									$(this).html(listProduct[$(this).data("idproduct")]);
									$(this).addClass('processed-countdown');
									object.processDealClock($(this).find('.deal-clock').first());
								});
							}

							// color
							if (jsonData.color) {
								let listProduct = new Array();
								for (i = 0; i < jsonData.color.length; i++) {
									listProduct[jsonData.color[i].id] = jsonData.color[i].content;
								}

								$(".deo-color-product:not(.processed-color-product)").each(function () {
									$(this).html(listProduct[$(this).data("idproduct")]);
									$(this).addClass('processed-color-product');
								});
							}
							
							// second_img          
							if (jsonData.second_img) {
								let listProductImg = new Array();
								let listProductName = new Array();
								for (i = 0; i < jsonData.second_img.length; i++) {
									listProductImg[jsonData.second_img[i].id] = jsonData.second_img[i].content;
									listProductName[jsonData.second_img[i].id] = jsonData.second_img[i].name;
								}

								$(".deo-second-img:not(.processed-second-img):not(.pro)").each(function (){
									if (listProductImg[$(this).data("idproduct")]) {
										let str_image = listProductImg[$(this).data("idproduct")];
										if ($(this).data("image-type")) {
											src_image = str_image.replace('home_default',$(this).data("image-type"));
										}else{
											src_image = str_image.replace('home_default', 'home_default');
										}
										let name_image = listProductName[$(this).data("idproduct")];
										if(deo_lazyload){
											$(this).html('<img class="img-fluid lazyload" title="'+name_image+'" alt="'+name_image+'" data-src="' + src_image + '"/>');
										}else{
											$(this).html('<img class="img-fluid" title="'+name_image+'" alt="'+name_image+'" src="' + src_image + '"/>');
										}
									}
									$(this).addClass('processed-second-img');
								});
							}
							
							//wishlist 
							if (jsonData.total_wishlist){
								$('.deo-total-wishlist').data('wishlist-total',jsonData.total_wishlist);
								$('.deo-total-wishlist').text(jsonData.total_wishlist);
							}else{
								$('.deo-total-wishlist').data('wishlist-total',0);
								$('.deo-total-wishlist').text('0');
							}
							$('.deo-total-wishlist').addClass('processed-total-wishlist');
							
							//compare
							if (jsonData.total_compare){
								$('.deo-total-compare').data('compare-total',jsonData.total_compare);
								$('.deo-total-compare').text(jsonData.total_compare);
							}else{
								$('.deo-total-compare').data('compare-total',0);
								$('.deo-total-compare').text(0);
							}
							$('.deo-total-compare').addClass('processed-total-compare');

							object.initAnimation();
							// object.processAjaxProduct();
						}
					},
					error: function () {
					}
				});
			}
		},
		
		//check get number product of wishlist compare
		getCountWishlistCompare: function() {
			if ($('.deo-total-compare:not(.processed-total-compare)').length)
				this.data_send += '&compare=1';

			if ($('.deo-total-wishlist:not(.processed-total-wishlist)').length)
				this.data_send += '&wishlist=1';
		},
		getQtyCategory: function () {
			// get category id
			let qty_category = new Array();
			$(".deo-qty-category:not(.processed-qty-category)").each(function () {
				qty_category.push($(this).data("id"));
			});

			this.data_send += (qty_category.length) ? '&qty_category=' + qty_category.join(',') : '';

			return false;
		},
		getMoreProductImg: function () {
			let more_product_img = new Array();
			$(".deo-more-product-img:not(.processed-more-img):not(.pro)").each(function () {
				more_product_img.push($(this).data("idproduct"));
			});

			this.data_send += (more_product_img.length) ? '&more_product_img=' + more_product_img.join(',') : '';

			return false;
		},
		getCountdown: function () {
			let countdown = new Array();
			$(".deo-countdown:not(.processed-countdown):not(.pro)").each(function () {
				countdown.push($(this).data("idproduct"));
			});

			this.data_send += (countdown.length) ? '&countdown=' + countdown.join(',') : '';

			return false;
		},
		getProductColorInfo: function () {
			let color = new Array();
			$(".deo-color-product:not(.processed-color-product)").each(function () {
				color.push($(this).data("idproduct"));
			});

			this.data_send += (color.length) ? '&color=' + color.join(',') : '';

			return false;
		},
		getSecondImg: function () {
			//tranditional image
			let second_img = new Array();
			$(".deo-second-img:not(.processed-second-img):not(.pro)").each(function () {
				second_img.push($(this).data("idproduct"));
			});

			this.data_send += (second_img.length) ? '&second_img=' + second_img.join(',') : '';

			return false;
		},
		processDealClock: function (deal_clock) {
			let text_day = deal_clock.data('text-day');
			let text_hour = deal_clock.data('text-hour');
			let text_min = deal_clock.data('text-min');
			let text_sec = deal_clock.data('text-sec');
			let text_finish = deal_clock.data('text-finish');
			let target_date = deal_clock.data('target-date');
			deal_clock.lofCountDown({
				TargetDate: target_date,
				DisplayFormat: '<li class="day">%%D%%<span>'+text_day+'</span></li><li class="hour">%%H%%<span>'+text_hour+'</span></li><li class="minute">%%M%%<span>'+text_min+'</span></li><li class="seconds">%%S%%<span>'+text_sec+'</span></li>',
				FinishMessage: text_finish,
			});
		},
		processAjaxProduct: function (){
			let object = this;
			
			object.initCountdown();
			object.initMoreProductImg();
			object.initTooltip();

			// init more image
			// if (typeof(deoAjaxConfigures) != 'undefined' && deoAjaxConfigures.more_product_img){
			// 	$(".deo-more-product-img:not('.processed-more-img'):not(.pro)").each(function() {
			// 		let more_img = $(this);
			// 		let list_thumbs = $(this).find('.list-thumbs');

			// 		// init slick carousel
			// 		let horizontal = ($(this).data("type") && $(this).data("type") === "horizontal") ? true : false;
			// 		let breakpoints = more_img.data('breakpoints');
			// 		let slidesToShow = 4;
			// 		let responsive = false;
			// 		let slick_rtl = (horizontal && prestashop.language.is_rtl == 1) ? true : false;

			// 		if ((typeof breakpoints !== 'undefined') && breakpoints) {
			// 			responsive = [];
			// 			for (let i = 0; i < breakpoints.length; i++) {
			// 				let settings = {
			// 					slidesToShow : breakpoints[i][1]
			// 				}
			// 				let breakpoint =  {
			// 					breakpoint : breakpoints[i][0],
			// 					settings : settings
			// 				};
			// 				responsive.push(breakpoint);
			// 			}
			// 		}

			// 		list_thumbs.slick({
			// 			speed: 300,
			// 			dots: false,
			// 			infinite: false,
			// 			slidesToScroll: 1,
			// 			adaptiveHeight : true,
			// 			slidesToShow: slidesToShow,
			// 			vertical: horizontal ? false : true,
			// 			verticalSwiping: horizontal ? false : true,
			// 			rtl: slick_rtl,
			// 			lazyload: deo_lazyload ? 'ondemand' : false,
			// 			responsive: responsive,
			// 		}).on('lazyLoaded', function(event, slick, direction){
			// 			// direction.closest('.slick-slide').addClass('slick-loaded');
			// 			direction.prev('.lazyload-wrapper').remove();
			// 		}).on('mousewheel', function (e) {
			// 			if (e.deltaY>0) {
			// 				$(this).slick('slickNext');
			// 			} else {
			// 				$(this).slick('slickPrev');
			// 			}
			// 			e.preventDefault();
			// 		});

			// 		// init fancybox
			// 		$('.thickbox-ajax-'+$(this).data("idproduct")).fancybox({
			// 			helpers: {
			// 				overlay: {
			// 					locked: false
			// 				}
			// 			},
			// 			'hideOnContentClick': true,
			// 			'transitionIn'  : 'elastic',
			// 			'transitionOut' : 'elastic'
			// 		});

			// 		more_img.addClass('processed-more-img');
			// 	}); 
			// }
		},
		initTooltip: function(){
		    $($('[data-toggle="deo-tooltip"]:not(.has-init-tooltip)')).each(function(index) {
		        let my, at;
		        let $tooltip = $(this);

		        // if ($tooltip.data("position") === 'top'){
		        //     my = "center bottom";
		        //     at = "center top-10";
		        // }else if($tooltip.data("position") === 'bottom'){
		        //     my = "center top+10";
		        //     at = "center bottom";
		        // }else if($tooltip.data("position") === 'left'){
		        //     if (prestashop.language.is_rtl == 1){
		        //         my = "center center";
		        //         at = "right+8 center";
		        //     }else{
		        //         my = "right center";
		        //         at = "left-8 center";
		        //     }
		        // }else if($tooltip.data("position") === 'right'){
		        //     if (prestashop.language.is_rtl == 1){
		        //         my = "right center";
		        //         at = "left-8 center";
		        //     }else{
		        //         my = "left center";
		        //         at = "right+8 center";
		        //     }
		        // }else{
		        //     return false;
		        // }

		        $tooltip.tooltip({
		            // position: {
		            //     my: my,
		            //     at: at,
		            //     using: function(position, feedback) {
		            //         $(this).css(position);
		            //         if(feedback.element.left+feedback.element.width == $(window).width()){
		            //             feedback.horizontal = 'right';
		            //         }
		            //         $("<div>").addClass("arrow").addClass(feedback.vertical).addClass(feedback.horizontal).appendTo(this);
		            //         $tooltip.addClass('has-init-tooltip');
		            //     }
		            // },

		            // open: function(event, ui){
		            //     if ($(this).hasClass('hotspot')){
		            //         if (typeof(event.originalEvent) === 'undefined'){
		            //             return false;
		            //         }
		                    
		            //         let $id = $(ui.tooltip).attr('id');
		                    
		            //         // close any lingering tooltips
		            //         $('div.deo-tooltip').not('#' + $id).remove();
		                    
		            //         // ajax function to pull in data and add it to the tooltip goes here
		            //     }
		            // },
		            // close: function(event, ui){
		            //     if ($(this).hasClass('hotspot')){
		            //         ui.tooltip.hover(
		            //             function(){ $(this).stop(true).fadeTo(400, 1); },
		            //             function(){ $(this).fadeOut('400', function(){ $(this).remove();}); }
		            //         );
		            //     }
		            // }
		        }).addClass('has-init-tooltip');
		    });
		},
		initDeoCartQty: function(){
			$('.deo-cart-quantity:not(.processed)').each(function(){
				// hide input quantity when cart buton does not show
				if ($(this).parents('.product-miniature').find('.qty_product').val()){
					$(this).val($(this).parents('.product-miniature').find('.qty_product').val());
				}else{
					$(this).hide();
				}
				$(this).addClass('processed');

				let max = $(this).data('max');
				let min = $(this).data('min');
				$(this).TouchSpin({
					max: max,
					min: (max == 0) ? max : min,
					verticalbuttons: true,
					verticalupclass: "material-icons touchspin-up",
					verticaldownclass: "material-icons touchspin-down",
				});
			});
		},
		initMoreProductImg: function(){
			let object = this;

			$(".deo-more-product-img.pro:not(.processed-more-product-img)").each(function() {
				object.callInitSlickCarousel($(this).find('.list-thumbs').first());
				$(this).find('.thickbox-ajax-'+$(this).data("idproduct")).fancybox({
					helpers: {
						overlay: {
							locked: false
						}
					},
					'hideOnContentClick': true,
					'transitionIn'  : 'elastic',
					'transitionOut' : 'elastic'
				});
				$(this).addClass('processed-more-product-img');
			});
		},
		initCountdown: function(){
			$(".deo-countdown.pro:not(.processed-countdown)").each(function() {
				let deal_clock = this;
				let text_year = ($(deal_clock).data('text-year')) ? $(deal_clock).data('text-year') : 'years';
				let text_week = ($(deal_clock).data('text-week')) ? $(deal_clock).data('text-week') : 'weeks';
				let text_day = ($(deal_clock).data('text-day')) ? $(deal_clock).data('text-day') : 'days';
				let text_hour = ($(deal_clock).data('text-hour')) ? $(deal_clock).data('text-hour') : 'hours';
				let text_min = ($(deal_clock).data('text-min')) ? $(deal_clock).data('text-min') : 'mins';
				let text_sec = ($(deal_clock).data('text-sec')) ? $(deal_clock).data('text-sec') : 'secs';
				let text_finish = ($(deal_clock).data('text-finish')) ? $(deal_clock).data('text-finish') : 'Expired';
				let time_from = Date.parse($(deal_clock).data('time-from'));
				let time_to = Date.parse($(deal_clock).data('time-to'));

				if ((!isNaN(time_to) || time_to) && time_to > deo_time_now){
					let time_countdown = time_to - ((!isNaN(time_from)) ? time_from : deo_time_now);
					let outputTranslation = {
						day: text_day,
						hour: text_hour,
						minute: text_min,
						second: text_sec,
					};
					let outputFormat = 'day|hour|minute|second';
					if (time_countdown > 999*7*60*60*1000) {
						outputTranslation.week = text_week;
						outputFormat = 'week|day|hour|minute|second';
					}
					if (time_countdown > 999*365*24*60*60*1000) {
						outputTranslation.year = text_year;
						outputFormat = 'year|week|day|hour|minute|second';
					}

					let countdown = new DeoCountdown({
						cont: deal_clock,
						date: Date.now() + time_countdown,
						outputTranslation: outputTranslation,
						outputFormat: outputFormat,
						endCallback: function () {
							// $(deal_clock).append($('<div class="countDown_expired">'+text_finish+'</div>'));
						},
					});
					countdown.start();
				}else{
					$(deal_clock).addClass('hide');
				}

				$(this).addClass('processed-countdown');
			});
		},
		initShadownHover: function(){
			$(".shadow-hover .thumbnail-container:not(.processed-shadown-hover)").each(function() {
				if ($(window).width() >= 992){
					let function_buttons = $(this).find('.box-button').first();
					let description = $(this).find('.product-description').first();
					let description_short = $(this).find('.product-description-short').first();
					if ($(this).find('div.box-shadow').length < 1){
						$(this).append('<div class="box-shadow" style="bottom: -'+ function_buttons.outerHeight() +'px"></div>');
					}
					if(description.find('a.show-all-description').length < 1){
						description.append('<a href="javascript:void(0)" class="show-all-description"><span class="left"></span><span class="center"></span><span class="right"></span></a>');
					}
					if(description_short.find('a.show-all-description').length < 1){
						description_short.append('<a href="javascript:void(0)" class="show-all-description"><span class="left"></span><span class="center"></span><span class="right"></span></a>');
					}
					$(this).addClass('processed-shadown-hover');
				}
			});
		},
		callInitSlickCarousel: function (carousel, configurations = {}){
			let activemode, centermode, dots, adaptiveheight, infinite, vertical, verticalswiping,
				autoplay, autoplayspeed, pauseonhover, arrows, rows, slidestoshow, slidestoscroll,
				rtl, mousewheel, fade, focusonselect, asnavfor, lazyload, responsive, initialslide;

			if (Object.keys(configurations).length && configurations.constructor === Object){
				mousewheel = (typeof configurations.mousewheel != 'undefined') ? configurations.mousewheel : false;
			}else{
				let data = carousel.data();
				if (Object.keys(data).length <= 0 ) return;

				activemode = (typeof data.activemode != 'undefined') ? data.activemode : false;
				centermode = (typeof data.centermode != 'undefined') ? data.centermode : false;
				dots =  (typeof data.dots != 'undefined') ? data.dots : false;
				adaptiveheight = (typeof data.adaptiveheight != 'undefined') ? data.adaptiveheight : false;
				infinite = (typeof data.infinite != 'undefined') ? data.infinite : false;
				vertical = (typeof data.vertical != 'undefined') ? data.vertical : false;
				verticalswiping = (typeof data.verticalswiping != 'undefined') ? data.verticalswiping : false;
				autoplay = (typeof data.autoplay != 'undefined') ? data.autoplay : false;
				autoplayspeed = (typeof data.autoplayspeed != 'undefined') ? data.autoplayspeed : 300;
				pauseonhover = (typeof data.pauseonhover != 'undefined') ? data.pauseonhover : false;
				arrows = (typeof data.arrows != 'undefined') ? data.arrows : false;
				rows = (typeof data.rows != 'undefined') ? data.rows : 1;
				slidestoshow = (typeof data.slidestoshow != 'undefined') ? data.slidestoshow : 1;
				slidestoscroll = (typeof data.slidestoscroll != 'undefined') ? data.slidestoscroll : 1;
				rtl = (typeof data.rtl != 'undefined') ? data.rtl : false;
				mousewheel = (typeof data.mousewheel != 'undefined') ? data.mousewheel : false;
				fade = (typeof data.fade != 'undefined') ? data.fade : false;
				focusonselect = ((typeof data.focusonselect != 'undefined') && data.focusonselect) ? data.focusonselect : false;
				asnavfor = ((typeof data.asnavfor != 'undefined') && data.asnavfor) ? data.asnavfor : false;
				initialslide = (typeof data.initialslide != 'undefined') ? data.initialslide : 0;

				if (data.lazyload){
					lazyload = data.lazyloadtype;
				}else{
					lazyload = false;
				}
				rtl = (vertical) ? false : rtl;
				adaptiveheight = (vertical) ? false : adaptiveheight;


				responsive = false;
				if ((typeof data.responsive !== 'undefined') && data.responsive) {
					responsive = [];
					for (let i = 0; i < data.responsive.length; i++) {
						let settings = {
							slidesToShow : data.responsive[i][1],
							// slidesToScroll : (typeof activemode !== 'undefined' && activemode) ? slidestoscroll : data.responsive[i][1]
						}
					
						let breakpoint =  {
							breakpoint : data.responsive[i][0],
							settings : settings
						};
						responsive.push(breakpoint);
					}
				}
			}


			let tab = carousel.closest('.DeoTab');
			if(tab.length){		
				if (carousel.closest('.tab-pane').hasClass('active')) {
					initSlickCarousel(carousel, configurations);
				}else{
					tab.find('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
						if ($(this).hasClass('active')) {
							let carouselTab = $('#'+$(this).data('tab')).find('.slick-slider');
							if(carouselTab.hasClass('deo-carousel-loading')){
								initSlickCarousel(carouselTab, configurations);
							}
						}
					});
				}
			}else{
				initSlickCarousel(carousel, configurations);
			}


			function initSlickCarousel(carousel, configurations) {
				function callback(carousel){
					carousel.imagesLoaded( function() {
						if (Object.keys(configurations).length && configurations.constructor === Object){
							carousel.slick(configurations);
						}else{
							carousel.slick({
								activeMode: activemode,
								centerMode: centermode,
								dots: dots,
								adaptiveHeight : adaptiveheight,
								infinite: infinite,
								vertical: vertical,
								verticalSwiping : verticalswiping,
								autoplay: autoplay,
								autoplaySpeed: autoplayspeed,
								pauseonhover: pauseonhover,
								arrows: arrows,
								slidesToShow: slidestoshow,
								slidesToScroll: slidestoscroll,
								rtl: rtl,
								lazyLoad: lazyload,
								responsive: responsive,
								asNavFor: asnavfor,
								focusOnSelect: focusonselect,
								fade: fade,
								initialSlide: initialslide,
							});
						}

						carousel.removeClass('deo-carousel-loading').addClass('slick-loaded');
						carousel.parents('.slick-row').addClass('hide-loading');
						carousel.on('lazyLoaded', function(event, slick, direction, imageSource){
							direction.parent().find('.lazyload-wrapper').remove();
						});
						if (mousewheel){
							carousel.on('mousewheel', function (e) {
								if (e.deltaY>0) {
									$(this).slick('slickNext');
								}else{
									$(this).slick('slickPrev');
								}
								e.preventDefault();
							});
						}
					});
				}
				if ('IntersectionObserver' in window && deo_lazy_intersection_observer && configurations.asNavFor == false){
					let observer = new IntersectionObserver((entries)  => {
						entries.forEach(entry => {
							if (entry.isIntersecting){
								callback($(entry.target));
							}
						});
					});

					carousel.each((key, elem) => {
						observer.observe(elem);
					});
				}else{
					callback(carousel);
				}
			}
		},
		callInitOwlCarousel: function (carousel, configurations = {}){
			let items, itemsdesktop, itemsdesktopsmall, 
				itemstablet, itemstabletsmall, itemsmobile, 
				itemssmallmobile, itemscustom, slidespeed,
				paginationspeed, autoplayspeed, autoplay, stoponhover,
				navigation, scrollperpage, pagination, paginationnumbers, 
				responsive, lazyload, lazyfollow, lazyeffect, autoheight, 
				mousedrag, touchdrag, direction, mousewheel;
			if (Object.keys(configurations).length && configurations.constructor === Object){
				mousewheel = (typeof configurations.mousewheel == 'undefined') ? configurations.mousewheel : false;
			}else{
				let data = carousel.data();

				if (Object.keys(data).length <= 0 ) return;

				items = data.items;
				itemsdesktop = data.itemsdesktop;
				itemsdesktopsmall = data.itemsdesktopsmall;
				itemstablet = data.itemstablet;
				itemstabletsmall = data.itemstabletsmall;
				itemsmobile = data.itemsmobile;
				itemssmallmobile = data.itemssmallmobile;
				itemscustom = data.itemscustom;
				slidespeed = data.slidespeed;
				paginationspeed = data.paginationspeed;
				autoplayspeed = data.autoplayspeed;
				autoplay = data.autoplay;
				stoponhover = data.stoponhover;
				navigation = data.navigation;
				scrollperpage = data.scrollperpage;
				pagination = data.pagination;
				paginationnumbers = data.paginationnumbers;
				responsive = data.responsive;
				lazyload = data.lazyload;
				lazyfollow = data.lazyfollow;
				lazyeffect = data.lazyeffect;
				autoheight = data.autoheight;
				mousedrag = data.mousedrag;
				touchdrag = data.touchdrag;
				direction = data.direction;
				mousewheel = data.mousewheel;
			}
			
			let tab = carousel.closest('.DeoTab');
			if (tab.length){
				if (carousel.closest('.tab-pane').hasClass('active')) {
					initOwlCarousel(carousel, configurations);
				}else{
					tab.find('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
						if ($(this).hasClass('active')) {
							let carouselTab = $('#'+$(this).data('tab')).find('.owl-carousel');
							if (carouselTab.hasClass('deo-carousel-loading')){
								initOwlCarousel(carouselTab, configurations);
							}
						}
					});
				}
			}else{
				initOwlCarousel(carousel, configurations);
			}
			
			function initOwlCarousel(carousel, configurations) {
				carousel.imagesLoaded( function() {
					if (Object.keys(configurations).length && configurations.constructor === Object){
						carousel.owlCarousel(configurations);
					}else{
						carousel.owlCarousel({
							items :             items,
							itemsDesktop :      itemsdesktop,
							itemsDesktopSmall : itemsdesktopsmall,
							itemsTablet :       itemstablet,
							itemsTabletSmall : 	itemstabletsmall,
							itemsMobile :       itemsmobile,
							itemsSmallMobile :  itemssmallmobile,
							itemsCustom :       itemscustom,
							singleItem :        false, 
							itemsScaleUp :      false,
							slideSpeed :        slidespeed, 
							paginationSpeed :   paginationspeed, 
							autoPlay :          autoplay,  
							stopOnHover :       stoponhover,
							navigation :        navigation,
							navigationText :    ["&lsaquo;", "&rsaquo;"],
							scrollPerPage :     scrollperpage,
							pagination :        pagination,  
							paginationNumbers : paginationnumbers, 
							responsive :        responsive,
							lazyLoad :          lazyload,
							lazyFollow :        lazyfollow, 
							lazyEffect :        lazyeffect,
							autoHeight :        autoheight,
							mouseDrag :         mousedrag,
							touchDrag :         touchdrag,
							addClassActive :    true,
							direction:          direction,
							afterInit: 			OwlLoaded,
							afterAction : 		SetOwlCarouselFirstLast,
							afterLazyLoad : 	afterLazyLoad,
						});
					}
				});
			
				if (mousewheel){
					carousel.on('mousewheel', function (e) {
						e.preventDefault();
						if (e.deltaY>0) {
							$(this).trigger('owl.next');
						} else {
							$(this).trigger('owl.prev');
						}
					});
				}
			
				function afterLazyLoad (el) {
					let owl_items = el.find('.owl-item:not(.loading)');
					owl_items.each(function(index, item){
						let lazyload_wrapper = $(item).find('.lazyload-wrapper').first();
						if ($(item).closest('.owl-carousel')[0] === $(el)[0]){
							lazyload_wrapper.next('img').css("display", "");
							lazyload_wrapper.remove();
						}
					});
					// console.log($(item).parentsUntil('.owl-carousel').length);
				}
				function OwlLoaded(el){
					el.removeClass('deo-carousel-loading').addClass('owl-loaded').parents('.owl-row').addClass('hide-loading');
				}
			}
		},
		initAnimation: function (){
			if (!deoAjaxConfigures.animation){
				return false;
			}
			$(".has-animation:not(.processed-animation)").each(function() {
				let items = $(this);
				items.each(function() {
					let osElement = $(this);
					let animation = $(osElement).data("animation");
					let osAnimationDelay = $(osElement).data("animation-delay");
					let osAnimationDuration = $(osElement).data("animation-duration");
					let osAnimationIterationCount = $(osElement).data("animation-iteration-count");
					let osAnimationInfinite = $(osElement).data("animation-infinite");
					if (osAnimationInfinite == 1){
						let loop_animation = 'infinite';
					}else{
						let loop_animation = osAnimationIterationCount;
					}
					osElement.css({
						"-webkit-animation-delay": osAnimationDelay,
						"-moz-animation-delay": osAnimationDelay,
						"animation-delay": osAnimationDelay,
						"-webkit-animation-duration": osAnimationDuration,
						"-moz-animation-duration": osAnimationDuration,
						"animation-duration": osAnimationDuration,
						"-webkit-animation-iteration-count": loop_animation,
						"-moz-animation-iteration-count": loop_animation,
						"animation-iteration-count": loop_animation,
					});
					
					osElement.waypoint(function() {     
						if (osElement.hasClass('has-animation')){                   
							osElement.addClass('animated '+ animation).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){                  
								$(this).removeClass('has-animation animated ' +animation);                  
							});         
						}            
				
						this.destroy();
					}, {
						triggerOnce: true,
						offset: '100%'
					});

					osElement.addClass('processed-animation');
				});
			});
		},
		eventHoverThumbnailImage: function (){
			$(document).on("mouseover",".deo-more-product-img .image-hover", function(){
				let slider = $(this).closest('.deo-more-product-img');
				let url_large_img = $(this).find('img').attr("rel");
				let img = $(this).closest('.thumbnail-container').find('.product-thumbnail > img').first();

				url_large_img = (img.data("image-type")) ? url_large_img.replace('home_default',img.data("image-type")) : url_large_img;
				img.stop().animate({opacity: 0}, {duration: 800, easing: "easeInOutQuad"});
				img.attr("src", url_large_img);
				img.attr("data-rel", url_large_img);
				img.stop().animate({opacity: 1}, {duration: 800, easing: "easeInOutQuad"});

				// change class when hover another image
				slider.find('.link-img').removeClass('shown');
				$(this).addClass('shown');
			});
		},
		eventProductShadown: function (){
			$(document).on('mouseenter','.shadow-hover .thumbnail-container.processed-shadown-hover', function(){

			}).on('mouseleave','.shadow-hover .thumbnail-container.processed-shadown-hover',  function(){
				if ($(window).width() >= 992){
					let function_buttons = $(this).find('.box-button').first();
					let box_shadown = $(this).find('.box-shadow').first();
					let description = $(this).find('.product-description').first();
					let description_short = $(this).find('.product-description-short').first();

					if ((description.find('a.show-all-description').length && description.hasClass('show')) || (description_short.find('a.show-all-description').length && description_short.hasClass('show'))){
						if (description.find('a.show-all-description').length && description.hasClass('show')){
							description.removeClass('show');
						}
						if (description_short.find('a.show-all-description').length && description_short.hasClass('show')){
							description_short.removeClass('show'); 
						}

						box_shadown.css("bottom", '-' + function_buttons.outerHeight() +'px');
					}
				}
			});

			// click show description
			$(document).on( "click",".shadow-hover .thumbnail-container.processed-shadown-hover .show-all-description", function() {
				if ($(window).width() >= 992){
					let thumbnail_container = $(this).closest('.thumbnail-container');
					let description = thumbnail_container.find('.product-description').first();
					let description_short = thumbnail_container.find('.product-description-short').first();
					let box_shadow = thumbnail_container.find('.box-shadow').first();
					let function_buttons = thumbnail_container.find('.box-button').first();

					description.addClass('show');
					description_short.addClass('show');
					box_shadow.css('bottom','-'+ function_buttons.outerHeight() +'px');
				}
			});

			$(window).resize(function() {
				$(".shadow-hover .thumbnail-container.processed-shadown-hover").each(function(key, value) {
					if ($(window).width() >= 992){
						let function_buttons = $(this).find('.box-button').first();
						let description = $(this).find('.product-description').first();
						let description_short = $(this).find('.product-description-short').first();

						if($(this).find('div.box-shadow').length < 1){
							$(this).append('<div class="box-shadow" style="bottom: -'+ function_buttons.outerHeight() +'px"></div>');
						}
						if(description.find('a.show-all-description').length < 1){
							description.append('<a href="javascript:void(0)" class="show-all-description"><span class="left"></span><span class="center"></span><span class="right"></span></a>');
						}
						if(description_short.find('a.show-all-description').length < 1){
							description_short.append('<a href="javascript:void(0)" class="show-all-description"><span class="left"></span><span class="center"></span><span class="right"></span></a>');
						}
					}
				});
				$(".shadow-hover .thumbnail-container.processed-shadown-hover .product-description .show-all-description").on( "click", function() {
					if ($(window).width() >= 992){
						let thumbnail_container = $(this).closest('.thumbnail-container');
						let description = thumbnail_container.find('.product-description').first();
						let description_short = thumbnail_container.find('.product-description-short').first();
						let box_shadow = thumbnail_container.find('.box-shadow').first();
						let function_buttons = thumbnail_container.find('.box-button').first();

						description.addClass('show');
						description_short.addClass('show');
						box_shadow.css('bottom','-'+ function_buttons.outerHeight() +'px');
					}
				});
			});
		},
		eventProductListLoadMore: function(){
			let object = this;
			$(".btn-show-more").click(function() {
				let btn = $(this);
				let page = parseInt(btn.data('page'));
				let use_animation = parseInt(btn.data('use-animation'));
				let data_form = btn.closest(".DeoProductList").find(".data_form").val();

				$.ajax({
					headers: {"cache-control": "no-cache"},
					url: deo_url_ajax,
					async: true,
					cache: false,
					dataType: "Json",
					data: {data_form: data_form, "p": page, "use_animation": use_animation, widget : 'DeoProductList'},
					beforeSend: function(){
						btn.data('reset-text', btn.html());
						btn.html(btn.data('loading-text'));
					},
					success: function(response) {
						let boxCover = $(btn).closest(".box-show-more");
						if(!response.is_more) {
							$(boxCover).removeClass("open").fadeOut();
						}
						if(response.html) {
							$(boxCover).prev().append(response.html);
						}
						$(btn).data("page", (page + 1));

						object.processAjaxProduct();

						// re call run animation
						object.initAnimation();

						// init hover shadown product
						object.initShadownHover();
					}
				}).always(function () {
					// FIX 1.7
					btn.html(btn.data('reset-text'));
				});
			});
		},
		eventProductTabs: function(){
			let object = this;
			// js toggle tab
			$('.DeoProductTabs .nav-tabs a').on('click', function (e) {
				e.preventDefault();

				let tab = $(this);
				let tabs = tab.closest('.deo-tabs');
				let tab_active = $(e.target).data('tab');
				tabs.find('.product-tab-option').val(tab_active);
				if (tabs.hasClass('DeoProductTabs') && !$(this).hasClass('processed')){
					let data_form = tabs.find(".data_form").val();
					$.ajax({
						headers: {"cache-control": "no-cache"},
						url: deo_url_ajax,
						async: true,
						cache: false,
						dataType: "Json",
						data: {data_form : data_form, id_category : tab.data('tab'), widget : 'DeoProductTabs'},
						beforeSend: function(){
							tabs.addClass('loading');
						},
						success: function(response) {
							let tabcontent = $(tab.attr("href"));
							if (response.html) {
								tabcontent.append(response.html);
								tab.tab('show');
								tab.addClass('processed');

								if (tabcontent.find('.slick-carousel').length == 0){
									return;
								}
						
								object.callInitSlickCarousel(tabcontent.find('.slick-carousel'));

								object.processAjaxProduct();

								// re call run animation
								object.initAnimation();

								// init hover shadown product
								object.initShadownHover();
								
							}
						}
					}).always(function () {
						tabs.removeClass('loading');
					});
				}else{
					tab.tab('show');
				}
			});
		},
		messageSuccess: function (text,title = ""){
			$.growl.notice({ title:title, message:text});
		},
		messageError: function (text,title = ""){
			$.growl.error({ title:title, message:text});
		},
		messageWarning: function (text,title = ""){
			$.growl.warning({ title:title, message:text});
		},
	};
}(jQuery));
/**
 * End block functions common for front-end
 */


/**
 * End block for module ap_gmap
 */
function synSize(name) {
	let obj = $("#" + name);
	let div = $(obj).closest(".gmap-cover");
	let gmap = $(div).find(".gmap");
	$(obj).height($(gmap).height());
	//console.log($(gmap).height());
}

$(document).ready(function(){	
	// stela
	if ($('.deo-parallax').length){
		$.stellar({horizontalScrolling:false});
	}

	// // mouse
	// currentPosX = [];
	// currentPosY = [];
	// $("div[data-mouse-parallax-strength]").each(function(){
	// 	currentPos = $(this).css("background-position");
	// 	if (typeof currentPos == "string"){
	// 		currentPosArray = currentPos.split(" ");
	// 	}else{
	// 		currentPosArray = [$(this).css("background-position-x"),$(this).css("background-position-y")];
	// 	}
	// 	currentPosX[$(this).data("mouse-parallax-rid")] = parseFloat(currentPosArray[0]);
	// 	currentPosY[$(this).data("mouse-parallax-rid")] = parseFloat(currentPosArray[1]);
	// 	$(this).mousemove(function(e){
	// 		newPosX = currentPosX[$(this).data("mouse-parallax-rid")];
	// 		newPosY = currentPosY[$(this).data("mouse-parallax-rid")];
	// 		if ($(this).data("mouse-parallax-axis") != "axis-y"){
	// 			mparallaxPageX = e.pageX - $(this).offset().left;
	// 			if ($(this).hasClass("full-bg-screen")){
	// 				mparallaxPageX = mparallaxPageX - 1000;
	// 			}
	// 			newPosX = (mparallaxPageX * $(this).data("mouse-parallax-strength") * -1) + newPosX;
	// 		}
	// 		if ($(this).data("mouse-parallax-axis") !="axis-x"){
	// 			mparallaxPageY = e.pageY - $(this).offset().top;
	// 			newPosY = mparallaxPageY * $(this).data("mouse-parallax-strength") * -1;
	// 		}
	// 		$(this).css("background-position",newPosX+"px "+newPosY+"px");
	// 	});
	// });

	// let ytIframeId; let ytVideoId;
	// function onYouTubeIframeAPIReady() {
	// 	$("div.iframe-youtube-api-tag").each(function(){
	// 		ytIframeId = $(this).attr("id");
	// 		ytVideoId = $(this).data("youtube-video-id");

	// 		new YT.Player(ytIframeId, {
	// 			videoId: ytVideoId,
	// 			width: "100%",
	// 			height: "100%",
	// 			playerVars :{autoplay:1,controls:0,disablekb:1,fs:0,cc_load_policy:0,
	// 						iv_load_policy:3,modestbranding:0,rel:0,showinfo:0,start:0},
	// 			events: {
	// 				"onReady": function(event){
	// 					event.target.mute();
	// 					setInterval(
	// 						function(){event.target.seekTo(0);},
	// 						(event.target.getDuration() - 1) * 1000
	// 					);
	// 				}
	// 			}
	// 		});
	// 	});
	// }
	// onYouTubeIframeAPIReady();
	
	// if (typeof MediaElementPlayer !== 'undefined') {
	// 	//add function for html5 youtube video
	// 	let player1 = new MediaElementPlayer('#special-youtube-video1');
	// 	let player2 = new MediaElementPlayer('#special-youtube-video2');
	// 	if (player1){
	// 		let auto_find = setInterval(function(){
	// 			if ($('#video-1 .mejs-overlay-play').html()){
	// 				$('#video-1 .mejs-overlay-play>.mejs-overlay-button').before('<div class="video-name">'+$('#special-youtube-video1').data('name')+'</div>');
	// 				$('#video-1 .mejs-overlay-play').append('<div class="video-description">Watch video and <span>subscribe us<span></div>');   
	// 				clearInterval(auto_find);
	// 			}
	// 		}, 500);
	// 	}
		
	// 	if (player2){
	// 		let auto_find1 = setInterval(function(){        
	// 			if ($('#video-2 .mejs-overlay-play').html()){
	// 				$('#video-2 .mejs-overlay-play>.mejs-overlay-button').before('<div class="video-name">'+$('#special-youtube-video2').data('name')+'</div>');
	// 				$('#video-2 .mejs-overlay-play').append('<div class="video-description">Watch video and <span>subscribe us<span></div>');   
	// 				clearInterval(auto_find1);              
	// 			}
	// 		}, 500);
	// 	}
	// }

	//js for select header, footer, content in demo
	let current_url = window.location.href;
	$('.deo_config').each(function(){
		let enable_js = $(this).data('enable_js');
		if (enable_js == false){
			return;
		}
		let param_paneltool = '&paneltool_setting';
		current_url = $(this).data('url');
		if (!current_url ){
			current_url = window.location.href;
			current_url = current_url.replace(param_paneltool, "");
		}
		
		let param = $(this).data('type');
		let value = $(this).data('id');
		let re = new RegExp("([?|&])" + param + "=.*?(&|$)","i");
		if (current_url.match(re)){
			$(this).attr('href', current_url.replace(re,'$1' + param + "=" + value + '$2') + param_paneltool);
		}else{
			if (current_url.indexOf('?') == -1)
				$(this).attr('href', current_url + '?' + param + "=" + value + param_paneltool);
			else
				$(this).attr('href', current_url + '&' + param + "=" + value + param_paneltool);
		}
	});
	
	// fix owl carousel in tab load delay when resize.
	$(window).resize(function(){
		if ($('.tab-pane .owl-carousel').length){
			$('.tab-pane .owl-carousel').each(function(index, element){
				if (!$(element).parents('.tab-pane').hasClass('active') && typeof ($(element).data('owlCarousel')) !== "undefined"){
					let w_owl_active_tab = $(element).parents('.tab-pane').siblings('.active').find('.owl-carousel').width();
					$(element).width(w_owl_active_tab);
					$(element).data('owlCarousel').updateVars();
					$(element).width('100%');
				}
			});
		}
	});
});


$(document).ready(function(){
	// if(typeof (ap_list_functions) != 'undefined')
 //    {
 //        $.each(ap_list_functions, function(i, val) {
 //            val();
 //            ap_list_functions[i] = null;
 //        });
 //    }

	// Defind list functions will run when document.ready()
	if (typeof (deo_functions_document_ready) != 'undefined'){
		$.each(deo_functions_document_ready, function(i, val) {
            val();
            deo_functions_document_ready[i] = null;
        });
        
		// for (let i = 0; i < deo_functions_document_ready.length; i++) {
		// 	deo_functions_document_ready[i]();
		// }
	}
});


$(window).load(function(){
	// Defind list functions will run when window.load()
	if (typeof (deo_functions_windown_loaded) != 'undefined'){
		for (let i = 0; i < deo_functions_windown_loaded.length; i++) {
			deo_functions_windown_loaded[i]();
		}
	}
	prestashop.on('updateProductList', function() {
		// FIX BUG : FILTER PRODUCT NOT SHOW MORE IMAGE
		DeoTemplate.processAjax();
	});
});

$(document).ready(function(){
	// REPLACE URL IN BLOCKLANGUAGES
	if (typeof deo_profile_multilang_url != "undefined") {
		 $.each(deo_profile_multilang_url, function(index, profile){
		 	if (profile.friendly_url){
				let url_search = prestashop.urls.base_url + profile.iso_code;
				let url_change = prestashop.urls.base_url + profile.iso_code + '/' + profile.friendly_url + '.html';
					   
				// update for widget Customer Actions and default
				let parent_o = $('.language-selector-wrapper');
				if ($('.deo_customer_actions').length){
					parent_o = $('.deo_customer_actions .language-selector');
				}
				
				parent_o.find('li a').each(function(){
					let lang_href = $(this).attr('href');
					if(lang_href.indexOf(url_search) > -1 ){
						$(this).attr('href', url_change);
					}
				});
		 	}
		});
	}
	
	// update for widget Customer Actions and default
	let parent_o_currency = ($('.deo_customer_actions').length) ? $('.deo_customer_actions .currency-selector') : $('.currency-selector');
	
	// REPLACE URL IN BLOCKLANGUAGES
	parent_o_currency.find('li a').each(function(){
		
		let url_link = $(this).attr('href');
		let id_currency = getParamFromURL("id_currency", url_link);
		let SubmitCurrency = getParamFromURL("SubmitCurrency", url_link);
		
		let current_url = window.location.href;
		// fix for only product page, url has #
		if (prestashop.page.page_name == 'product'){            
			current_url = prestashop.urls.current_url;      
		}
		current_url = removeParamFromURL('SubmitCurrency',current_url);
		current_url = removeParamFromURL('id_currency',current_url);
		
		if (current_url.indexOf('?') == -1){
			let new_url = current_url + '?SubmitCurrency=' + SubmitCurrency + "&id_currency=" +id_currency;
			$(this).attr('href', new_url);
		}else{
			let new_url = current_url + '&SubmitCurrency=' + SubmitCurrency + "&id_currency=" +id_currency;
			$(this).attr('href', new_url);
		}
	});
	
	function removeParamFromURL(key, sourceURL) {

		let rtn = sourceURL.split("?")[0],
			param,
			params_arr = [],
			queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
		
		if (queryString !== "") {
			params_arr = queryString.split("&");
			for (let i = params_arr.length - 1; i >= 0; i -= 1) {
				param = params_arr[i].split("=")[0];
				if (param === key) {
					params_arr.splice(i, 1);
				}
			}
			if(params_arr.length > 0){
				rtn = rtn + "?" + params_arr.join("&");
			}
		}
		return rtn;
	}

	function getParamFromURL(key, sourceURL) {
		let rtn = sourceURL.split("?")[0],
			param,
			params_arr = [],
			queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";

		if (queryString !== "") {
			params_arr = queryString.split("&");
			
			for (let i = params_arr.length - 1; i >= 0; i -= 1) {
				param = params_arr[i].split("=")[0];
				if (param === key) {
					return params_arr[i].split("=")[1];
				}
			}
		}
		return false;
	}
});

//DeoCustomerActions
$(document).ready(function() {
	if (typeof enable_js_lang != 'undefined'){
		$('ul#first-currencies li:not(.selected)').css('opacity', 0.3);
		$('ul#first-currencies li:not(.selected)').hover(function(){
			$(this).css('opacity', 1);
		}, function(){
			$(this).css('opacity', 0.3);
		});
	}
	
	if (typeof enable_js_currency != 'undefined'){
		$("#setCurrency").mouseover(function(){
			$(this).addClass("countries_hover");
			$(".currencies_ul").addClass("currencies_ul_hover");
		});
		$("#setCurrency").mouseout(function(){
			$(this).removeClass("countries_hover");
			$(".currencies_ul").removeClass("currencies_ul_hover");
		});
	}
	
	if (typeof js_country != 'undefined'){
		$("#countries").mouseover(function(){
			$(this).addClass("countries_hover");
			$(".countries_ul").addClass("countries_ul_hover");
		});
		$("#countries").mouseout(function(){
			$(this).removeClass("countries_hover");
			$(".countries_ul").removeClass("countries_ul_hover");
		});
	}

	function setCurrency(id_currency){
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: prestashop['urls']['base_url'] + 'index.php' + '?rand=' + new Date().getTime(),
			data: 'controller=change-currency&id_currency='+ parseInt(id_currency),
			success: function(msg){
				location.reload(true);
			}
		});
	}
})

// deo-dropdown
$(document).ready(function() {
	$('.deo-dropdown').click(function(e){
		e.stopPropagation();
		$(this).toggleClass('expanded');
	});
	$(document).click(function() {
		$('.deo-dropdown').removeClass('expanded');
	});

	$('.deo-dropdown .deo-radio-dropdown').change(function(){
		let dropdown = $(this).closest('.deo-dropdown');
		let selected_value = dropdown.find('.selected-value');
		if ($(this).is(':checked')){
			let text = $(this).data('name');
			selected_value.html(text);
		}
		dropdown.removeClass('expanded');
	});
	$('.deo-dropdown .deo-radio-dropdown').each(function(){
		if ($(this).is(':checked')){
			let text = $(this).data('name');
			let dropdown = $(this).closest('.deo-dropdown');
			let selected_value = dropdown.find('.selected-value');
			selected_value.html(text);

			return true;
		}
	});
});


// slideshow
$(document).ready(function() {
	let slideshow;
	// if ($('.deo_slideshow').data('type-carousel') == 'slickcarousel'){
		slideshow = $('.deo_slideshow').find('.slick-carousel');
		slideshow.on('afterChange', function(event, slick, currentSlide){
			let slide = slick.$slides.get(currentSlide);
			animationAfterTextSlide(slide);
		});
		slideshow.on('beforeChange', function(event, slick, currentSlide, nextSlide){
			let slide = slick.$slides.get(nextSlide);
			animationBeforeTextSlide(slide);
		});
		if (slideshow.data('lazyload')){
			slideshow.on('lazyLoaded', function(event, slick, direction, imageSource){
				let slide = slick.$slides.get(0);
				animationBeforeTextSlide(slide);
				animationAfterTextSlide(slide);
			});
		}else{
			slideshow.on('init', function(event, slick){
				let slide = slick.$slides.get(0);
				animationBeforeTextSlide(slide);
				animationAfterTextSlide(slide);
			});
		}
	// }else if ($('.deo_slideshow').data('type-carousel') == 'owlcarousel'){
	// 	slideshow = $('.deo_slideshow').find('.owl-carousel');
	// 	if (slideshow.data('lazyload')){
	// 		slideshow.on('owl.afterLazyLoad', function(event, owl, base){
	// 			let slide = $(base.$owlItems[0]);
	// 			animationBeforeTextSlide(slide);
	// 			animationAfterTextSlide(slide);
	// 		});
	// 	}else{
	// 		slideshow.on('owl.afterInit', function(event, owl, base){
	// 			let slide = $(base.$owlItems[0]);
	// 			animationBeforeTextSlide(slide);
	// 			animationAfterTextSlide(slide);
	// 		});
	// 	}
	// 	slideshow.on('owl.beforeMove', function(event, owl, base, currentItem){
	// 		let slide = $(base.$owlItems[currentItem]);
	// 		animationBeforeTextSlide(slide);
	// 		animationAfterTextSlide(slide);
	// 	});
	// 	slideshow.on('owl.afterMove', function(event, owl, base, currentItem){
	// 		let slide = $(base.$owlItems[currentItem]);
	// 		animationBeforeTextSlide(slide);
	// 		animationAfterTextSlide(slide);
	// 	});
	// }else if($('.deo_slideshow').data('type-carousel') == 'boostrap'){
	// 	slideshow = $('.deo_slideshow').find('.carousel');
	// 	slideshow.on('initCarousel', function(){
	// 		let slide = $(this).find('.carousel-item').first();
	// 		animationBeforeTextSlide(slide);
	// 		animationAfterTextSlide(slide);
	// 	});
	// 	slideshow.trigger('initCarousel');
	// 	// before
	// 	slideshow.on('slide.bs.carousel', function () {
	// 		let slide = $(this).find('.carousel-item.active');
	// 		animationBeforeTextSlide(slide);
	// 		animationAfterTextSlide(slide);
	// 	});
	// 	// after
	// 	slideshow.on('slid.bs.carousel', function () {
	// 		let slide = $(this).find('.carousel-item.active');
	// 		animationBeforeTextSlide(slide);
	// 		animationAfterTextSlide(slide);
	// 	});
	// }

	function animationBeforeTextSlide(slide){
		let text_slide = $(slide).find('.text-slide');
		text_slide.each(function(){
			$(this).removeClass('animated animated-finish').removeClass($(this).data('effect')).addClass('animate-wait');
		});
	}

	function animationAfterTextSlide(slide){
		let text_slide = $(slide).find('.text-slide');
		delay_time = 1000;
		text_slide.each(function(){
			let delay = $(this).data('delay');
			setTimeout(()=>{
				$(this).removeClass('animate-wait').addClass('animated').addClass($(this).data('effect'));
			}, delay_time);
			delay_time = delay_time + delay;

			setTimeout(()=>{
				$(this).addClass('animated-finish');
			}, delay_time);
		});
	}
});


$(document).ready(function(){
    if (prestashop.page.page_name == 'category'){
        if ($.cookie(deo_cookie_theme +'_grid_list') == 'grid'){
			$('.deo_grid').trigger('click');
		}
		if ($.cookie(deo_cookie_theme +'_grid_list') == 'list'){
			$('.deo_list').trigger('click');
		}
    }
});

function SetOwlCarouselFirstLast(el){
	el.find(".owl-item").removeClass("first");
	el.find(".owl-item.active").first().addClass("first");

	el.find(".owl-item").removeClass("last");
	el.find(".owl-item.active").last().addClass("last");
}

