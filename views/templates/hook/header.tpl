{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{* load css skin *}
<link rel="preconnect" href="https://fonts.gstatic.com">
{if isset($deo_primary_custom_font) && $deo_primary_custom_font}
	<link href="https://fonts.googleapis.com/css2?family={$deo_primary_custom_font|replace:' ':'+'}:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
{/if}
{if isset($deo_second_custom_font) && $deo_second_custom_font}
	<link href="https://fonts.googleapis.com/css2?family={$deo_second_custom_font|replace:' ':'+'}:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
{/if}

{* load css skin *}
{if isset($deo_url_skin) && $deo_url_skin}
	<link rel="stylesheet" id="deo-dynamic-skin-css" href="{$deo_url_skin}" type="text/css" media="all">
{/if}

{* load css position *}
{if !empty($deo_url_css_positions)}
	{foreach from=$deo_url_css_positions item=item}
		<link rel="stylesheet" class="deo-url-css-position" href="{$item}" type="text/css" media="all">
	{/foreach}
{/if}
{if isset($deo_url_css_profile)}
	<link rel="stylesheet" id="deo-url-css-profile" href="{$deo_url_css_profile}" type="text/css" media="all">
{/if}

{* load api google map *}
{if (isset($deo_has_google_map) && $deo_has_google_map)}
	{if $key_google_map}
		<script type="text/javascript" src="//maps.google.com/maps/api/js?key={$key_google_map}" async></script>
	{else}
		<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=false" async></script>
	{/if}
{/if}

{* load lib social login *}
{* {if $fb_enable && $fb_app_id != ''}
    {literal}
    <script type="text/javascript">
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '{/literal}{$fb_app_id}{literal}',
                cookie     : true,  // enable cookies to allow the server to access 
                xfbml      : true,  // parse social plugins on this page
                version    : 'v2.9', // use graph api version 2.8
                scope: 'email, user_birthday',
            });
        };

        // Load the SDK asynchronously
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id; js.async = " ";
            js.src = "//connect.facebook.net/{/literal}{$lang_locale|escape:'html':'UTF-8'}{literal}/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
    {/literal}
{/if}
{if $google_enable && $google_client_id != ''}
	<script type="text/javascript">
		var google_client_id= "{$google_client_id|escape:'html':'UTF-8'}";
	</script>
	<script type="text/javascript" src="https://apis.google.com/js/api:client.js" async></script>
{/if} *}

<script type="text/javascript">
	// List functions will run when document.ready()
	var deo_functions_document_ready = [];

	// List functions will run when window.load()
	var deo_functions_windown_loaded = [];

	var deoAjaxConfigures = {
		ajax_enable: {$ajax_enable},
		qty_category: {$qty_category},
		more_product_img: {$more_product_img},
		second_img: {$second_img},
		countdown: {$countdown},
		animation: {$animation},
	}

	var DeoTemplate;
	deo_functions_document_ready.push(function(){
		if (typeof $.DeoTemplate !== "undefined" && $.isFunction($.DeoTemplate)) {
		    DeoTemplate = new $.DeoTemplate();
		}
	});

	deo_functions_document_ready.push(function(){
		DeoTemplate.processAjax();
	});

	{if isset($enable_infinite_scroll) && $enable_infinite_scroll}
		var DeoInfiniteScroll;
		deo_functions_document_ready.push(function(){
			if (typeof $.DeoInfiniteScroll !== "undefined" && $.isFunction($.DeoInfiniteScroll)) {
				var config_deo_infinite_scroll = {
					PRODUCT_LIST_CSS_SELECTOR : "{$infinite_scroll_product_list_css_selector|replace:'"':"'" nofilter}",
					ITEM_CSS_SELECTOR : "{$infinite_scroll_item_css_selector|replace:'"':"'" nofilter}",
					PAGINATION_CSS_SELECTOR : "{$infinite_scroll_pagination_css_selector|replace:'"':"'" nofilter}",
					HIDE_MESSAGE_END_PAGE : {$infinite_scroll_hide_message_end_page|intval},
					TEXT_MESSAGE_END_PAGE : "{$infinite_scroll_text_message_end_page|escape:'html':'UTF-8'}",
					TEXT_BACK_TO_TOP : "{$infinite_scroll_text_back_to_top|escape:'html':'UTF-8'}",
					TEXT_ERROR : "{$infinite_scroll_text_error|escape:'html':'UTF-8'}",
					TEXT_LOAD_MORE : "{$infinite_scroll_text_loadmore|escape:'html':'UTF-8'}",
					HAS_FILTER_MODULE : {$infinite_scroll_has_filter_module|intval},
					DISPLAY_LOAD_MORE_PRODUCT : {$infinite_scroll_display_load_more_product|intval},
					NUMBER_PAGE_SHOW_LOAD_MORE_PRODUCT : {$infinite_scroll_number_page_show_load_more_product|intval},
					FREQUENCY_SHOW_LOAD_MORE_PRODUCT : {$infinite_scroll_frequency_show_load_more_product|intval},
					CURRENT_PAGE : {$infinite_scroll_current_page|intval},
					PS_INSTANT_SEARCH : {$ps_instant_search|intval},
					// default value, used in case you want the "stop bottom" feature
					acceptedToLoadMoreProductsToBottom : 0,
					JAVASCRIPT_DEBUG_MODE : 0,
				};

				deoInfiniteScroll = new $.DeoInfiniteScroll(config_deo_infinite_scroll);

				deoInfiniteScroll.callbackAfterAjaxDisplayed = function() {
					deoInfiniteScroll.log('deoInfiniteScroll.callbackAfterAjaxDisplayed()');
					$(document).trigger('is-callbackAfterAjaxDisplayed');
					{$infinite_scroll_js_script_after nofilter}

					// init for product list just loaded
					DeoTemplate.initTooltip();
					DeoTemplate.processAjaxProduct();
					DeoTemplate.initShadownHover();
				}

				deoInfiniteScroll.callbackProcessProducts = function($products) {
					deoInfiniteScroll.log('deoInfiniteScroll.callbackProcessProducts()');
					{$infinite_scroll_js_script_process nofilter}
					return $products;
				};
			}
		}); 
	{/if}
	
</script>

