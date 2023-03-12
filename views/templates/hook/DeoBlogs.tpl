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
	<div id="blog-{$formAtts.form_id}" class="block deo-blog-builder {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
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
		<div class="block_content">	
			{if !empty($products)}
				{* {if $formAtts.carousel_type == "slickcarousel"}
					{include file=$deo_helper->getTplTemplate('DeoBlogsSlickCarousel.tpl', $formAtts['override_folder'])}
				{elseif $formAtts.carousel_type == 'boostrap'}
					{include file=$deo_helper->getTplTemplate('DeoBlogsBootstrapCarousel.tpl', $formAtts['override_folder'])}
				{elseif $formAtts.carousel_type == 'owlcarousel'}
					{include file=$deo_helper->getTplTemplate('DeoBlogsOwlCarousel.tpl', $formAtts['override_folder'])}
				{/if} *}
				{include file=$deo_helper->getTplTemplate('DeoBlogsSlickCarousel.tpl', $formAtts['override_folder'])}
			{else}
				<p class="alert alert-info">{l s='No blog at this time.' mod='deotemplate'}</p>	
			{/if}
			{if isset($formAtts.show_view_all) && $formAtts.show_view_all}
				<div class="blog-viewall">
					<a class="btn btn-default" href="{$formAtts.helper->getFontBlogLink()}" title="{l s='View all' mod='deotemplate'}">{l s='View all' mod='deotemplate'}</a>
				</div>
			{/if}
		</div>
	</div>
	<div class="hidden-xl-down hidden-xl-up datetime-translate">
		{l s='Sunday' mod='deotemplate'}
		{l s='Monday' mod='deotemplate'}
		{l s='Tuesday' mod='deotemplate'}
		{l s='Wednesday' mod='deotemplate'}
		{l s='Thursday' mod='deotemplate'}
		{l s='Friday' mod='deotemplate'}
		{l s='Saturday' mod='deotemplate'}
		
		{l s='January' mod='deotemplate'}
		{l s='February' mod='deotemplate'}
		{l s='March' mod='deotemplate'}
		{l s='April' mod='deotemplate'}
		{l s='May' mod='deotemplate'}
		{l s='June' mod='deotemplate'}
		{l s='July' mod='deotemplate'}
		{l s='August' mod='deotemplate'}
		{l s='September' mod='deotemplate'}
		{l s='October' mod='deotemplate'}
		{l s='November' mod='deotemplate'}
		{l s='December' mod='deotemplate'}
		
		{l s='st' mod='deotemplate'}
		{l s='nd' mod='deotemplate'}
		{l s='rd' mod='deotemplate'}
		{l s='th' mod='deotemplate'}
	</div>
{/if}