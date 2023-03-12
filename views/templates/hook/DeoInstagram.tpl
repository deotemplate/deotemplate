{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{* {if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
	{if isset($formAtts.lib_error) && $formAtts.lib_error}
		<div class="alert alert-warning deo-widget-error">{$formAtts.lib_error}</div>
	{/if}
{else} *}
	{function name=deo_instagram}
		<div id="instafeed-{$formAtts.form_id}" class="deo-instagram"
			data-carousel_type="{(isset($formAtts.carousel_type) && $formAtts.carousel_type) ? $formAtts.carousel_type : ''}" 
			{* data-show_like="{(isset($formAtts.show_like) && $formAtts.show_like) ? 'true' : 'false'}" 
			data-show_comment="{(isset($formAtts.show_comment) && $formAtts.show_comment) ? 'true' : 'false'}"  *}
			data-show_icon="{(isset($formAtts.show_icon) && $formAtts.show_icon) ? 'true' : 'false'}" 
			data-show_title="{(isset($formAtts.show_title) && $formAtts.show_title) ? 'true' : 'false'}" 
			data-client_id="{(isset($formAtts.client_id) && $formAtts.client_id) ? $formAtts.client_id : ''}" 
			data-access_token="{(isset($formAtts.access_token) && $formAtts.access_token) ? $formAtts.access_token : ''}" 
			data-user_id="{(isset($formAtts.user_id) && $formAtts.user_id) ? $formAtts.user_id : ''}" 
			data-sort_by="{(isset($formAtts.sort_by) && $formAtts.sort_by) ? $formAtts.sort_by : ''}" 
			data-limit="{(isset($formAtts.limit) && $formAtts.limit) ? $formAtts.limit : ''}" 
			data-resolution="{(isset($formAtts.resolution) && $formAtts.resolution) ? $formAtts.resolution : ''}" 
			data-array_fake_item="{(isset($formAtts.array_fake_item) && count($formAtts.array_fake_item)) ? json_encode($formAtts.array_fake_item) : ''}"
			data-text_instagram="{l s='Instagram' mod='deotemplate'}" 
			data-refresh_api_token="{(isset($formAtts.refresh_api_token) && $formAtts.refresh_api_token) ? 'true' : 'false'}" 

			{* {if $formAtts.carousel_type == "owlcarousel"}
				data-items="{if $formAtts.items}{$formAtts.items}{else}false{/if}" 
				data-itemsdesktop="{if isset($formAtts.itemsdesktop) && $formAtts.itemsdesktop}[1500,{$formAtts.itemsdesktop}]{else}false{/if}" 
				data-itemsdesktopsmall="{if isset($formAtts.itemsdesktopsmall) && $formAtts.itemsdesktopsmall}[1200,{$formAtts.itemsdesktopsmall}]{else}false{/if}" 
				data-itemstablet="{if isset($formAtts.itemstablet) && $formAtts.itemstablet}[992,{$formAtts.itemstablet}]{else}false{/if}" 
				data-itemstabletsmall="{if isset($formAtts.itemstabletsmall) && $formAtts.itemstabletsmall}[768,{$formAtts.itemstabletsmall}]{else}false{/if}" 
				data-itemsmobile="{if isset($formAtts.itemsmobile) && $formAtts.itemsmobile}[576,{$formAtts.itemsmobile}]{else}false{/if}" 
				data-itemssmallmobile="{if isset($formAtts.itemssmallmobile) && $formAtts.itemssmallmobile}[480,{$formAtts.itemssmallmobile}]{else}false{/if}" 
				data-itempercolumn="{if isset($formAtts.itempercolumn) && $formAtts.itempercolumn}{$formAtts.itempercolumn}{else}1{/if}" 
				data-itemscustom="{if isset($formAtts.itemscustom) && $formAtts.itemscustom}{$formAtts.itemscustom}{else}false{/if}" 
				data-slidespeed="{if isset($formAtts.slidespeed) && $formAtts.slidespeed}{$formAtts.slidespeed}{else}200{/if}" 
				data-paginationspeed="{if isset($formAtts.paginationspeed) && $formAtts.paginationspeed}{$formAtts.paginationspeed}{else}800{/if}" 
				data-autoplay="{if isset($formAtts.autoplay) && $formAtts.autoplay}true{else}false{/if}" 
				data-stoponhover="{if isset($formAtts.stoponhover) && $formAtts.stoponhover}true{else}false{/if}" 
				data-navigation="{if isset($formAtts.navigation) && $formAtts.navigation}true{else}false{/if}" 
				data-pagination="{if isset($formAtts.pagination) && $formAtts.pagination}true{else}false{/if}" 
				data-paginationnumbers="{if isset($formAtts.paginationnumbers) && $formAtts.paginationnumbers}true{else}false{/if}" 
				data-responsive="{if isset($formAtts.responsive) && $formAtts.responsive}true{else}false{/if}" 
				data-lazyload="{if isset($formAtts.lazyload) && $formAtts.lazyload}true{else}false{/if}" 
				data-lazyfollow="{if isset($formAtts.lazyfollow) && $formAtts.lazyfollow}true{else}false{/if}" 
				data-lazyeffect="{if isset($formAtts.lazyeffect) && $formAtts.lazyeffect}{$formAtts.lazyeffect}{/if}" 
				data-autoheight="{if isset($formAtts.autoheight) && $formAtts.autoheight}true{else}false{/if}" 
				data-mousedrag="{if isset($formAtts.mousedrag) && $formAtts.mousedrag}true{else}false{/if}" 
				data-touchdrag="{if isset($formAtts.touchdrag) && $formAtts.touchdrag}true{else}false{/if}" 
				data-direction="{if isset($IS_RTL) && $IS_RTL}rtl{else}false{/if}" 
				data-mousewheel="{if isset($formAtts.mousewheel) && $formAtts.mousewheel}true{else}false{/if}" 
				data-showloading="{if isset($formAtts.showloading) && $formAtts.showloading}true{else}false{/if}" 
			{elseif $formAtts.carousel_type == "slickcarousel"}
				data-centermode="{if isset($formAtts.slick_centermode) && $formAtts.slick_centermode}true{else}false{/if}" 
				data-dots="{if isset($formAtts.slick_dot) && $formAtts.slick_dot}true{else}false{/if}" 
				data-adaptiveheight="{if isset($formAtts.slick_autoheight) && $formAtts.slick_autoheight}true{else}false{/if}" 
				data-infinite="{if isset($formAtts.slick_loopinfinite) && $formAtts.slick_loopinfinite}true{else}false{/if}" 
				data-vertical="{if isset($formAtts.slick_vertical) && $formAtts.slick_vertical}true{else}false{/if}" 
				data-verticalswiping="{if isset($formAtts.slick_vertical) && $formAtts.slick_vertical}true{else}false{/if}" 
				data-autoplay="{if isset($formAtts.slick_autoplay) && $formAtts.slick_autoplay}true{else}false{/if}" 
				data-autoplayspeed="{if isset($formAtts.slick_autoplayspeed) && $formAtts.slick_autoplayspeed}{$formAtts.slick_autoplayspeed}{/if}" 
				data-pauseonhover="{if isset($formAtts.slick_pauseonhover) && $formAtts.slick_pauseonhover}true{else}false{/if}" 
				data-arrows="{if isset($formAtts.slick_arrows) && $formAtts.slick_arrows}true{else}false{/if}" 
				data-itempercolumn="{if isset($formAtts.slick_row) && $formAtts.slick_row}{$formAtts.slick_row}{/if}" 
				data-slidestoshow="{if isset($formAtts.slick_slidestoshow) && $formAtts.slick_slidestoshow}{$formAtts.slick_slidestoshow}{/if}" 
				data-slidestoscroll="{if isset($formAtts.slick_slidestoscroll) && $formAtts.slick_slidestoscroll}{$formAtts.slick_slidestoscroll}{/if}" 
				data-rtl="{if isset($IS_RTL) && $IS_RTL}true{else}false{/if}" 
				data-lazyload="{if isset($formAtts.slick_lazyload) && $formAtts.slick_lazyload}true{else}false{/if}" 
				data-lazyloadtype="{if isset($formAtts.slick_lazyload_type) && $formAtts.slick_lazyload_type}{$formAtts.slick_lazyload_type}{/if}" 
				data-responsive="{if isset($formAtts.slick_items_custom) && $formAtts.slick_items_custom}{json_encode($formAtts.slick_items_custom)}{/if}" 
				data-mousewheel="{if isset($formAtts.slick_mousewheel) && $formAtts.slick_mousewheel}true{else}false{/if}" 
				data-fade="{if isset($formAtts.slick_fade) && $formAtts.slick_fade}true{else}false{/if}" 
			{/if}  *}
			{if $formAtts.carousel_type == "slickcarousel"}
				data-centermode="{if isset($formAtts.slick_centermode) && $formAtts.slick_centermode}true{else}false{/if}" 
				data-dots="{if isset($formAtts.slick_dot) && $formAtts.slick_dot}true{else}false{/if}" 
				data-adaptiveheight="{if isset($formAtts.slick_autoheight) && $formAtts.slick_autoheight}true{else}false{/if}" 
				data-infinite="{if isset($formAtts.slick_loopinfinite) && $formAtts.slick_loopinfinite}true{else}false{/if}" 
				data-vertical="{if isset($formAtts.slick_vertical) && $formAtts.slick_vertical}true{else}false{/if}" 
				data-verticalswiping="{if isset($formAtts.slick_vertical) && $formAtts.slick_vertical}true{else}false{/if}" 
				data-autoplay="{if isset($formAtts.slick_autoplay) && $formAtts.slick_autoplay}true{else}false{/if}" 
				data-autoplayspeed="{if isset($formAtts.slick_autoplayspeed) && $formAtts.slick_autoplayspeed}{$formAtts.slick_autoplayspeed}{/if}" 
				data-pauseonhover="{if isset($formAtts.slick_pauseonhover) && $formAtts.slick_pauseonhover}true{else}false{/if}" 
				data-arrows="{if isset($formAtts.slick_arrows) && $formAtts.slick_arrows}true{else}false{/if}" 
				data-itempercolumn="{if isset($formAtts.slick_row) && $formAtts.slick_row}{$formAtts.slick_row}{/if}" 
				data-slidestoshow="{if isset($formAtts.slick_slidestoshow) && $formAtts.slick_slidestoshow}{$formAtts.slick_slidestoshow}{/if}" 
				data-slidestoscroll="{if isset($formAtts.slick_slidestoscroll) && $formAtts.slick_slidestoscroll}{$formAtts.slick_slidestoscroll}{/if}" 
				data-rtl="{if isset($IS_RTL) && $IS_RTL}true{else}false{/if}" 
				data-lazyload="{if isset($formAtts.slick_lazyload) && $formAtts.slick_lazyload}true{else}false{/if}" 
				data-lazyloadtype="{if isset($formAtts.slick_lazyload_type) && $formAtts.slick_lazyload_type}{$formAtts.slick_lazyload_type}{/if}" 
				data-responsive="{if isset($formAtts.slick_items_custom) && $formAtts.slick_items_custom}{json_encode($formAtts.slick_items_custom)}{/if}" 
				data-mousewheel="{if isset($formAtts.slick_mousewheel) && $formAtts.slick_mousewheel}true{else}false{/if}" 
				data-fade="{if isset($formAtts.slick_fade) && $formAtts.slick_fade}true{else}false{/if}" 
			{/if}
		></div>
		{if isset($formAtts.profile_link) && $formAtts.profile_link}
			<p class="link-instagram">
				<a href="https://instagram.com/{$formAtts.profile_link}" title="{l s='View all in instagram' mod='deotemplate'}" target="_blank">
					<i class="deo-custom-icons"></i> {l s='View all in instagram' mod='deotemplate'}
				</a>
			</p>
		{/if}
	{/function}

	{if !isset($formAtts.accordion_type) || $formAtts.accordion_type == 'full'}{* Default : always full *}
		<div class="block {(isset($formAtts.class)) ? $formAtts.class : ''} instagram-block {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				<div class="box-title">
			{/if}
				{if isset($formAtts.title) && $formAtts.title}
					<h4 class="title_block">{$formAtts.title}</h4>
				{/if}
				{if isset($formAtts.sub_title) && $formAtts.sub_title}
					<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
				{/if}
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				</div>
			{/if}
			{if isset($formAtts.client_id) && $formAtts.client_id}
				<div class="block_content">
					{deo_instagram}
				</div>
			{/if}
		</div>
	{elseif isset($formAtts.accordion_type) && ($formAtts.accordion_type == 'accordion' || $formAtts.accordion_type == 'accordion_small_screen' || $formAtts.accordion_type == 'accordion_mobile_screen')}
		<div class="block {(isset($formAtts.class)) ? $formAtts.class : ''} instagram-block block-toggler{if $formAtts.accordion_type == 'accordion_small_screen'} accordion_small_screen{elseif $formAtts.accordion_type == 'accordion_mobile_screen'} accordion_mobile_screen{/if}">
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				<div class="box-title">
			{/if}
				{if isset($formAtts.title) && $formAtts.title}
					<div class="title clearfix">
						<h4 class="title_block">{$formAtts.title}</h4>
						<span class="navbar-toggler collapse-icons" data-target="#widget-instagram-{$formAtts.form_id}" data-toggle="collapse">
							<i class="add"></i>
							<i class="remove"></i>
						</span>
					</div>
				{/if}
				{if isset($formAtts.sub_title) && $formAtts.sub_title}
					<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
				{/if}
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				</div>
			{/if}
			{if isset($formAtts.client_id) && $formAtts.client_id}
				<div class="collapse block_content" id="widget-instagram-{$formAtts.form_id}">
					{deo_instagram}
				</div>
			{/if}
		</div>
	{/if}
{* {/if} *}