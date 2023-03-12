{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
    {if isset($formAtts.lib_error) && $formAtts.lib_error}
        <div class="alert alert-warning deo-widget-error">{$formAtts.lib_error}</div>
    {/if}
{else}
    {if !empty($categories)}
		<div class="slick-row">
			<div class="slick-carousel deo-carousel slick-slider deo-carousel-loading {if isset($formAtts.slick_showloading) && $formAtts.slick_showloading}show-icon-loading{/if}" id="{$formAtts.form_id|escape:'html':'UTF-8'}" 
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
				data-slidestoshow="{if isset($formAtts.slick_slidestoshow) && $formAtts.slick_slidestoshow}{$formAtts.slick_slidestoshow}{/if}" 
				data-slidestoscroll="{if isset($formAtts.slick_slidestoscroll) && $formAtts.slick_slidestoscroll}{$formAtts.slick_slidestoscroll}{/if}" 
				data-rtl="{if isset($IS_RTL) && $IS_RTL}true{else}false{/if}" 
				data-lazyload="{if isset($formAtts.slick_lazyload) && $formAtts.slick_lazyload}true{else}false{/if}" 
				data-lazyloadtype="{if isset($formAtts.slick_lazyload_type) && $formAtts.slick_lazyload_type}{$formAtts.slick_lazyload_type}{/if}" 
				data-responsive="{if isset($formAtts.slick_items_custom) && $formAtts.slick_items_custom}{json_encode($formAtts.slick_items_custom)}{/if}" 
				data-mousewheel="{if isset($formAtts.slick_mousewheel) && $formAtts.slick_mousewheel}true{else}false{/if}" 
				data-fade="{if isset($formAtts.slick_fade) && $formAtts.slick_fade}true{else}false{/if}" 
			>
				{$mproducts=array_chunk($categories,$formAtts.slick_row)}
				{foreach from=$mproducts item=products name=mypLoop}
					<div class="slick-slide {if isset($formAtts.slick_vertical) && $formAtts.slick_vertical}loading-vertical {/if}{if isset($formAtts.array_fake_item.xxl)}loading-xxl-{$formAtts.array_fake_item.xxl} {/if}{if isset($formAtts.array_fake_item.xl)}loading-xl-{$formAtts.array_fake_item.xl} {/if}{if isset($formAtts.array_fake_item.lg)}loading-lg-{$formAtts.array_fake_item.lg} {/if}{if isset($formAtts.array_fake_item.md)}loading-md-{$formAtts.array_fake_item.md} {/if}{if isset($formAtts.array_fake_item.sm)}loading-sm-{$formAtts.array_fake_item.sm} {/if}{if isset($formAtts.array_fake_item.xs)}loading-xs-{$formAtts.array_fake_item.xs} {/if}{if isset($formAtts.array_fake_item.sp)}loading-sp-{$formAtts.array_fake_item.sp}{/if}">
						<div class="item">
							{foreach from=$products item=category name=category}
	                			{include file=$deo_helper->getTplTemplate('DeoCategoryImageCarouselItem.tpl', $formAtts['override_folder'])}
							{/foreach}
						</div>
					</div>
				{/foreach}
			</div>
		</div>
    {else}
        <p class="alert alert-info">{l s='No slide at this time.' mod='deotemplate'}</p>
    {/if}
{/if}