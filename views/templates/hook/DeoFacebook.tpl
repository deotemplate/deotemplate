{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="deo-facebook widget-facebook block {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
	<div id="fb-root"></div>
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		<div class="box-title">
	{/if}
		{if isset($formAtts.title) && $formAtts.title}
			<h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
		{/if}
		{if isset($formAtts.sub_title) && $formAtts.sub_title}
			<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
		{/if}
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		</div>
	{/if}
	{if isset($formAtts.page_url) && $formAtts.page_url}
		<div class="fb-page" data-href="{$formAtts.page_url|escape:'html':'UTF-8'}" 
			data-tabs="{if isset($formAtts.tabs) && $formAtts.tabs}{$formAtts.tabs}{/if}" 
			data-height="{if isset($formAtts.height) && $formAtts.height}{$formAtts.height|escape:'html':'UTF-8'}{/if}"
			data-width="{if isset($formAtts.width) && $formAtts.width}{$formAtts.width|escape:'html':'UTF-8'}{/if}"
			data-small-header="{if isset($formAtts.small_header) && $formAtts.small_header}true{else}false{/if}" 
			data-adapt-container-width="{if isset($formAtts.adapt_container_width) && $formAtts.adapt_container_width}true{else}false{/if}"
			data-hide-cover="{if isset($formAtts.hide_cover) && $formAtts.hide_cover}true{else}false{/if}" 
			data-show-facepile="{if isset($formAtts.show_facepile) && $formAtts.show_facepile}true{else}false{/if}">
			<blockquote cite="{$formAtts.page_url|escape:'html':'UTF-8'}" class="fb-xfbml-parse-ignore">
				<a href="{$formAtts.page_url|escape:'html':'UTF-8'}">Facebook</a>
			</blockquote>
		</div>
	{/if}
</div>