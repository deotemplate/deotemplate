/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

var DeoLazy = (callback, args) => {
	if ('IntersectionObserver' in window && deo_lazy_intersection_observer){
		let observer = new IntersectionObserver((entries)  => {
			entries.forEach(entry => {
				if (entry.isIntersecting){
					callback($(entry.target));
				}
			});
		});

		observer.observe(args);
		// $(args).each((key, elem) => {
		// 	observer.observe(elem);
		// });
	}else{
		callback($(args));
	}
};


$(document).ready(function(){
	$(".effect-parallax-image img").each(function() {
		let callback = function(elem){
			elem.panr({
				sensitivity: 20,
				scale: !1,
				scaleOnHover: !0,
				scaleTo: 1.1,
				scaleDuration: .25,
				panY: !0,
				panX: !0,
				panDuration: 1.25,
				resetPanOnMouseLeave: !0,
				onEnter: function(){},
				onLeave: function(){}
			});
		};

		DeoLazy(callback, this);
	});

	$(".DeoGallery").each(function() {
		let callback = function(elem){
			let gallery = elem.find('.galleries');
			let filterButtons = elem.find('.filter-tags .filter-item > a');

			gallery.find('.link-image').fancybox({
				type: "image",
				helpers : {
					overlay: {
						locked: false
					}
				},
			});

			gallery.imagesLoaded().progress(function() {
				gallery.isotope('layout');
			});

			gallery.isotope({
				itemSelector: '.gallery-item',
				percentPosition: true,
				masonry: {
					columnWidth: '.gallery-size',
					horizontalOrder: true,
				}
			});
			
			filterButtons.click(function () {
				let search = $(this).data('filter');
				let parent = $(this).closest('.filter-tags');
				
				filterButtons.removeClass('is-checked');
				$(this).addClass('is-checked');
				gallery.isotope({
					filter: function () {
						if (search === "*") {
							return true;
						} else {
							let label = $(this).find(".description").text() + $(this).data("tags");
							return label.search(search) !== -1;
						}
					}
				});
			});

			function updateFilterCounts(update=true)  {
				// get filtered item elements
				let itemElems = gallery.isotope('getFilteredItemElements');
				let $itemElems = $(itemElems);
				let count = 0;
		
				filterButtons.each(function(i,button){
					let $button = $(button);
					let filterValue = $button.attr('data-filter');

					if (!filterValue) {
						return;
					}

					if (filterValue == '*'){
						$button.find('.filter-count').addClass('processed-filter-count').text($(itemElems).length);
						return;
					}

					if (update){
						$itemElems.each(function(i,item){
							let label = $(item).find(".description").text() + " " + $(item).find(".sub-title").text() + " " + $(item).find(".title").text() + " " + $(item).data("tags");
							if (label.indexOf(filterValue) === -1){
								count += 1; 
							}
						});

						$button.find('.filter-count').addClass('processed-filter-count').text(count);
					}
				});
			}
			updateFilterCounts();
		}

		DeoLazy(callback, this);
	});
	$(".deo-google-map").each(function() {
		let callback = function(elem){
			let gmap = elem;
			let id_gmap = gmap.attr('id');
			let zoom = gmap.data('zoom');
			let marker_list = gmap.data('marker-list');
			let marker_center = gmap.data('marker-center');
			let is_display_store = gmap.data('is-display-store');

			deo_gmap[id_gmap] = [];
			initDeoGmap('', marker_list, deo_gmap[id_gmap], "map-canvas-"+id_gmap, zoom, marker_center, id_gmap);
			if (is_display_store) {
				initDeoListStore(marker_list, "google-map-stores-list-"+id_gmap, id_gmap);
			}
		}

		DeoLazy(callback, this);
	});

	$(document).on("click",".deo-item-store",function() {
		let id_gmap = $(this).data('id-gmap');
		let id_store = $(this).data('id-store');
		let market = deo_gmap[id_gmap][id_store];
		google.maps.event.trigger(market, 'click');
	});

	function initDeoListStore(data, name, id_gmap) {
		let obj = $("#" + name);
		let stores_html = '';
		stores_html += '<ul>';
		for (let i = 0; i < data.length; i++) {
			let s = data[i];
			stores_html += '<li class="deo-item-store" data-id-gmap="'+id_gmap+'" data-id-store="'+i+'">';
				stores_html += '<h3 class="name-store"><span class="number-store">'+(i+1)+'</span> '+s.name+'</h3>';
				stores_html += '<p class="address-store">'+s.address+'</p>';
			stores_html += '</li>';
		}
		stores_html += '</ul>';
		obj.empty();
		obj.append(stores_html);
	}

	function initDeoGmap(map, data, stores, nameGmap, zoom, marker_center, id_gmap){
		map = new google.maps.Map(document.getElementById(nameGmap), {
			center: new google.maps.LatLng(marker_center.latitude, marker_center.longitude),
			zoom: zoom,
			mapTypeId: 'roadmap'
		});
		
		google.maps.event.addListenerOnce(map, 'idle', function(){
			$('#'+id_gmap).addClass('proccessed');
			setTimeout(function(){$('#'+id_gmap+' .dismissButton').trigger('click');}, 2000);
		});

		if (data.length > 0){
			setTimeout(createDeoStore(map, deo_gmap[id_gmap], data), 1000);
		}else{
			stores[0] = new google.maps.Marker({
				position: new google.maps.LatLng(marker_center.latitude, marker_center.longitude),
				animation: google.maps.Animation.DROP,
				map: map,
			});
		}
	};

	function createDeoStore(map, stores, data) {
		// dataMarkers
		for (let i = 0; i < data.length; i++) {
			let obj = data[i];
			let lg = parseFloat(obj.longitude);
			let lt = parseFloat(obj.latitude);
			let name = obj.name;
			let address = obj.address;
			let other = obj.other;
			let id_store = obj.id_store;
			let has_store_picture = obj.has_store_picture;

			let latlng = new google.maps.LatLng(lt, lg);
			let html = '';
			html += '<div class="detail-store">';
				html += '<h3 class="name-store">'+name+'</h3>';
				html += '<p class="address-store">'+address+'</p>';
				html += (has_store_picture ? '<p class="image-store"><img src="'+deoGoogleMap.img_store_dir + parseInt(id_store)+'.jpg" alt="'+name+'"/><p>' : '');
				html += '<div class="desc-store">'+other+'</div>';
				html += '<div class="view-more"><a href="http://maps.google.com/maps?saddr=&daddr='+latlng+'" target="_blank">' + deoGoogleMap.translation_5 +'</a></div>';
			html += "</div>";

			let infowindow = new google.maps.InfoWindow({
				content: "loading..."
			});

			let marker = new google.maps.Marker({
				position: new google.maps.LatLng(lt, lg),
				animation: google.maps.Animation.DROP,
				map: map,
				icon: deoGoogleMap.img_ps_dir + deoGoogleMap.logo_store,
				title: obj.name,
				html: html
			});

			google.maps.event.addListener(marker, "click", function () {
				infowindow.setContent(this.html);
				infowindow.open(map, this);
			});
			stores[i] = marker;
		}
	}


	$(".deo-instagram").each(function() {
		let instafeed = $(this);
		let id_instafeed = instafeed.attr('id');

		let carousel_type = instafeed.data('carousel_type');
		// let show_like = instafeed.data('show_like');
		// let show_comment = instafeed.data('show_comment');
		let show_icon = instafeed.data('show_icon');
		let show_title = instafeed.data('show_title');
		let client_id = instafeed.data('client_id');
		let access_token = instafeed.data('access_token');
		let user_id = instafeed.data('user_id');
		let limit = instafeed.data('limit');
		let sort_by = instafeed.data('sort_by');
		// let resolution = instafeed.data('resolution');
		let array_fake_item = instafeed.data("array_fake_item");
		let lazyload = instafeed.data('lazyload');
		let showloading = instafeed.data("showloading");
		let text_instagram = instafeed.data("text_instagram");
		let itempercolumn = instafeed.data("itempercolumn");

		let template, img, text_icon, text_like, text_comment, text_title, text_template;
		if (lazyload && carousel_type !== "list"){
			// if (carousel_type == "slickcarousel"){
			// 	img = '<span class="lazyload-wrapper" style="padding-bottom: 100%;"><span class="lazyload-icon"></span></span><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="img-fluid" data-lazy="{{image}}" class="img-fluid"/>';
			// }else{
			// 	img = '<span class="lazyload-wrapper" style="padding-bottom: 100%;"><span class="lazyload-icon"></span></span><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="img-fluid lazyOwl" data-src="{{image}}" class="img-fluid"/>';
			// }
			img = '<span class="lazyload-wrapper" style="padding-bottom: 100%;"><span class="lazyload-icon"></span></span><img title="{{caption}}" alt="{{caption}}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="img-fluid" data-lazy="{{image}}" class="img-fluid"/>';
		}else{
			img = '<img title="{{caption}}" alt="{{caption}}" src="{{image}}" class="img-fluid"/>'
		}

		text_icon = (show_icon) ? '<span class="icon"><i class="deo-custom-icons"></i></span>' : '';
		text_title = (show_title) ? '<span class="text-instagram">'+text_instagram+'</span>' : '';
		// text_like = (show_like) ? '<span class="like">{{likes}}</span>' : '';
		// text_comment = (show_comment) ? '<span class="comment">{{comments}}</span>' : '';
		// text_template = text_icon + text_title + text_like + text_comment;
		text_template = text_icon + text_title;
	
		template = '<div class="block-container"><div class="image"><a href="{{link}}" class="link-img-instagram" target="_blank">' + img + '<span class="content-instagram">' + text_template + '</span></a></div></div>';

		let feed = new Instafeed({
			clientId: client_id,
			target: id_instafeed,
			template: template,
			get: "user",
			accessToken: access_token,
			userId: user_id,
			sortBy: sort_by,
			limit: limit,
			// resolution: resolution,

			after: function() {
				if (carousel_type == "list"){

				}else if (carousel_type == "owlcarousel"){
					let photos = [];
					instafeed.find(".block-container").each(function() {
						photos.push($(this).prop('outerHTML'));
					});
					instafeed.html('');
					instafeed.addClass('deo-carousel deo-carousel-loading');
					if (showloading){
						instafeed.addClass('show-icon-loading');
					}
					let class_loading = 'owl-item';
					$.each(array_fake_item, function(index, value) {
						class_loading += ' loading-'+index+'-'+value;
					});
					
					if (itempercolumn > 1){
						// CASE : 2,3 images in one column
						photos = array_chunk(photos,itempercolumn);
						let total_column = photos.length;

						for (i = 0; i < total_column; i++){
							let img_html = '';
							img_html += '<div class="'+class_loading+'">';
							img_html += '<div class="item">';
							for(j = 0; j < photos[i].length; j++){
								img_html += photos[i][j];
							}
							img_html += '</div>';

							instafeed.html(instafeed.html() + img_html);
						}
					}else{
						for (i = 0; i < photos.length; i++){
							let img_html = '';
							img_html += '<div class="'+class_loading+'">';
							img_html += '<div class="item">';
								img_html += photos[i];
							img_html += '</div>';
							instafeed.html(instafeed.html() + img_html);
						}
					}
					instafeed.wrap('<div class="owl-row"></div>');
					DeoTemplate.callInitOwlCarousel(instafeed);
				}else if (carousel_type == "slickcarousel"){
					let photos = [];
					instafeed.find(".block-container").each(function() {
						photos.push($(this).prop('outerHTML'));
					});
					instafeed.html('');
					instafeed.addClass('deo-carousel deo-carousel-loading');
					if (showloading){
						instafeed.addClass('show-icon-loading');
					}
					let class_loading = 'slick-slide';
					$.each(array_fake_item, function(index, value) {
						class_loading += ' loading-'+index+'-'+value;
					});
					
					if (itempercolumn > 1){
						// CASE : 2,3 images in one column
						photos = array_chunk(photos,itempercolumn);
						let total_column = photos.length;

						for (i = 0; i < total_column; i++){
							let img_html = '';
							img_html += '<div class="'+class_loading+'">';
							img_html += '<div class="item">';
							for(j = 0; j < photos[i].length; j++){
								img_html += photos[i][j];
							}
							img_html += '</div>';

							instafeed.html(instafeed.html() + img_html);
						}
					}else{
						for (i = 0; i < photos.length; i++){
							let img_html = '';
							img_html += '<div class="'+class_loading+'">';
							img_html += '<div class="item">';
								img_html += photos[i];
							img_html += '</div>';
							instafeed.html(instafeed.html() + img_html);
						}
					}
					instafeed.wrap('<div class="slick-row"></div>');
					DeoTemplate.callInitSlickCarousel(instafeed);
				}
				
			}
		});

		feed.run();
		let array_chunk = function(arr, chunkSize) {
			let groups = [], i;
			for (i = 0; i < arr.length; i += chunkSize) {
				groups.push(arr.slice(i, i + chunkSize));
			}
			return groups;
		}


		// auto expired access_token
		let refresh_api_token = instafeed.data("refresh_api_token");
		if (refresh_api_token && access_token){
			$.ajax({
				type: 'GET',
				headers: {"cache-control": "no-cache"},
				url: 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token='+access_token,
				async: true,
				cache: false,
				dataType: 'json',
				success: function (resp){
					
				},
			});		
		}
	});
	
	$(".deo-tabs").each(function() {
		let callback = function(elem){
			let tabs = elem;
			// let id_tab = tab.attr('id');
			let fade_effect = tabs.data('fade_effect');


			// ACTION USE EFFECT
			if (fade_effect){
				tabs.find(".tab-pane").addClass("fade");
			}

			// ACTION SET ACTIVE
			let active_tab = tabs.data('active_tab');
			if (active_tab >= 0 && typeof active_tab != 'undefined'){
				let tab = tabs.find('.nav-tabs li:eq('+active_tab+') a[data-toggle="tab"]');
				tab.tab('show');

				let tab_active = tab.data('tab');
				tabs.find('.product-tab-option').val(tab_active);
			}
		}

		DeoLazy(callback, this);
	});

	// js toggle select change tab
	$('.deo-tabs .product-tab-option').change(function(){
		let tabs = $(this).closest('.deo-tabs');
		let option_checked = $(this).find(':selected').attr('value');
		if (tabs.hasClass('DeoProductTabs')){
			tabs.find('.nav-tabs li a[data-tab="'+option_checked+'"]').trigger('click');
		}else{
			tabs.find('.nav-tabs li a[data-tab="'+option_checked+'"]').tab('show');
		}
	});

	// js toggle tab
	$('.deo-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		// e.target // newly activated tab
		// e.relatedTarget // previous active tab
		let tabs = $(this).closest('.deo-tabs');
		let tab_active = $(e.target).data('tab');
		tabs.find('.product-tab-option').val(tab_active);
	});

	// $(".deo-category-image,.widget-category_image").each(function() {
	// 	let callback = function(elem){
	// 		let category = elem;
	// 		// let id_category = category.attr('id');
			
	// 		let limit = category.data('limit');
	// 		let cate_depth = category.data('cate_depth');
	// 		let count = 0;
	// 		let element = 0;
	// 		let cate_items = category.find('.cate-items');
	// 		let view_all = category.data('viewall');
	// 		let link_view_all = category.data('link_viewall');
	// 		let view_all_wapper = category.find('.view_all_wapper');

	// 		view_all_wapper.find(".btn.active-js-view-all").on( "click", function() {
	// 			let btn = view_all_wapper.find('.btn.active-js-view-all');
	// 			if (btn.hasClass('hide-less')){
	// 				category.find('.hidden-cate-item').hide(300, function(){
	// 					btn.html(btn.data('view-more')).removeClass('hide-less');
	// 				});
	// 			}else{
	// 				category.find('.hidden-cate-item').show(300, function(){
	// 					btn.html(btn.data('hide-less')).addClass('hide-less');
	// 				});
	// 			}
	// 		});
	// 	}

	// 	DeoLazy(callback, this);
	// });

	$('body').on("click", ".deo-category-image .view_all_wapper .btn,.widget-category_image .view_all_wapper .btn", (function(e) {	
		let category = $(this).closest(".deo-category-image,.widget-category_image").first();
		let limit = category.data('limit');
		let cate_depth = category.data('cate_depth');
		let count = 0;
		let element = 0;
		let cate_items = category.find('.cate-items');
		let view_all = category.data('viewall');
		let link_view_all = category.data('link_viewall');
		let view_all_wapper = category.find('.view_all_wapper');
		let btn = view_all_wapper.find(".btn.active-js-view-all");
		if (btn.length == 0){
			return false;
		}
		// let btn = view_all_wapper.find('.btn.active-js-view-all');
		
		if (btn.hasClass('hide-less')){
			category.find('.hidden-cate-item').hide(300, function(){
				btn.html(btn.data('view-more')).removeClass('hide-less');
			});
		}else{
			category.find('.hidden-cate-item').show(300, function(){
				btn.html(btn.data('hide-less')).addClass('hide-less');
			});
		}
	}));

	$(".deo-twitter").each(function() {
		let callback = function(elem){
			let twitter = elem;
			let id_twitter = twitter.attr('id');
			let border_color = twitter.data('border_color');
			let show_border = twitter.data('show_border');
			let name_color = twitter.data('name_color');
			let mail_color = twitter.data('mail_color');
			let text_color = twitter.data('text_color');
			let link_color = twitter.data('link_color');

			let deo_flag_twitter_set_css = 1;
			twitter.bind("DOMSubtreeModified", function() {
				deo_flag_twitter_set_css++;
				
				let isRun = 10;
				
				// let is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
				// let is_explorer = navigator.userAgent.indexOf('MSIE') > -1;
				// let is_firefox = navigator.userAgent.indexOf('Firefox') > -1;
				let is_safari = navigator.userAgent.indexOf("Safari") > -1;
				// let is_opera = navigator.userAgent.toLowerCase().indexOf("op") > -1;
				// if ((is_chrome)&&(is_safari)) {is_safari=false;}
				// if ((is_chrome)&&(is_opera)) {is_chrome=false;}

				if (window.chrome || is_safari){
					isRun = 5;
				}
				
				if (deo_flag_twitter_set_css == isRun){
					// Run only one time
					twitter.find('iframe').ready(function() {
						if (border_color && show_border){
							// SHOW BORDER COLOR
							twitter.find('iframe').css('border', '1px solid '+border_color);
						}

						if (name_color){
							twitter.find('iframe').contents().find('.TweetAuthor-name.Identity-name.customisable-highlight').css('color',name_color);
						}

						if (mail_color){
							twitter.find('iframe').contents().find('.TweetAuthor-screenName.Identity-screenName').css('color', mail_color);
						}

						if (text_color){
							twitter.find('iframe').contents().find("body").css('color',text_color);
						}

						if (link_color){
							twitter.find('iframe').contents().find(".timeline-Tweet-text a").css('color',link_color);
						}
					});
				}
			});	
		}

		DeoLazy(callback, this);
	});

	// Set text translate for image 360
	if ($('.DeoImage360').length){
		Magic360.lang = {
			'hint-text' : deo_image360_hint_text,
			'mobile-hint-text' : deo_image360_mobile_hint_text,
		}
	}

	$(".deo-accordion").each(function() {
		let callback = function(elem){
			let accordion = elem;
			let id_accordion = accordion.attr('id');
			let active_type = accordion.data('active_type');
			let active_accordion = accordion.data('active_accordion');
			if (typeof active_type == 'undefined' || typeof active_accordion == 'undefined'){
				return false;
			}

			// ACTION SET ACTIVE
			if (active_type == 'set'){
				accordion.find('.panel:nth-child('+active_accordion+') .panel-heading .panel-title a').trigger('click');
			}

			// ACTION SHOWALL
			if (active_type == 'showall'){
				accordion.find('.panel-heading .panel-title > a').on('click', function(e) {
					e.stopPropagation();
					e.preventDefault();
					// show, hidden content
					var div_id = $(this).attr('href');
					$(div_id ).collapse("toggle");
				});
				accordion.find('.panel-heading .panel-title > a').trigger('click');
			}
		}

		DeoLazy(callback, this);
	});
	$(".deo-popup").each(function() {
		let callback = function(elem){
			let popup = elem;
			let simple_popup = popup.data('simple_popup');
			let show_btn_open_popup = popup.data('show_btn_open_popup');
			if (simple_popup){
				initSimplePopup(popup);
			}else{
				if (!show_btn_open_popup){
					initPopup(popup);
				}
			}
		}

		DeoLazy(callback, this);
	});
	$('.deo-show-popup').click(function(e){
		if ($('.deo-show-popup.active').length || $('.deo-show-popup.deo-loading-button').length){
			return false;
		}

		let id_popup = $(this).data('popup');
		$(this).addClass('deo-loading-button active');
		initPopup($('#'+id_popup));
	});
	function initPopup(popup){
		let id_popup = popup.attr('id');
		let simple_popup = popup.data('simple_popup');
		let content = popup.find('.wrapper-popup').first();
		let active = popup.data('active');
		let show_desktop = popup.data('show_desktop');
		let show_tablet = popup.data('show_tablet');
		let show_mobile = popup.data('show_mobile');
		let show_btn_open_popup = popup.data('show_btn_open_popup');
		let closebtn = popup.data('closebtn');
		let time_wait = popup.data('time_wait');
		let time_close = popup.data('time_close');
		let time_life = popup.data('time_life');
		let hide_popup_when_close = popup.data('hide_popup_when_close');
		let time_show_again = popup.data('time_show_again');
		let width = popup.data('width');
		let height = popup.data('height');
		let wrapcss = popup.data('wrapcss');
		let effect = popup.data('effect');
		let padding = popup.data('padding');
		let margin = popup.data('margin');
		let bg_color_overlay_popup = popup.data('bg_color_overlay_popup');
		let top = popup.data('top');
		let left = popup.data('left');
		let position_popup = popup.data('position_popup');
		let overlay_popup = popup.data('overlay_popup');
		let bg_data = popup.data('bg_data');
		let lazyload = popup.data('lazyload');
		let bg_img = popup.data('bg_img');
		let show_homepage = (typeof popup.data('show_homepage') != 'undefined') ? popup.data('show_homepage') : false;

		if (position_popup) top = left = 0.5;
		if (prestashop.page.page_name != 'index' && !show_btn_open_popup){
			active = (show_homepage) ? false : true; 
		}

		function checkResponsive(fancybox,show_desktop,show_tablet,show_mobile) {
			let width = $(window).width();

			if (show_desktop && show_tablet && show_mobile){
				// fancybox();
			}else if (show_desktop && !show_tablet && !show_mobile){
				if (width < 992){
					fancybox.close(true);
				}
			}else if (show_desktop && show_tablet && !show_mobile){
				if (width < 576){
					fancybox.close(true);
				}
			}else if (!show_desktop && show_tablet && !show_mobile){
				if ((width < 992) && (width > 576)){
					fancybox.close(true);
				}
			}else if (show_desktop && !show_tablet && show_mobile){
				if ((width > 992) && (width < 576)){
					fancybox.close(true);
				}
			}else if (!show_desktop && show_tablet && show_mobile){
				if ((width >= 992)){
					fancybox.close(true);
				}
			}else if (!show_desktop && !show_tablet && show_mobile){
				if ((width >= 576)){
					fancybox.close(true);
				}
			}else if (!show_desktop && !show_tablet && !show_mobile){
				fancybox.close(true);
			}
		}
		if (active) {
			setTimeout( function() {
				fancybox();
			}, time_wait);
		}

		function fancybox() {
			let helpers = false;
			if (overlay_popup){
				helpers = {
					overlay : {
						locked: false,
						css : {
							'background' : bg_color_overlay_popup,
						}
					}
				}
			}
			$.fancybox({
				wrapCSS : wrapcss,
				content : content,
				type: 'iframe',
				autoSize : false,
				aspectRatio : false,
				scrolling : 'no',
				topRatio : top,
				leftRatio : top,
				openSpeed : "slow",
				closeSpeed : "slow",
				openMethod  : effect+'In',
				closeMethod : effect+'Out',
				margin : margin,
				padding: padding,
				closeBtn : closebtn,
				helpers : helpers,
				minHeight: '50px',
				afterLoad : function(instance, current) {
					let fancybox_wrap = instance.wrap;
						
					// background
					if (bg_data){
						$(fancybox_wrap).attr('style',bg_data);
					}
					if (lazyload && bg_img){
						$(fancybox_wrap).addClass('lazyload');
						$(fancybox_wrap).attr('data-bgset',bg_img);
					}

					if (show_btn_open_popup){
						$('.deo-show-popup[data-popup="'+id_popup+'"]').removeClass('deo-loading-button');
					}
				},
				beforeLoad : function() {
					//set width height
					this.width  = width;  
					this.height  = height;
					//check cookie
					if ($.cookie('popup_'+id_popup) && $.cookie('popup_'+id_popup) == 1 && !show_btn_open_popup){
						parent.$.fancybox.close();
					}

					//check screen
					checkResponsive(parent.$.fancybox,show_desktop,show_tablet,show_mobile);

				},
				onUpdate : function() {
					//check screen
					checkResponsive(parent.$.fancybox,show_desktop,show_tablet,show_mobile);

					// automatically close popup
					if (time_close){
						setTimeout( function() {
							parent.$.fancybox.close(); 
						}, time_close);
					}
					
				},
				beforeClose : function() {
					//set cookie
					let fancybox_wrap = $(this.wrap);
					let check_box = $(fancybox_wrap).find('.show_message_again').first();
					if (hide_popup_when_close && !show_btn_open_popup){
						if (check_box.length){
							if (time_life > 0 && check_box.is(":checked")){
								$.cookie('popup_'+id_popup, '1', { expires: time_life });
							}
						}else{
							if (time_life > 0){
								$.cookie('popup_'+id_popup, '1', { expires: time_life });
							}
						}
					}
				},
				afterClose : function(instance, current) {
					// automatically open popup
					if (time_show_again){
						setTimeout( function() {
							fancybox();
						}, time_show_again);
					}

					if (show_btn_open_popup){
						$('.deo-show-popup[data-popup="'+id_popup+'"]').removeClass('deo-loading-button active');
					}
				},
			});
		}

		// remove cookie
		// $.cookie('popup_'+id_popup, '0');
	}
	function initSimplePopup(popup){
		let id_popup = popup.attr('id');
		let simple_popup = popup.data('simple_popup');
		let content = popup.find('.wrapper-popup').first();
		let active = popup.data('active');
		let show_btn_open_popup = popup.data('show_btn_open_popup');
		let closebtn = popup.data('closebtn');
		let time_wait = popup.data('time_wait');
		let time_close = popup.data('time_close');
		let time_life = popup.data('time_life');
		let hide_popup_when_close = popup.data('hide_popup_when_close');
		let time_show_again = popup.data('time_show_again');
		let width = popup.data('width');
		let height = popup.data('height');
		let wrapcss = popup.data('wrapcss');
		let bg_color_overlay_popup = popup.data('bg_color_overlay_popup');
		let position_popup_simple = popup.data('position_popup_simple');
		let top_simple = popup.data('top_simple');
		let left_simple = popup.data('left_simple');
		let right_simple = popup.data('right_simple');
		let bottom_simple = popup.data('bottom_simple');
		let show_homepage = popup.data('show_homepage');
		let overlay_popup = popup.data('overlay_popup');
		let bg_data = popup.data('bg_data');
		let lazyload = popup.data('lazyload');
		let bg_img = popup.data('bg_img');

		if (prestashop.page.page_name != 'index'){
			active = (show_homepage) ? false : true; 
		}

		if (active && $.cookie('popup_'+id_popup) && $.cookie('popup_'+id_popup) == 0 && !show_btn_open_popup) {
			// setTimeout( function() {
				$('#'+id_popup).removeClass('hidden-popup');
				$('#'+id_popup+'-bg-overlay-popup').removeClass('hidden-overlay-popup');
			// }, time_wait);
		}

		// automatically close popup
		if (time_close){
			setTimeout( function() {
				$('#'+id_popup).addClass('hidden-popup'); 
				$('#'+id_popup+'-bg-overlay-popup').addClass('hidden-overlay-popup');
			}, time_close);
		}

		$('#'+id_popup+' .deo-close-popup, #'+id_popup+' .close-popup').click(function(){
			let check_box = $('#'+id_popup+' .show_message_again');
			if (hide_popup_when_close && !show_btn_open_popup){
				if (check_box.length){
					if (time_life > 0 && check_box.is(":checked")){
						$.cookie('popup_'+id_popup, '1', { expires: time_life });
					}
				}else{
					if (time_life > 0){
						$.cookie('popup_'+id_popup, '1', { expires: time_life });
					}
				}
			}
			$('#'+id_popup).addClass('hidden-popup'); 
			$('#'+id_popup+'-bg-overlay-popup').addClass('hidden-overlay-popup');
		});

		// $('#'+id_popup+'-bg-overlay-popup').click(function(){
		// 	$('#'+id_popup).addClass('hidden-popup'); 
		// 	$('#'+id_popup+'-bg-overlay-popup').addClass('hidden-overlay-popup');
		// });

		// remove cookie
		// $.cookie('popup_'+id_popup, '0');
		// $.cookie('popup_demo_cookie', '0');
	}
});

// load more menu
$(window).load(function(){
	$('.menu-vertical-block .view-all').click(function(e){
		e.preventDefault();
		$(this).prevAll().show(400);
		$(this).hide();
	});

	init_load_more_menu();
	$(window).resize(function(){
		setTimeout(function() {
			init_load_more_menu();
		}, 500);
	});

	function init_load_more_menu(){
		let menu = $('.menu-vertical-block');
		if ($(window).width() >= 992){
			// $('.menu-vertical-block img').imagesLoaded(function(){ 
				let block = menu.closest('.DeoRow').find('.block-static-menu-load-more');
				let view_more = menu.find('.megamenu.vertical .view-all');
				block = (block.length) ? block : $('.block-absolute-menu-load-more');
				if (!view_more.length && !block.length){
					return false;
				}

				// show all before calulator
				menu.find('.megamenu > .nav-item:not(.view-all)').show();
				menu.removeClass('active-load-all');
				let height_menu = (block.hasClass('block-static-menu-load-more')) ? menu.outerHeight() : menu.find('.box-content').outerHeight();
				let height_li = menu.find('.nav-item').first().outerHeight();
				let height_block = 0;
				let height_view_more = view_more.outerHeight();
				let height_menu_title = menu.find('.vertical-menu-button').outerHeight();
				let total_li = menu.find('.megamenu > .nav-item:not(.view-all)').length;

				// get height of block highest
				if (block.length){
					let height_temp = 0;

					block.each(function() {
						if ($(this).outerHeight() > height_temp){
							height_temp = $(this).outerHeight();
						}
					});
					height_block = height_temp;
				}else{
					return false;
				}

				let compare_height = height_menu - height_block;
				if (compare_height > 0){
					let height_min = height_menu_title + height_view_more;
					let li_max = Math.floor((height_block - height_min)/height_li);
					let number_li = total_li - li_max;

					for (let i = li_max; i <= total_li; i++) {
						menu.find('.megamenu > .nav-item:not(.view-all)').eq(i).hide();
					}
					
					view_more.show();
					menu.addClass('active-load-all');
				}
			// });
		}else{
			menu.find('.navbar-nav > li:not(.view-all)').show();
		}
	}
});

// click show video
$(document).ready(function(){
	$('.deo-video:not(.popup-video) .image-video').click(function(e){ 
		e.preventDefault();
		let group_video = $(this).closest('.deo-video');
		let video_type = 'normal';
		let iframe, symbol;
		if (group_video.hasClass('youtube-video')){
			video_type = 'youtube';
			iframe = group_video.find(".content-video iframe");
			symbol = iframe.attr('src').indexOf("?") > -1 ? "&" : "?";
		}
		if(group_video.hasClass('playing')){
			group_video.removeClass('playing');
		}else{
			group_video.addClass('playing');
			if (video_type == 'youtube'){
				iframe.attr('src',iframe.attr('src') + symbol + "autoplay=1");
			}
		}

	});

	$('.deo-video.popup-video .image-video').click(function(e){ 
		e.preventDefault();
		let image_video = $(this);
		setTimeout( function() {
			$.fancybox({
				openMethod  : 'fadescaleIn',
				closeMethod : 'fadescaleOut',
				content : image_video.closest('.deo-video').find(".content-video"),
				helpers : {
					overlay: {
						locked: false
					}
				},
				afterShow: function() {
					image_video.addClass('playing');
				},
				afterClose: function() {
					image_video.removeClass('playing');
				}
			});
		}, 1000);
	});
});

// menu toogle
$(document).ready(function(){
	$('.showmenu').click(function(e) {
		e.stopPropagation();
		let btn_toogle = $(this);
		let parent = btn_toogle.closest('.menu-toogle');
		let menu = parent.find('.group-nav');
		if(menu.hasClass('active-menu')){
			menu.removeClass('active-menu');
			parent.removeClass('active-overlay');
		}
		else{
			menu.addClass('active-menu');
			parent.addClass('active-overlay');
		}
	}); 
	$('.closemenu').click(function(e) {
		e.stopPropagation();
		let close_btn = $(this);
		let menu = close_btn.closest('.group-nav');
		if(menu.hasClass('active-menu')){
			menu.removeClass('active-menu');
		}
	});
	$('.bg-overlay').click(function(e) {
		e.stopPropagation();
		let overlay = $(this);
		let parent = overlay.closest('.menu-toogle');
		let menu = parent.find('.group-nav');
		if(menu.hasClass('active-menu')){
			menu.removeClass('active-menu');
			parent.removeClass('active-overlay');
		}
	});
	$(document).keyup(function(e) {
		let menu = $('.group-nav');
		let overlay = $('.bg-overlay');
		let parent = overlay.closest('.menu-toogle');
		if (e.keyCode == 27) { 
			if(menu.hasClass('active-menu')){
				menu.removeClass('active-menu');
				parent.removeClass('active-overlay');
			}
		}
	});
});

// menu sidebar
$(document).ready(function(){
	$('.header-label .icon-open-menu').click(function(){
		if(!$('body').hasClass('open-menu')){
			$('body').addClass('open-menu');
		}else{
			$('body').removeClass('open-menu');
		}
	});
	$('.menu-sidebar .close-menu,.bg-menu-overlay').click(function(){
		if($('body').hasClass('open-menu')){
			$('body').removeClass('open-menu');
		}
	});
	$(document).keyup(function(e) {
		if (e.keyCode == 27) { 
			if($('body').hasClass('open-menu')){
				$('body').removeClass('open-menu');
			}
		}
	});
});

// menu-sidebar-fixed
$(document).ready(function(){
	let menu = $('body#index .menu-sidebar-fixed');
	let body = menu.closest('body');
	let icon = $('body#index .menu-sidebar .showmenu-sidebar');

	if(menu.hasClass('menu-right')){
		body.addClass('layout-sidebar-right');
	}

	autoHiddenMenu();
	$(window).resize(function(){
		autoHiddenMenu();   
	});

	// click show menu
	$('.showmenu-sidebar').click(function(e) {
		e.stopPropagation();
		if(menu.hasClass('active-menu')){
			menu.removeClass('active-menu');
		}else{
			menu.addClass('active-menu');
		}
	}); 

	$(document).keyup(function(e) {
		let windowsize = $(window).width();
		if (menu.length) {
			if ((windowsize < 1399) && (windowsize >= 992)){
				setTimeout(reloadOwl, 500);
				if (e.keyCode == 27) { 
					if(menu.hasClass('active-menu')){
						menu.removeClass('active-menu');
					}
				}
			}
		}
	});

	function autoHiddenMenu(){
		let windowsize = $(window).width();
		if (menu.length) {
			setTimeout(reloadOwl, 500);
			if ((windowsize < 1399) && (windowsize >= 992)){
				body.addClass('active-sidebar-absolute');
			}else{
				body.removeClass('active-sidebar-absolute');
			}
		}
	} 
	function reloadOwl(){
		$('.owl-carousel').each(function(){
			// $(this).data().owlCarousel.reload();
			$(this).trigger('refresh.owl.carousel');
		});
	}
});


//sticky-menu
$(document).ready(function(){
	// let lastScrollTop = 0;
	let deo_sticky_offset_top;

	if ($('.header-top').length){
		deo_sticky_offset_top = $('.header-top').offset().top;
	}else if ($('.deo-header-mobile').length){
		deo_sticky_offset_top = $('.deo-header-mobile').offset().top;
	}

	function sticky_menu() {
		let scrollTop = $(window).scrollTop();
		// let scrollUp = true;

		// if (scrollTop > lastScrollTop){
		// 	scrollUp = false;
		// } else {
		// 	scrollUp = true;
		// }

		if ((!deo_header_mobile && $(window).width() <= 991) || !$("body").hasClass("keep-header")){
			$('#header').removeClass('sticky-menu-active');
			return;
		}

		if (scrollTop > deo_sticky_offset_top) {
			$('#header').addClass('sticky-menu-active');
		} else {
			$('#header').removeClass('sticky-menu-active');
		}

		// if (scrollTop > deo_sticky_offset_top && scrollUp) {
		// 	$('#header').addClass('sticky-menu-active');
		// } else {
		// 	$('#header').removeClass('sticky-menu-active');
		// }

		// lastScrollTop = scrollTop;
	}

	sticky_menu();
	$(window).scroll(function(){
		sticky_menu();
	});
	$(window).resize(function() {
		sticky_menu();
	});
});


// lazyloaded
$(document).ready(function(){
	if ('IntersectionObserver' in window && deo_lazy_intersection_observer && deo_lazyload){
		function callback(img){
			if ($(img).hasClass('lazyload-bg')){
				let src = $(img).attr('data-bgset');
				$(img).css('background-image', 'url('+src+')');
				$(img).removeClass('lazyload').addClass('lazyloaded');
				$(img).find('.lazyload-background').first().remove();

			}else{
				let src = $(img).attr('data-src');
				$(img).attr('src', src);
				$(img).removeAttr('data-src');
				$(img).css("display", "");
				$(img).removeClass('lazyload').addClass('lazyloaded');
				$(img).prev('.lazyload-wrapper').remove();
			}
		}

		let observer = new IntersectionObserver((entries)  => {
			entries.forEach(entry => {
				if (entry.isIntersecting){
					callback(entry.target);
				}
			});
		});

		let lazyImgs = $('.lazyload');
		lazyImgs.each((key, img) => {
			observer.observe(img);
		});
	}

	// let docElem = document.documentElement;
	window.lazySizesConfig = window.lazySizesConfig || {};
	window.lazySizesConfig.loadMode = 1;
	window.lazySizesConfig.loadHidden = false;

	document.addEventListener('lazyloaded', function(element){
		if($(element.target).hasClass('lazyload-bg')){
			$(element.target).find('.lazyload-background').first().remove();
		}else{
			$(element.target).css("display", "");
			$(element.target).prev('.lazyload-wrapper').remove();
		}
	});
});
$(document).on('lazybeforesizes', function(e){
	//use width of parent node instead of the image width itself
	e.detail.width = $(e.target).closest(':not(picture)').innerWidth() || e.detail.width;
});

// click dot hotspot
$(document).ready(function(){
	$('.hotspot .hotspot-title,.hotspot .close,.hotspot .overlay-popup').on('click', function(e) {
		e.preventDefault();
		let hotspot = $(this).closest('.hotspot');
		if(hotspot.hasClass('open')){
			hotspot.removeClass('open');
		}else{
			hotspot.addClass('open');
		}
	});
	$(document).keyup(function(e) {
		if (e.keyCode == 27) { 
			$('.hotspot').removeClass('open');
		}
	});
});

function hexToRgba(hex,a = 1) {
	let r,g,b;
	if ( hex.charAt(0) == '#' ) {
		hex = hex.substr(1);
	}
	if ( hex.length == 3 ) {
		hex = hex.substr(0,1) + hex.substr(0,1) + hex.substr(1,2) + hex.substr(1,2) + hex.substr(2,3) + hex.substr(2,3);
	}
	r = hex.charAt(0) + '' + hex.charAt(1);
	g = hex.charAt(2) + '' + hex.charAt(3);
	b = hex.charAt(4) + '' + hex.charAt(5);
	r = parseInt( r,16 );
	g = parseInt( g,16 );
	b = parseInt( b ,16);
	return 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')';
}


// sticky block
$(document).ready(function(){
	var stickySidebar = new StickySidebar('.deo-block-fixed', {
		// topSpacing: 0,
		// bottomSpacing: 0,
		// containerSelector: '.row',
		minWidth: 768,
		// innerWrapperSelector: '.sidebar__inner'
	});
	// $('.deo-block-fixed').stick_in_parent();	
});


$(window).load(function(){
	$(window).resize(function() {
		// fix zoom, only work at product page
		if (prestashop.page.page_name == 'product')
			initElevateZoom();
	});

	prestashop.on('updatedProduct', function(e) {
		let temp = $('<div>').append($(e.product_details));
		let data_product = temp.children('#product-details').data('product');

		if ($('.more-infor-product').length){
			let reference = '';
			if ($('#product-details .product-reference').length){
				reference = $('#product-details .product-reference span').html();
			}else if (typeof data_product.reference != 'undefined' && data_product.reference != ''){
				reference = data_product.reference;
			}

			if (reference != '') {
				$('.more-infor-product.reference span').html(reference);
			}
			
			if (data_product.show_quantities){
				$('.more-infor-product.product-quantities').show();
				$('.more-infor-product.product-quantities span').html(data_product.quantity+ ' ' +data_product.quantity_label ).data('stock',data_product.quantity).data('allow-oosp',data_product.allow_oosp);
			}else{
				$('.more-infor-product.product-quantities').hide();
			}
		}

		if ($('.countdown-product-page').length){
			$('.countdown-product-page .deo-countdown').empty();
			$('.countdown-product-page .deo-countdown').data('time-from', data_product.specific_prices.from).data('time-to', data_product.specific_prices.to).removeClass('processed-countdown');
			DeoTemplate.initCountdown();
		}
	});


	// Back to top
	$("#back-top").hide();
	$(window).scroll(function () {
		if ($(this).scrollTop() > 100) {
			$('#back-top').fadeIn();
		} else {
			$('#back-top').fadeOut();
		}
	});

	// scroll body to 0px on click back to top
	$('.deo-back-top a').click(function () {
		$('body,html').animate({
			scrollTop: 0
		}, 800);
		return false;
	});
	

	//check page product only
	if (prestashop.page.page_name == 'product'){
		innitSlickandZoom();
	}
	
	// update for order page - tab adress, when change adress, block adress change class selected
	$('.address-item .radio-block').click(function(){
		if (!$(this).parents('.address-item').hasClass('selected')){
			$('.address-item.selected').removeClass('selected');
			$(this).parents('.address-item').addClass('selected');
		}
	})
	
	// action loading quickview
	actionQuickViewLoading();
	
	prestashop.on('updateProductList', function() {
		actionQuickViewLoading();
		DeoTemplate.initTooltip();
		DeoTemplate.processAjaxProduct();
		DeoTemplate.initShadownHover();
	});	

	prestashop.on('updatedProduct', function () {
		let images_cover = $('.images-for-detail .product-images .image-container img');
		let thumb_container = $('.images-for-detail .thumb-images .thumb-container');
		let thumb = $('.images-for-detail .thumb-images .thumb-container .deo-js-thumb');

		if ($('.deo-quickview.modal .thumb-images').length){
			// run slick slider for product thumb - quickview
			initSlickProductQuickView();
			
			return false;
		}
		if (prestashop.page.page_name == 'product'){
			$('.tooltip').remove();

			let lazyload = (deo_lazyload && $('#content').data('lazyload')) ? true : false;
			let size = $('#content').data('size');
			if ($("#page").hasClass('detail-gallery')){
				thumb_container.removeClass('col-thumbnail').addClass($('#content').data('col'));
				thumb.removeClass('deo-lazyload-img').addClass('lazyload');

				thumb.each(function(){
					let src = $(this).attr('src');
					src = src.replace("home_default", size);

					if (lazyload){
						$(this).before('<span class="lazyload-wrapper" style="padding-bottom: '+deo_rate_images[size]+';"><span class="lazyload-icon"></span></span>');
						$(this).attr('data-src', src).attr('src', 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==');
					}else{
						$(this).attr('src', src);
					}
				});
			}else if ($("#page").hasClass('detail-thumbnail')){
				thumb_container.removeClass('col-thumbnail').addClass($('#content').data('col-loading'));
				thumb.each(function(){
					let src = $(this).attr('src');
					src = src.replace("home_default", size);

					if (lazyload){
						$(this).before('<span class="lazyload-wrapper" style="padding-bottom: '+deo_rate_images[size]+';"><span class="lazyload-icon"></span></span>');
						$(this).addClass('deo-lazyload-img').attr('data-lazy', src).attr('src', 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==');
					}else{
						$(this).attr('src', src);
					}
				});


				images_cover.each(function(){
					let src = $(this).attr('src');
					if (lazyload){
						$(this).before('<span class="lazyload-wrapper" style="padding-bottom: '+deo_rate_images['large_default']+';"><span class="lazyload-icon"></span></span>');
						$(this).addClass('deo-lazyload-img').attr('data-lazy', src).attr('src', 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==');
					}else{
						$(this).attr('src', src);
					}
				});
			}
		
			innitSlickandZoom();			
		}

		DeoTemplate.initTooltip();
	});

	
	$('body').on("click", '.product-add-to-cart .add-to-cart', (function(e) {
	// $('body').on('click', '.product-add-to-cart .add-to-cart', function(e){
		$(this).addClass('loading');
	}));
	prestashop.on('updateCart', function() {
		$('.product-add-to-cart .add-to-cart').removeClass('loading');
	});

	$(document).on('click', '.deo_grid', function(e){
		e.preventDefault();

		$('#js-product-list .product_list').removeClass('list');
		$('#js-product-list .product_list').addClass('grid');

		$(this).parent().find('.deo_list').removeClass('selected');
		$(this).addClass('selected');

		// let configName = deo_cookie_theme +'_grid_list';
		// $.cookie(configName, 'grid', {expires: 1, path: '/'});
	});

	$(document).on('click', '.deo_list', function(e){
		e.preventDefault();

		$('#js-product-list .product_list').removeClass('grid');
		$('#js-product-list .product_list').addClass('list');

		$(this).parent().find('.deo_grid').removeClass('selected');
		$(this).addClass('selected');

		// let configName = deo_cookie_theme +'_grid_list';
		// $.cookie(configName, 'list', {expires: 1, path: '/'});
	});
	
	// display modal by config
	if (typeof $("#content").data('modal') != 'undefined'){
		if (!$("#content").data('modal')){
			$('div[data-target="#product-modal"]').hide();
		}
	}

	function innitSlickandZoom(){
		// remove fancybox and init
		$(document).unbind('click.fb-start');
		$('body').on("click", '.images-for-detail .product-images .slick-slide,.images-for-detail .thumb-container .thumbnail-image', (function(e) {
			e.preventDefault();
		}));

		if ($("#page").hasClass('detail-thumbnail')){
			// setup slide for product thumb (main)
			$('.thumb-images').imagesLoaded(function(){ 
				if (typeof check_loaded_main_product != 'undefined'){
					clearInterval(check_loaded_main_product);
				}
				check_loaded_main_product = setInterval(function(){
					clearInterval(check_loaded_main_product);
					let thumb = $("#content").data("thumb");
					let breakpoints = $("#content").data("breakpoints");
					if (typeof thumb != 'undefined'){
						initSlickProductThumb(thumb, breakpoints);
					}
				}, 300);
			});
		}else if ($("#page").hasClass('detail-gallery')){
			if ($(window).width() <= 991){
				var fancyGallery = $('.images-for-detail .thumb-container .thumbnail-image');
				fancyGallery.fancybox({
					type: "image",
					helpers : {
						overlay: {
							locked: false
						}
					},
				});
			}

		}

		initElevateZoom();

		// setup slide for product modal
		initSlickProductModal();
	}

	function initElevateZoom(){
		// remove elevateZoom on mobile
		if ($(window).width() <= 991 || $("#content").data("zoom") == 'none'){		
			if ($('#page').hasClass('detail-gallery')){
				if ($('.images-for-detail .thumb-images img.deo-js-thumb').data('elevateZoom')){
					let ezApi = $('.images-for-detail .thumb-images img.deo-js-thumb').data('elevateZoom');
					ezApi.changeState('disable');			
					$('.images-for-detail .thumb-images img.deo-js-thumb').unbind("touchmove");
				}
			}else if ($('#page').hasClass('detail-thumbnail')){
				if ($(".images-for-detail .product-images .slick-current img").data('elevateZoom')){
					let ezApi = $(".images-for-detail .product-images .slick-current img").data('elevateZoom');
					ezApi.changeState('disable');			
					$(".images-for-detail .product-images .slick-current img").unbind("touchmove");
				}
			}

			return false;
		}

		let zoom_config = {}; 

		//check if that is gallery, zoom all thumb
		// fix zoom, create config
		let zt = $("#content").data('zoom');
		let zoom_cursor;
		let zoom_type;
		let scroll_zoom = false;
		let	lens_FadeIn = 200;
		let	lens_FadeOut = 200;
		let	zoomWindow_FadeIn = 200;
		let	zoomWindow_FadeOut = 200;
		let zoom_tint = false;
		let zoomWindow_Width = 400;
		let zoomWindow_Height = 400;
		let zoomWindow_Position = 1;
		
		if (zt == 'in'){
			zoom_cursor = 'crosshair';
			zoom_type = 'inner';
			lens_FadeIn = false;
			lens_FadeOut = false;		
		}else{
			zoom_cursor = 'default';
			zoom_type = 'window';
			zoom_tint = true;
			zoomWindow_Width = $("#content").data('zoomWidth');
			zoomWindow_Height = $("#content").data('zoomwindowheight');
			
			if ($("#content").data('position') == 'right'){			
				// update position of zoom window with ar language
				if (prestashop.language.is_rtl == 1){
					zoomWindow_Position = 11;
				}else{
					zoomWindow_Position = 1;
				}
			}
			if ($("#content").data('position') == 'left'){
				// update position of zoom window with ar language
				if (prestashop.language.is_rtl == 1){
					zoomWindow_Position = 1;
				}else{
					zoomWindow_Position = 11;
				}
			}
			if ($("#content").data('position') == 'top'){
				zoomWindow_Position = 13;
			}
			if ($("#content").data('position') == 'bottom'){
				zoomWindow_Position = 7;
			}
			
			if (zt == 'out_scrooll'){
				// scroll to zoom does not work on IE
				let ua = window.navigator.userAgent;
				let old_ie = ua.indexOf('MSIE ');
				let new_ie = ua.indexOf('Trident/');
				if (old_ie > 0 || new_ie > 0) {
					// If Internet Explorer, return version number
					scroll_zoom = false;
				}else{
					// If another browser, return 0
					scroll_zoom = true;
				}

				zoom_config.scrollZoom = scroll_zoom;
			}
		};
		
		if ($('#page').hasClass('detail-gallery')){
			lens_FadeIn = false;
			lens_FadeOut = false;
			zoomWindow_FadeIn = false;
			zoomWindow_FadeOut = false;
		}
		
		zoom_config.responsive = true;
		zoom_config.cursor = zoom_cursor;
		zoom_config.gallery = 'product-thumbs';
		zoom_config.zoomType = zoom_type;

		// in
		zoom_config.lensFadeIn = lens_FadeIn;
		zoom_config.lensFadeOut = lens_FadeOut;
		zoom_config.zoomWindowFadeIn = zoomWindow_FadeIn;
		zoom_config.zoomWindowFadeOut = zoomWindow_FadeOut;

		// out
		zoom_config.zoomLevel = 1;
		zoom_config.scrollZoomIncrement = 0.1;
		zoom_config.zoomWindowWidth = zoomWindow_Width;
		zoom_config.zoomWindowHeight = zoomWindow_Height;
		zoom_config.borderColour = '#888';
		zoom_config.borderSize = 2;
		zoom_config.zoomWindowOffetx = 0;
		zoom_config.zoomWindowOffety = 0;
		zoom_config.zoomWindowPosition = zoomWindow_Position;
		zoom_config.tint = zoom_tint;


		
		if ($('#page').hasClass('detail-gallery')){
			if (deo_lazyload && $('#content').data('lazyload')){
				$('.images-for-detail .thumb-images img.deo-js-thumb').on('lazyloaded', function(element){
					if (typeof $.fn.elevateZoom != 'undefined'){
						$(".zoomContainer").remove();
						$('.images-for-detail .thumb-images img.deo-js-thumb').each(function(){
							$(this).elevateZoom(zoom_config);
						});
					}
				});
			}else{
				if (typeof $.fn.elevateZoom != 'undefined'){
					$(".zoomContainer").remove();
					$('.images-for-detail .thumb-images img.deo-js-thumb').each(function(){
						$(this).elevateZoom(zoom_config);
					});
				}
			}
		}else if ($('#page').hasClass('detail-thumbnail')){
			$(".zoomContainer").remove();
			if (deo_lazyload && $('#content').data('lazyload')){
				$(".images-for-detail .product-images").imagesLoaded( function(){ 
					$(".images-for-detail .product-images").on('lazyLoaded', function(event, slick, direction, imageSource){
						if (typeof $.fn.elevateZoom != 'undefined'){
							$(".images-for-detail .product-images .slick-current img").elevateZoom(zoom_config);
							$(".images-for-detail .product-images").on('afterChange', function(event, slick, currentSlide){
								$(".zoomContainer").remove();
								let current = $(slick.$slides.get(currentSlide));
								current.find('img.deo-js-qv-product-cover').elevateZoom(zoom_config);
							});
						}
					});
				});
			}else{
				$(".images-for-detail .product-images").imagesLoaded( function(){ 
					if (typeof $.fn.elevateZoom != 'undefined'){
						$(".images-for-detail .product-images").on('init', function(event, slick){
							$(this).find(".slick-current img").elevateZoom(zoom_config);
						});
						$(".images-for-detail .product-images").on('afterChange', function(event, slick, currentSlide){
							$(".zoomContainer").remove();
							let current = $(slick.$slides.get(currentSlide));
							current.find('img.deo-js-qv-product-cover').elevateZoom(zoom_config);
						});
					}
				});
			}
		}
	}

	function initSlickProductThumb(thumb, breakpoints){
		let slider_cover = $('.images-for-detail .product-images');
		let slider_thumb = $('.images-for-detail .thumb-images');

		// return true;
		let vertical = true;
		let numberimage = 1;
		let verticalSwiping = true;
		// update for rtl
		let slick_rtl = false;

		let responsive = false;
		if ((typeof breakpoints !== 'undefined') && breakpoints) {
			responsive = [];
			if (breakpoints.length > 2){
				numberimage = breakpoints[0][1];
				for (let i = 1; i < breakpoints.length; i++) {
					let settings = {
						slidesToShow : breakpoints[i][1]
					}
					let breakpoint =  {
						breakpoint : breakpoints[i][0],
						settings : settings
					};
					responsive.push(breakpoint);
				}
			}else if (breakpoints.length == 1){
				numberimage = breakpoints[0][1];
			}
		}

		if (thumb == "bottom"){
			vertical = false;
			verticalSwiping = false;
		} 

		if (thumb == 'none'){
			vertical = false;
			verticalSwiping = false;
		}
		
		// update for rtl
		if (!vertical && prestashop.language.is_rtl == 1){
			slick_rtl = true;
		}

		configurations_thumb.vertical = vertical;
		configurations_thumb.verticalSwiping = verticalSwiping;
		configurations_thumb.slidesToShow = numberimage;
		configurations_thumb.slidesToScroll = 1;
		configurations_thumb.rtl = slick_rtl;
		configurations_thumb.responsive = responsive;
		configurations_thumb.vertical = vertical;

		configurations_cover.rtl = slick_rtl;
		configurations_cover.slidesToShow = 1;
		configurations_cover.slidesToScroll = 1;

		if (thumb == "none"){
			configurations_cover.asNavFor = null;
			DeoTemplate.callInitSlickCarousel(slider_cover, configurations_cover);
		}else{
			configurations_thumb.asNavFor = ".images-for-detail .product-images";
			configurations_cover.asNavFor = ".images-for-detail .thumb-images";
			configurations_thumb.initialSlide = $('.images-for-detail').data('initialslide');
			configurations_cover.initialSlide = $('.images-for-detail').data('initialslide');
			DeoTemplate.callInitSlickCarousel(slider_cover, configurations_cover);
			DeoTemplate.callInitSlickCarousel(slider_thumb, configurations_thumb);

			slider_thumb.on('afterChange', function(event, slick, currentSlide){
				let current = $(slick.$slides.get(currentSlide));

				if (!current.find('.thumbnail-image').hasClass('selected')){
					slider_thumb.find('.thumbnail-image').removeClass('selected');
					current.find('.thumbnail-image').addClass('selected');
				}
			});
		}

		// open fancybox with slick active
		if ($(window).width() <= 991){
			var fancyGallery = $('.images-for-detail .product-images .image-container');
			fancyGallery.fancybox({
				type: "image",
				helpers : {
					overlay: {
						locked: false
					}
				},
			});

			$('body').on("click", '.product-cover .layer', (function(e) {
				let slide = $('.images-for-detail .product-images').get(0).slick;
				fancyGallery.eq(slide.currentSlide).trigger('click'); 
			}));
		}
	}

	function findPosition(slides){
		let position;
		for (let i = 0; i < slides.length; i++) {
			if ($(slides[i]).hasClass('active')) {
				position = $(slides[i]).data('slick-index');
				return position;
			}
		}
	}

	// loading quickview
	function actionQuickViewLoading(){
		$('.deo-quick-view').click(function(){
			if (!$(this).hasClass('loading')){
				let btn_quickview = $(this);
				btn_quickview.addClass('loading');
			
				if (typeof check_active_quickview != 'undefined'){
					clearInterval(check_active_quickview);
				}

				check_active_quickview = setInterval(function(){
					if ($('.deo-quickview.modal').length){
						$('.deo-quickview.modal').on('hide.bs.modal', function (e){
							btn_quickview.removeClass('loading');
						});
						clearInterval(check_active_quickview);
							
						// run slick for product thumb - quickview
						$('.deo-quickview.modal').on('shown.bs.modal', function(e){
							initSlickProductQuickView();
							DeoTemplate.initCountdown();
						});
					}
				}, 300);
			}
		});
	}

	function correctHtmlProductQuickView(){
		let slider_cover = $('.deo-quickview.modal .product-images');
		let slide_quickview = $('.deo-quickview.modal .thumb-images');
	}

	// build slick slider for quickview
	function initSlickProductQuickView(){
		$('.deo-quickview.modal .images-container.images-for-detail').addClass('images-for-quickview').removeClass('images-for-detail');
		$('body').on("click", '.deo-quickview .thumb-images .thumbnail-image,.deo-quickview .product-images .image-container', (function(e) {
			e.preventDefault();
		}));
	
		let slider_cover = $('.deo-quickview.modal .product-images');
		let slide_quickview = $('.deo-quickview.modal .thumb-images');
		let configurations_cover_quickview = Object.assign({}, configurations_cover);
		

		configurations_quickview.activeMode = true;
		configurations_quickview.asNavFor = ".deo-quickview.modal .product-images";
		configurations_cover_quickview.asNavFor = ".deo-quickview.modal .thumb-images";
		configurations_quickview.initialSlide = $('.images-for-quickview').data('initialslide');
		configurations_cover_quickview.initialSlide = $('.images-for-quickview').data('initialslide');

		let deo_col_thumbnail = [];
		let responsive = configurations_quickview.responsive;
		let class_loading = (configurations_quickview.vertical) ? 'loading-vertical ' : '';

		deo_col_thumbnail.push("loading-xxl-"+configurations_quickview.slidesToShow);
		if (Object.keys(responsive).length){
			$.each(responsive, function(index, value) {
				if (value.breakpoint <= 480) {
					deo_col_thumbnail.push("loading-sp-"+value.settings.slidesToShow);
				}else if (value.breakpoint <= 576) {
					deo_col_thumbnail.push("loading-xs-"+value.settings.slidesToShow);
				}else if (value.breakpoint <= 768) {
					deo_col_thumbnail.push("loading-sm-"+value.settings.slidesToShow);
				}else if (value.breakpoint <= 992) {
					deo_col_thumbnail.push("loading-md-"+value.settings.slidesToShow);
				}else if (value.breakpoint <= 1200) {
					deo_col_thumbnail.push("loading-lg-"+value.settings.slidesToShow);
				}else if (value.breakpoint <= 1500) {
					deo_col_thumbnail.push("loading-xl-"+value.settings.slidesToShow);
				}
			});
		}

		class_loading += deo_col_thumbnail.join(" ");
		slide_quickview.find('.col-thumbnail').addClass(class_loading).removeClass('col-thumbnail');

		DeoTemplate.callInitSlickCarousel(slider_cover, configurations_cover);
		DeoTemplate.callInitSlickCarousel(slide_quickview, configurations_quickview);


		slide_quickview.on('afterChange', function(event, slick, currentSlide){
			let current = $(slick.$slides.get(currentSlide));

			if (!current.find('.thumbnail-image').hasClass('selected')){
				slide_quickview.find('.thumbnail-image').removeClass('selected');
				current.find('.thumbnail-image').addClass('selected');
			}
		});
	}

	// build slick slider for modal - product page
	function initSlickProductModal(){
		var index_image_cover_detail_page = $('.images-for-detail').data('initialslide');
		let slider_modal = $('#product-modal .product-modal-cover');
		
		$('.images-for-detail .product-images').on('afterChange', function(event, slick, currentSlide){
			let slide = slick.$slides.get(currentSlide);
			let total_slide = $('#product-modal .thumbnails-modal img').length;
			let index = $(slide).data('slick-index');
			if ((index + 1) > total_slide){
				index =  index - total_slide;
			}

			index_image_cover_detail_page = index;
			$('#product-modal .thumbnails-modal img').removeClass('selected');
			$('#product-modal .thumbnails-modal img[data-index="'+ (index_image_cover_detail_page + 1) +'"]').addClass('selected');
		});

		$('#product-modal').on('shown.bs.modal', function(e){
			let configurations_cover_modal_product_page = Object.assign({}, configurations_cover);
			configurations_cover_modal_product_page.initialSlide = index_image_cover_detail_page;
			configurations_cover_modal_product_page.asNavFor = ($("#content").data("thumb") == 'none') ? ".images-for-detail .product-images" : ".images-for-detail .product-images, .images-for-detail .thumb-images";
			if (!slider_modal.hasClass('slick-initialized')) {
				DeoTemplate.callInitSlickCarousel(slider_modal, configurations_cover_modal_product_page);
			}

			$('.product-cover .layer').click(function(e) {
				let slider = $('.images-for-detail .product-images');

				if (slider.hasClass('slick-initialized')) {
					index_image_cover_detail_page = slider.find('.slick-active.first.last').data('slick-index');
			
					if (index_image_cover_detail_page != slider_modal.find('.slick-active.first.last').data('slick-index')){
						slider_modal.slick('slickGoTo', index_image_cover_detail_page);
					}
				}
			});

			slider_modal.on('afterChange', function(event, slick, currentSlide){
				let slider = slick.$slides.get(currentSlide);
				let total_slide = $('#product-modal .thumbnails-modal img').length;
				let index = $(slider).data('slick-index');
				if ((index + 1) > total_slide){
					index =  index - total_slide;
				}

				$('#product-modal .thumbnails-modal img').removeClass('selected');
				$('#product-modal .thumbnails-modal img[data-index="'+ (index + 1) +'"]').addClass('selected');
			});

			$('#product-modal .thumbnails-modal img').click(function(e) {
				e.preventDefault();
				if(!$(this).hasClass('selected')){
					$('#product-modal .thumbnails-modal img').removeClass('selected');
					$(this).addClass('selected');
					if (slider_modal.hasClass('slick-initialized')) {
						slider_modal.slick('slickGoTo', $(this).data('index') - 1);
					}
				}
			});
		});
	}
});

// fix default prestashop remove css inline of label
$(document).ready(function(){
	$('.product-flag').removeAttr('style');	
	prestashop.on('updateProductList', function() {
		$('.product-flag').removeAttr('style');
	});
});