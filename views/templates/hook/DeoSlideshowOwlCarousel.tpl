{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="owl-row">
	<div class="carousel-slider deo-carousel owl-carousel owl-theme deo-carousel-loading {if isset($formAtts.showloading) && $formAtts.showloading}show-icon-loading{/if}" id="{$formAtts.form_id}" 
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
	>
		{$Num=array_chunk($formAtts.slides, $formAtts.itempercolumn)}
		{foreach from=$Num item=sliders name=manuloop} 
			<div class="owl-item {if isset($formAtts.array_fake_item.xxl)}loading-xxl-{$formAtts.array_fake_item.xxl} {/if}{if isset($formAtts.array_fake_item.xl)}loading-xl-{$formAtts.array_fake_item.xl} {/if}{if isset($formAtts.array_fake_item.lg)}loading-lg-{$formAtts.array_fake_item.lg} {/if}{if isset($formAtts.array_fake_item.md)}loading-md-{$formAtts.array_fake_item.md} {/if}{if isset($formAtts.array_fake_item.sm)}loading-sm-{$formAtts.array_fake_item.sm} {/if}{if isset($formAtts.array_fake_item.xs)}loading-xs-{$formAtts.array_fake_item.xs} {/if}{if isset($formAtts.array_fake_item.sp)}loading-sp-{$formAtts.array_fake_item.sp}{/if}">
				<div class="item">
					{foreach from=$sliders item=slider}
						{include file=$deo_helper->getTplTemplate('DeoSlideshowItem.tpl', $formAtts['override_folder'])}
					{/foreach}
				</div>
			</div>
		{/foreach}
	</div>
</div>