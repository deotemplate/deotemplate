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
	<div class="block products_block {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			<div class="box-title">
		{/if}
			{if isset($formAtts.title)&&!empty($formAtts.title)}
				<h4 class="title_block">{$formAtts.title nofilter}</h4>
			{/if}
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			</div>
		{/if}
		<div class="block_content">	
			{if !empty($products)}
				{* {if $formAtts.carousel_type == "slickcarousel"}
					{include file=$deo_helper->getTplTemplate('DeoProductSlickCarousel.tpl', $formAtts['override_folder'])}
				{elseif $formAtts.carousel_type == "boostrap"}
					{include file=$deo_helper->getTplTemplate('DeoProductBootstrapCarousel.tpl', $formAtts['override_folder'])}
				{elseif $formAtts.carousel_type == "owlcarousel"}
					{include file=$deo_helper->getTplTemplate('DeoProductOwlCarousel.tpl', $formAtts['override_folder'])}
				{/if} *}
				{include file=$deo_helper->getTplTemplate('DeoProductSlickCarousel.tpl', $formAtts['override_folder'])}
			{else}
				<p class="alert alert-info">{l s='No products at this time.' mod='deotemplate'}</p>	
			{/if}
		</div>
	</div>
{/if}