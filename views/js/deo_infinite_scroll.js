/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

(function ($) {
	$.DeoInfiniteScroll = function (options) {
		let object = this;
		let config = $.extend({}, {
			PRODUCT_LIST_CSS_SELECTOR : "#js-product-list .products",
			ITEM_CSS_SELECTOR : ".ajax_block_product",
			PAGINATION_CSS_SELECTOR : ".pagination",
			DEFAULT_PAGE_PARAMETER : "page",
			HIDE_MESSAGE_END_PAGE : 0,
			TEXT_MESSAGE_END_PAGE : "We have reached the bottom end of this page",
			TEXT_BACK_TO_TOP : "Go back to top",
			TEXT_ERROR : "Something wrong happened and we can not display further products",
			TEXT_LOAD_MORE : "Load more products",
			HAS_FILTER_MODULE : 1,
			DISPLAY_LOAD_MORE_PRODUCT : 0,
			NUMBER_PAGE_SHOW_LOAD_MORE_PRODUCT : 2,
			FREQUENCY_SHOW_LOAD_MORE_PRODUCT : 0,
			JAVASCRIPT_DEBUG_MODE : 0,
			CURRENT_PAGE : 1,
			PS_INSTANT_SEARCH : 0,
			//default value, used in case you want the "stop bottom" feature
			acceptedToLoadMoreProductsToBottom : 0,
		}, options);

		$.each(config, function (key, value) {
			object[key] = config[key];
		});

		// avoid when it's loaded and executed in ajax in 1.5 while already in the page
		if (this.is_initialised){
			return;
		}

		object.is_running = true;
		object.waiting_for_next_page = false;
		object.waiting_for_previous_page = false;
		object.current_page_bottom = 1;
		object.current_page_top = 1;
		object.page_cache = {};
		object.override_page_to_call = false;
		object.override_friendly_url = false;
		//save the pages already loaded to reload them?

		// quick tip to avoid multiple test in the javascript
		if (object.FREQUENCY_SHOW_LOAD_MORE_PRODUCT === 0) {
			object.FREQUENCY_SHOW_LOAD_MORE_PRODUCT = 999999;
		}

		// left: 37, up: 38, right: 39, down: 40,
		// spacebar: 32, pageup: 33, pagedown: 34, end: 35, home: 36
		object.antiScroll = {};
		object.antiScroll.keys = {37: 1, 38: 1, 39: 1, 40: 1};
		object.antiScroll.active = false;

		object.cache_scrolltop = 0;

		// EVENTS DOCUMENT READY
		$(function() {
			if (typeof deoInfiniteScroll == "undefined"){
				return false;
			}
			if (object.PS_INSTANT_SEARCH) {
				$(".search_query").change(function(){
					if($(this).val().length > 4){
						object.processReset();
						object.is_running = false;
					} else {
						object.is_running = true;
					}
				});
			}


			object.hidePagination();
			// we force to put it at the top, to avoid when we go back to trigger scrolls by mistake
			// $(window).scrollTop(0);
			object.updateCacheScrolltop();

			// we load prepage 1 for checks such as "check that the products are not on page 1"
			object.current_page_top = object.current_page_bottom = object.CURRENT_PAGE;
			// when we get to page 7, our "load next" validation needs to be consistent
			object.acceptedToLoadMoreProductsToBottom = Math.ceil( (object.CURRENT_PAGE - object.NUMBER_PAGE_SHOW_LOAD_MORE_PRODUCT) / object.FREQUENCY_SHOW_LOAD_MORE_PRODUCT);
			
			object.getProductsPerPage({'page': 1});
			if (object.CURRENT_PAGE > 1) {
				object.getProductsPerPage({'page': object.CURRENT_PAGE});

				// we prefetch the one before direct
				object.waiting_for_previous_page = true;
				object.displayLoadMoreLabelToTop({'page': object.current_page_top-1});
				// moreover we block the scroll and we put it back at the beginning of the current page
				$(window).load(function(){
					console.log('object.initializeInfiniteScrollPlugin : Window is loaded. Now starting the forced scroll to first products');

					let $firstProduct = $(object.getProductsSelector()).first();
					//if we have a product that we have visited which is present on the page, then we go to it, and not to "the first"
					let product_visited = object.loadProductVisitedInfos();
					if (product_visited.page == object.CURRENT_PAGE && document.location.href == product_visited.current_url) {
						$(object.getProductsSelector()).each(function(){	
							if ($(this).find('a').first().attr('href') == product_visited.product_link) {
								$firstProduct = $(this);
							}
						});
					}

					if ($firstProduct.length == 0) {
						object.log('object.addProductsToPage() : could not find "$firstProduct', {warn:1});
						return;
					}

					object.disableScroll();
					let interval_force_scroll = setInterval(function(){
						//let target_top = tmp_scrolltop + $(object.PRODUCT_LIST_CSS_SELECTOR).height() - height_before;

						let target_top = $firstProduct.offset().top;
						target_top -= $firstProduct.height() / 2;
						// fix when products are too high and we wanna scroll below 0
						target_top = Math.round(Math.max(0, target_top - $firstProduct.height() / 2));
						$('body, html').stop(true).animate({scrollTop: target_top}, 500, function(){
							// add error tolerance so that floating pixels will work
							let error_margin = 5;
							if (Math.round($(window).scrollTop()) - error_margin <= target_top && Math.round($(window).scrollTop()) + error_margin >= target_top) {
								object.log('Deo Infinite Scroll ON READY : reached destination');
								clearInterval(interval_force_scroll);
								object.enableScroll();
								return;
							}
							object.log('Deo Infinite Scroll ON READY ON READY : Forcing scroll to first products');
							//$(window).scrollTop(target_top);
						});
					}, 600);
					// alteration of the classes/data of the products to have the information on the page linked to the product
					object.alterDataProductsPerPage($(object.getProductsSelector()), object.CURRENT_PAGE);
				});
			} else {
				// alteration of the classes/data of the products to have the information on the page linked to the product
				object.alterDataProductsPerPage($(object.getProductsSelector()), object.CURRENT_PAGE);
			}

			// when we arrive on the page we check although we are not already at the bottom
			$(window).bind('scroll.infinitescroll', object.handleScroll);
			object.handleScroll();


			// SUPPORT MODULE BLOCKLAYERED
			if(object.HAS_FILTER_MODULE){
				$(document).ajaxComplete(function(e, jqXHR, ajaxOptions){
					// If the request is one of the blocklayered, then we need to reset the infinite scroll
					if(ajaxOptions.url.search('/modules/blocklayered') != -1 
						&& ajaxOptions.url.search('&infinitescroll=1') == -1){
						object.log('AJAX call on blocklayered');
						// the page can be different from 1 if we press F5 for example
						let matches = /\/page\-([\d]*)/.exec(ajaxOptions.url)
						let page_called = 1;
						if (matches && matches.length) {
							page_called = parseInt(matches[1]);
							if (isNaN(page_called)) {
								page_called = 1;
							}
						}

						// we deactivate the scroll if we just fetch the page!= 1
						if (page_called != 1) {
							// deactivation of the scrollto that the module has been doing for a short time only after a search, to avoid that when we load page 2 it makes us scroll...
							if (typeof $.scrollTo === "function"){
								let oldScrollTo = $.scrollTo;
								$.scrollTo = function(){
									return false;
								}
							}
						}


						object.override_page_to_call = ajaxOptions.url.replace(/\/page\-[\d*]/, '');
						object.processReset({'page':page_called});

						// override of the result processing function (since we have json here)
						object.getProductsFromAjaxResult = function(result) {
							data = JSON.parse(result);
							let base_url = document.location.href;
							// removing stuff after the #
							base_url = base_url.substr(0, base_url.indexOf('#'))
							// removal of p=XXX
							base_url = base_url.replace(/\?p=[\d]*/, '?');
							base_url = base_url.replace(/\&p=[\d]*/, '');
							// we also remove page-XX in the friendly
							data.current_friendly_url = utf8_decode(data.current_friendly_url.replace(/\/page\-[\d*]/, ''));
							// here friendly url it's just after the "#" (included)
							object.override_friendly_url = base_url + data.current_friendly_url;
							object.log('Overriding override_friendly_url with ' + object.override_friendly_url);
							let $response = $('<html />').html(utf8_decode(data.productList));
							let $products = $response.find(object.getProductsSelector());

							return $products;
						};				
						object.addPageToFriendlyUrl = function(url, page) {
							return url + '/page-'+page;
						};
						// blocklayered is badly done so good
						setInterval(function(){lockLocationChecking = true}, 10);
					}

				});// end ajaxComplete

				if (typeof prestashop !== "undefined") {
					//we check if we are on a faceted search result
					prestashop.on('updateProductList', function(data) {
						if (typeof data === 'object') {
							if (typeof data.current_url !== "undefined" && typeof data.rendered_facets !== "undefined" && typeof data.rendered_products !== "undefined") {
								object.log('AJAX call on facetedsearch');
								let ajax_url = data.current_url;
								ajax_url += (ajax_url.indexOf('?') == -1 ? '?' : '&') + 'kiwik-ajax';
								// if there is a "%26" in the url, i.e. an encoded "&",
								// we have to be careful, so we replace it with something temporary so that it doesn't get transformed into "&" (and therefore interpreted...)
								let kiwik_et = '----kiwik-et----';
								object.override_page_to_call = decodeURIComponent(
									ajax_url.replace('%26', kiwik_et)
								).replace(kiwik_et, encodeURIComponent('%26'));//forced to add an encode on the %26 to transform it into %2526 because it is decoded later in the call
									//ajaxOptions.url.replace('from-xhr', 'kiwik-ajax'));
								object.processReset();
							}
						}
					});
				}

			}// end if blocklayered is loaded
		});
		//END EVENTS DOCUMENT READY
	};

	$.DeoInfiniteScroll.prototype = {
		//UTILITIES
		log : function(msg, options) {
			let object = deoInfiniteScroll;
			if (typeof options === 'undefined') {
				options = {};
			}

			if (object.JAVASCRIPT_DEBUG_MODE) {
				if (options.error)
					console.error(msg);
				else if(options.warn)
					console.warn(msg);
				else
					console.log(msg);
			}
		},

		hidePagination : function() {
			let object = deoInfiniteScroll;
			// Why in js ? Because google needs to find the other pages !
			$(object.PAGINATION_CSS_SELECTOR).hide();
		},

		showLoading : function(options) {
			let object = deoInfiniteScroll;
			let putBefore = typeof options === 'object' && typeof options['before'] !== 'undefined' ? options['before'] : false;

			//object.log('object.showLoading()');
			if ($('.deo-infinitescroll-loading').length == 0) {
				let loader = $('<div class="deo-infinitescroll-loading" style="display:none;"><i class="icon-loading"></i></div>');
				if (!putBefore) {
					$(object.PRODUCT_LIST_CSS_SELECTOR).after(loader);
				} else {
					$(object.PRODUCT_LIST_CSS_SELECTOR).before(loader);
				}
			}
			$('.deo-infinitescroll-loading').stop(true, true).fadeIn();
		},

		hideLoading : function(options) {
			let object = deoInfiniteScroll;
			setTimeout(function(){
				$('.deo-infinitescroll-loading').remove();
			}, 500);
		},

		addProductsToPage : function(options) {
			let object = deoInfiniteScroll;
			let $products = typeof options === 'object' && typeof options['$products'] !== 'undefined' ? options['$products'] : $();
			let page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;

			object.log('object.addProductsToPage()');

			object.disableScroll();
			$(window).unbind('scroll.infinitescroll');
			object.hideLoading();

			$products = object.callbackProcessProducts($products);
			// alteration of the classes/data of the products to have the information on the page linked to the product
			object.alterDataProductsPerPage($products, page);

			$products.hide();

			let products_selector = object.getProductsSelector();
			if (page <= object.current_page_top) {
				let $firstProduct = $(products_selector).first();
				$firstProduct.before($products);
				let tmp_scrolltop = $(window).scrollTop();
				let height_before = $(object.PRODUCT_LIST_CSS_SELECTOR).height();

				$products.css('opacity', 0.3);
				$products.show();

				if ($firstProduct.length == 0) {
					object.log('object.addProductsToPage() : could not find "$firstProduct', {warn:1});
					return;
				}

				setTimeout(function(){
					// let target_top = tmp_scrolltop + $(object.PRODUCT_LIST_CSS_SELECTOR).height() - height_before;
					let target_top = $firstProduct.offset().top;
					// fix when products are too high and we wanna scroll below 0
					target_top = Math.max(0, target_top - $firstProduct.height() / 2);
					$('body, html').animate({scrollTop: target_top}, 1000, function(){
						$products.animate({'opacity': 1}, 250);
						//$(window).scrollTop(target_top);
						object.log('object.addProductsToPage() : Forcing scroll to '+target_top);
						object.waiting_for_previous_page = false;

						object.callbackAfterAjaxDisplayed();
						object.enableScroll();
						$(window).bind('scroll.infinitescroll', object.handleScroll);
					});
				}, 100);
				
			} else {
				let $lastProduct = $(products_selector).last();
				$lastProduct.after($products);

				let total_done = 0;
				$products.fadeIn({
					'duration': 200,
					'complete': function() {
						total_done++;
						if ($products.length == 0 || total_done == $products.length) {
							//when going down, no need to update scroll height
							object.waiting_for_next_page = false;
							object.callbackAfterAjaxDisplayed();
							object.enableScroll();
							$(window).bind('scroll.infinitescroll', object.handleScroll);
						}
					}
				});
			}

			//Handling list and grid view
			if(typeof bindGrid === 'function')
				bindGrid();


			//object.enableScroll();
		},

		displayMessageWhenEndPage : function(options) {
			let object = deoInfiniteScroll;
			object.log('object.displayMessageWhenEndPage()');
			if (object.HIDE_MESSAGE_END_PAGE != 1 && $('.deo-message-when-end-page').length == 0){
				let products_selector = object.getProductsSelector();

				let $button = $('<div>');
				$button.addClass('deo-message-when-end-page col-sp-12');
				$button.html(object.TEXT_MESSAGE_END_PAGE+' ');

				let $a = $('<a>');
				$a.attr('href', 'javascript:void(0)');
				$a.html(object.TEXT_BACK_TO_TOP+'<i class="icon"></i>');
				$button.append($a);

				setTimeout(function(){
					$(products_selector).last().after($button);
				}, 500);

				$button.find('a').click(function(){
					$('html,body').animate({
						scrollTop: $('#js-product-list-top').offset().top
					}, 500);
					return false;
				});
			}
		},

		hideMessageWhenEndPage : function(options) {
			let object = deoInfiniteScroll;
			$('.deo-message-when-end-page').stop(true, true).fadeOut(function(){
				$(this).remove();
			});
		},

		processEnd : function(options) {
			let object = deoInfiniteScroll;
			object.log('object.processEnd()');
			object.hideLoading();
			object.displayMessageWhenEndPage();
			//object.is_running = false;
		},

		processReset : function(options) {
			let object = deoInfiniteScroll;
			let page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;;
			object.log('object.processReset() page='+page);

			object.current_page_bottom = page;
			object.current_page_top = page;

			for(let page in object.page_cache) {
				if (!object.page_cache[page].loaded) {
					object.page_cache[page].ajax.abort();
				}
			}
			object.page_cache = {};
			object.waiting_for_next_page = false;
			object.waiting_for_previous_page = false;
			object.acceptedToLoadMoreProductsToBottom = 0;
			object.hideMessageWhenEndPage();
			object.hidePagination();
			object.hideButtonLoadMoreBottom();
			object.hideButtonLoadMoreTop();
			// prefetch current page 1
			object.getProductsPerPage({'page': 1});
			object.alterDataProductsPerPage($(object.getProductsSelector()), 1);
		},


		getProductsSelector : function() {
			let object = deoInfiniteScroll;
			 return object.PRODUCT_LIST_CSS_SELECTOR.split(',').map(function(a){return a+ ' ' + object.ITEM_CSS_SELECTOR;}).join(', ');
		},

		getOffset : function(options){
			let object = deoInfiniteScroll;
			let topOffset = typeof options === 'object' && typeof options['top'] !== 'undefined' ? options['top'] : false;

			let products_selector = object.getProductsSelector();
			let offset = (!topOffset) ? $(products_selector).last().offset() : $(products_selector).first().offset();

			if (offset == null){
				offset = {top:0, left:0};
			}
			return offset;
		},


		displayLoadMoreLabelToTop : function(options) {
			let object = deoInfiniteScroll;
			let page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;

			object.log('object.displayLoadMoreLabelToTop()');

			if (page < 1) {
				return;
			}

			let $button = $('<div>');
			$button.addClass('deo-infinite-load-more').addClass('deo-load-more-top');

			let $a = $('<a>');
			$a.attr('href', 'javascript:void(0)');
			$a.html(object.TEXT_LOAD_MORE+'<i class="icon"></i>');
			$button.append($a);

			$(object.PRODUCT_LIST_CSS_SELECTOR).before($button);

			$button.find('a').click(function(){
				$(this).parent().remove();
				object.showLoading({'before':true});

				let $products = object.getProductsPerPage({'page': page, 'callback':object.displayPage});
				// if it's preloaded we do it now, otherwise we wait eh
				if ($products !== false) {
					object.displayPage({'page':page, '$products': $products, 'before':true});	
				}
				return false;
			});
		},

		displayLoadMoreLabelToBottom : function(options) {
			let object = deoInfiniteScroll;
			let page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;

			object.log('object.displayLoadMoreLabelToBottom()');

			if (object.page_cache[page] != undefined && object.page_cache[page].loaded) {
				let $products = object.page_cache[page].products
				let page_one_is_loaded_and_identical = object.isEqualToPageOne({'page':page}) && object.page_cache[1].loaded == true;
				let page_is_empty = $products.length == 0;
				if (page_one_is_loaded_and_identical || page_is_empty) {
					object.hideButtonLoadMoreBottom();
					object.processEnd();
					return;
				}
			}

			if($('.deo-load-more-bottom').length == 0){
				$('.deo-infinitescroll-loading').hide();

				let $button = $('<div>');
				$button.addClass('deo-infinite-load-more').addClass('deo-load-more-bottom');

				let $a = $('<a>');
				$a.attr('href', 'javascript:void(0)');
				$a.html(object.TEXT_LOAD_MORE+'<i class="icon"></i>');
				$button.append($a);

				$(object.PRODUCT_LIST_CSS_SELECTOR).after($button);

				$button.find('a').click(function(){
					$(this).parent().remove();
					deoInfiniteScroll.acceptedToLoadMoreProductsToBottom++;
					$(window).trigger('scroll.infinitescroll');

					return false;
				});
			}	
		},

		hideButtonLoadMoreBottom : function(options) {
			let object = deoInfiniteScroll;
			object.log('object.hideButtonLoadMoreBottom()');	
			$('.deo-load-more-bottom').remove();
		},

		hideButtonLoadMoreTop : function(options) {
			let object = deoInfiniteScroll;
			object.log('object.hideButtonLoadMoreTop()');	
			$('.deo-load-more-top').remove();
		},

		getParamsFromUrl : function(url) {
			let object = deoInfiniteScroll;
			let hash;
			let json_result = {};
			if (url.indexOf('?') !== -1) {
				let hashes = url.substr(url.indexOf('?') + 1).split('&');
				for (let i = 0; i < hashes.length; i++) {
					hash = hashes[i].split('=');
					if (hash.length == 2) {
						json_result[hash[0]] = decodeURIComponent(hash[1]);
					}
				}
			}
			return json_result;
		},

		getUrlToFetch : function(params) {
			let object = deoInfiniteScroll;
			let base = document.location.href;
			if (object.override_page_to_call)
				base = object.override_page_to_call;

			if (params !== undefined) {
				let full_params = object.getParamsFromUrl(base);
				if (base.indexOf('?') !== -1)
					base = base.substr(0, base.indexOf('?'));
				for(let name in params) {
					full_params[name] = params[name];
				}
				params = '?' + (Object.keys(full_params).length  > 0 ? decodeURIComponent($.param(full_params)):'');
			} else {
				params = '';
			}

			return base + params;
		},

		updateUrl : function(page) {
			let object = deoInfiniteScroll;
			if (page == 0 || isNaN(page)) {
				return;
			}
			
			if (page != object.CURRENT_PAGE) {
				window.history.pushState(page, false, object.getFriendlyUrl({'page': page}));
				if (typeof ga !== "undefined") {
					let friendly_url = object.getFriendlyUrl({'page': page});
					friendly_url = friendly_url.replace(prestashop.urls.base_url, '/');
					ga('set', 'page', friendly_url);
					ga('send', 'pageview');
				}
			}
			object.CURRENT_PAGE = page;
		},

		displayPage : function(options) {
			let object = deoInfiniteScroll;
			let $products = typeof options === 'object' && typeof options['$products'] !== 'undefined' ? options['$products'] : $();
			let page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;
			object.log('object.displayPage() : page='+page);

			//verif de si ils sont pas déjà affichés, si oui on est au bout ?
			let page_one_is_loaded_and_identical = object.isEqualToPageOne({'page':page}) && object.page_cache[1].loaded == true;
			let page_is_empty = $products.length == 0;
			if ((page_one_is_loaded_and_identical || page_is_empty) && page > object.current_page_bottom) {
				object.processEnd();
				return;
			}

			object.addProductsToPage({'$products': $products, 'page': page});

			object.current_page_bottom = Math.max(object.current_page_bottom, page);
			object.current_page_top = Math.min(object.current_page_top, page);

			object.updateUrl(page);

			//we throw a little prefetch gift at this place
			object.prefetchMultipleProductPages({'page': page, 'number': 3});
		},

		getFriendlyUrl : function(options) {
			let object = deoInfiniteScroll;
			let page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;
			
			if (object.override_friendly_url) {
				return typeof object.addPageToFriendlyUrl === "function" ? object.addPageToFriendlyUrl(object.override_friendly_url, page) : '';
			}

			params = {};
			params[object.DEFAULT_PAGE_PARAMETER] = page;
			let result = object.getUrlToFetch(params);
			result = result.replace(/([?&]+kiwik-ajax[=]?[\d]*)/gi, '');
			//removal of p=1 if that's all
			if (page == 1) {
				result = result.replace(/&?p=[\d]*/,'').replace(/\?$/, '');
			}

			return result;
		},

		isEqualToPageOne : function(options) {
			let object = deoInfiniteScroll;
			let page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;

			//small exception to allow real page 1
			if (page == 1)
				return false;

			let page_one_products = object.page_cache[1].products;
			let page_to_check_products = object.page_cache[page].products;
			if(page_one_products == null || page_to_check_products == null ||page_one_products.length == 0 || page_to_check_products == 0) {
				return true;
			}
			
			if (object.page_cache[1].products_html_raw == object.page_cache[page].products_html_raw) {
				return true;
			}

			//otherwise we check if the links inside are the same or not
			let page_one_link = '';
			page_one_products.find('a').each(function(){page_one_link += $(this).attr('href');});
			let page_to_check_links = '';
			page_to_check_products.find('a').each(function(){page_to_check_links += $(this).attr('href');});

			return page_one_link == page_to_check_links;
		},

		getProductsFromAjaxResult : function(result) {
			let object = deoInfiniteScroll;
			//deletion of script tags otherwise they are loaded...
			result = result.replace(/<script(.*?)>(.*?)<\/script>/gi, '');

			let $response = $('<html />').html(result);
			let $products = $response.find(object.getProductsSelector());
			return $products;
		},

		prefetchMultipleProductPages : function(options) {
			let object = deoInfiniteScroll;
			let page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;
			let number = typeof options === 'object' && typeof options['number'] !== 'undefined' ? options['number'] : 1;

			if(number > 0) {
				for (let i = 0; i < number; i++) {
					//we block the prefetch at 3 at most
					if (object.page_cache[page +i] == undefined 
						&& page + i <= object.current_page_bottom + 3
						&& page + i >= object.current_page_top - 1) {
							object.getProductsPerPage({'page': page + i});
					}
				}
			} else if (number < 0) {
				for (let i = 0; i > number; i--) {
					if (page + i > 0) {
						object.getProductsPerPage({'page': page + i});
					}
				}
			}
		},

		getProductsPerPage : function(options) {
			let object = deoInfiniteScroll;
			let page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;
			let callback = typeof options === 'object' && typeof options['callback'] !== 'undefined' ? options['callback'] : null;

			if (object.page_cache[page] !== undefined && object.page_cache[page].loaded === true) {
				return object.page_cache[page].products;
			}

			if (object.page_cache[page] !== undefined && object.page_cache[page].loaded === false) {
				//if we ask for a callback we overwrite the previous one
				if (typeof callback === 'function') {
					object.log('object.getProductsPerPage() : Adding a callback to page ='+page);
					object.page_cache[page].callback = callback;
				}
				return false;
			}

			if(page != 0) {
				object.log(
					'object.getProductsPerPage() : page='+page+
					(typeof callback == "function" ? ' with a callback : ' + callback.toString().substr(0, callback.toString().indexOf('(')) : ' without callback')
				);
			}

			let params = {'infinitescroll':1};
			params[object.DEFAULT_PAGE_PARAMETER] = page;

			let customxhr = new XMLHttpRequest();
			let calledURL = object.getUrlToFetch(params);
			let ajax_id = $.ajax({
				url: calledURL,
				type: 'GET',
				cache: true,
				xhr: function() {
					return customxhr;
				},
				success: function(result, status, jqXHR) {
					if (status == 'abort') {
						return;
					}
					// attempted fix redirect advanced search pages SEO which redirects when requesting a page that is too large
					if (customxhr && typeof customxhr.responseURL != 'undefined') {
						// fix attempt if we ask for page 0 which redirects to page 1, we don't cut everything
						if (customxhr.responseURL != calledURL && page > 1) {
							object.log('object.getProductsPerPage() : got a redirection from '+calledURL+' to '+customxhr.responseURL);
							result = '';//we put this so that the page goes well "loaded" but without products, so that it stops, otherwise it waits ad infinitum
							//do we cut if we are down?
							if (page == object.current_page_bottom+1) {
								object.waiting_for_next_page = true;
								object.processEnd();
							}
						}
					}

					object.log('object.getProductsPerPage() : ajax result for page '+page);	
					$products = object.getProductsFromAjaxResult(result);
					//object.log($products);
					object.page_cache[page].products = $products;
					object.page_cache[page].products_html_raw = $products.html();
					object.page_cache[page].loaded = true;

					if (typeof object.page_cache[page].callback === 'function')
						object.page_cache[page].callback({'$products':$products, 'page':page});
					//at this stage we can launch a prefetch on the next ones if there are not several pages?
					object.prefetchMultipleProductPages({'page': page, 'number': 3});
				},
				error: function(result, status, jqXHR) {
					if (status == 'abort') {
						return;
					}
					if (status == 'error') {
						//to block bottom loading if it bugged
						if (page == object.current_page_bottom+1) {
							object.waiting_for_next_page = true;
							object.processEnd();
						}
						return;
					}
				}
			});

			object.page_cache[page] = {
				'loaded' : false,
				'products' : null,
				'products_html_raw' : '',
				'ajax' : ajax_id,
				'callback' : callback
			};
			
			return false;
		},

		//alteration of the classes/data of the products to have the information on the page linked to the product
		alterDataProductsPerPage : function($products, page) {
			let object = deoInfiniteScroll;
			object.log('object.alterDataProductsPerPage() for page='+page);
			if (isNaN(page) || page == 0) {
				return;
			}
			$products.attr('data-page', page);
			$products.addClass('page-'+page);

			$('body').off('mouseenter touchstart', object.getProductsSelector())
				.on('mouseenter touchstart', object.getProductsSelector(), function(){
					let page = parseInt($(this).attr('data-page'));
					object.updateUrl(page);
					//we store it in the local storage
					object.saveProductToBeVisited($(this).find('a').first().attr('href'), page, document.location.href);
			});
		},

		//we store it in the local storage
		saveProductToBeVisited : function(product_link, page, current_url) {
			let object = deoInfiniteScroll;
			try {
				localStorage.setItem('is_product_link', product_link);
				localStorage.setItem('is_page', page);
				localStorage.setItem('is_current_url', current_url);
			} catch (e) {
				object.log('object.saveProductToBeVisited() : localStorage Failed', {warn: true});
			}
		},

		loadProductVisitedInfos : function() {
			let object = deoInfiniteScroll;
			let product_link = null;
			let page = null;
			let current_url = null;
			try {
				product_link = localStorage.getItem('is_product_link');
				page = localStorage.getItem('is_page');
				current_url = localStorage.getItem('is_current_url');
			} catch (e) {
				object.log('object.saveProductToBeVisited() : loadProductVisitedInfos Failed', {warn: true});
			}

			let result = {
				product_link: product_link,
				page: page,
				current_url: current_url
			};

			object.log('object.loadProductVisitedInfos() : ', result);
			object.saveProductToBeVisited('', 0, document.location.href);
			return result;
		},




		updateCacheScrolltop : function(force_value) {
			let object = deoInfiniteScroll;
			object.cache_scrolltop = force_value !== undefined ? force_value : $(window).scrollTop();
		},

		handleScroll : function() {
			object = deoInfiniteScroll;

			let delta = object.cache_scrolltop - $(window).scrollTop();
			object.updateCacheScrolltop();
			if (!object.is_running)
				return;

			//if we have "blocked" the scroll, then we do not launch a search or anything
			if (object.active) {
				return;
			}

			object.hidePagination();

			//as long as page 1 is not loaded, we do nothing? since this is our stopping condition
			if ((object.page_cache[1] === undefined || object.page_cache[1].loaded == false)) {
				return false;
			} 	

			let offsetBottom = object.getOffset();
			let offsetTop = object.getOffset({'top':true});
			//if we go down
			if (delta <= 0 && offsetBottom.top-$(window).height() <= $(window).scrollTop() && !object.waiting_for_next_page) {
				if(object.DISPLAY_LOAD_MORE_PRODUCT){
					if (object.NUMBER_PAGE_SHOW_LOAD_MORE_PRODUCT + object.acceptedToLoadMoreProductsToBottom * object.FREQUENCY_SHOW_LOAD_MORE_PRODUCT < object.current_page_bottom + 1){
						object.displayLoadMoreLabelToBottom({'page':object.current_page_bottom + 1});
						return;
					}
				}

				object.waiting_for_next_page = true;
				object.showLoading();

				let $products = object.getProductsPerPage({'page': object.current_page_bottom+1, 'callback':object.displayPage});
				//if it's preloaded we do it now, otherwise we wait eh
				if ($products !== false){
					object.displayPage({'page': object.current_page_bottom+1, '$products': $products, 'before':false});			
				}
			} 
			//if we go up
			else if (delta > 0 && $(window).scrollTop() < offsetTop.top && !object.waiting_for_previous_page && object.current_page_top > 1) {
				object.waiting_for_previous_page = true;
				
				object.displayLoadMoreLabelToTop({'page': object.current_page_top-1});		
			}
			//otherwise we preload down
			else {
				object.getProductsPerPage({'page': object.current_page_bottom+1});
				object.getProductsPerPage({'page': object.current_page_top-1});
				//if we are on classic scroll, we check if we have to update the url according to the number of products visible per page
				let scrollTop = $(window).scrollTop();
				let windowHeight = $(window).height();
				let products_per_page = {};
				$(object.getProductsSelector()).each(function(){
					let product_top =$(this).offset().top;
					let product_bottom =  product_top + $(this).height();

					if ( (product_bottom > scrollTop && product_bottom < scrollTop + windowHeight)
						|| (product_top > scrollTop && product_top < scrollTop + windowHeight)) {
						let page = parseInt($(this).attr('data-page'));
						if (typeof products_per_page[page] === "undefined")
							products_per_page[page] = 0;
						products_per_page[page]++;
					}
				});
				let most_products_per_page = 0;
				let best_page = null;
				for(let page in products_per_page) {
					let nb = products_per_page[page];
					if (nb >= most_products_per_page) {
						most_products_per_page = nb;
						best_page = page;
					}
				}
				if (best_page) {
					//if we go to the top, and the page with the most product is "lower" than the url
					if (delta > 0 && best_page < object.CURRENT_PAGE)
						object.updateUrl(best_page);
					else if (delta < 0 && best_page > object.CURRENT_PAGE)
						object.updateUrl(best_page);
				}
			}
		},



		preventDefault : function(e) {
			e = e || window.event;
			if (e.cancelable) { 
				if (e.preventDefault)
					e.preventDefault();
					e.returnValue = false;  
			}	
		},

		preventDefaultForScrollKeys : function(e) {
			let object = deoInfiniteScroll;
			if (object.antiScroll.keys[e.keyCode]) {
				object.preventDefault(e);
				return false;
			}
		},

		disableScroll : function() {
			let object = deoInfiniteScroll;
			object.active = true;
			object.log('object.disableScroll', {'warn':true});

			// older FireFox
			if (window.addEventListener){
				window.addEventListener('DOMMouseScroll', object.preventDefault, false);
			}

			window.onwheel = object.preventDefault; // modern standard
			window.onmousewheel = document.onmousewheel = object.preventDefault; // older browsers, IE
			window.ontouchmove  = object.preventDefault; // mobile
			document.onkeydown  = object.preventDefaultForScrollKeys;
		},

		enableScroll : function() {
			let object = deoInfiniteScroll;
			object.active = false;
			object.log('object.enableScroll', {'warn':true});
			if (window.removeEventListener){
				window.removeEventListener('DOMMouseScroll', object.preventDefault, false);
			}
			window.onmousewheel = document.onmousewheel = null; 
			window.onwheel = null; 
			window.ontouchmove = null;  
			document.onkeydown = null;  
		},
	};
}(jQuery));