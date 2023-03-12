{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{function name="categories" nodes=[] depth=0}
	{strip}
		{if $nodes|count}
			{foreach from=$nodes item=node}         
				<a href="javascript:void(0);" data-cate-id="{$node.id_category|escape:'htmlall':'UTF-8'|stripslashes}" data-cate-name="{$node.name}" class="cate-item cate-level-{$node.level_depth}{if isset($formAtts.selectedCate) && $node.id_category eq $formAtts.selectedCate} active{/if}" >{if $node.level_depth > 1}{'&nbsp;&nbsp;'|str_repeat:($node.level_depth) nofilter}{/if} {$node.name}</a>           
				{categories nodes=$node.children depth=$depth+1}           
			{/foreach}
		{/if}
	{/strip}
{/function}

<!-- Block search module -->
<div id="form-{$formAtts.form_id}" class="deo-search-advanced block exclusive{if isset($formAtts.searchcategory) && $formAtts.searchcategory} search-by-category{/if} {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		<div class="box-title">
	{/if}
		{if isset($formAtts.title) && $formAtts.title}
			<h4 class="title_block">{$formAtts.title|rtrim}</h4>
		{/if}
		{if isset($formAtts.sub_title) && $formAtts.sub_title}
			<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
		{/if}
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		</div>
	{/if}
	<a href="javascript:void(0)" class="popup-title" rel="nofollow">
		<i class="deo-custom-icons search"></i>
		<span class="name-simple">{l s='Search' mod='deotemplate'}</span>
	</a>
	<form method="get" action="{$link->getModuleLink('deotemplate', 'advancedsearch', array(), Tools::usingSecureMode())}" class="deo-search-advanced-top-box popup-content">
		<div class="block_content clearfix advanced-search-content">	
			<input type="hidden" name="token" class="token" value="{$formAtts.token|escape:'htmlall':'UTF-8'|stripslashes}">
			{if isset($formAtts.searchcategory) && $formAtts.searchcategory}	
				<div class="list-cate-wrapper"{if isset($formAtts.searchcategory) && !$formAtts.searchcategory} style="display: none"{/if}>
					<input class="deo-advanced-search-cate-id" name="cate_id" value="{if isset($formAtts.selectedCate)}{$formAtts.selectedCate}{/if}" type="hidden">
					<a href="javascript:void(0)" id="{$formAtts.form_id}-dropdownListCate" class="select-list-cate select-title" rel="nofollow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span>
							{if isset($formAtts.selectedCateName) && $formAtts.selectedCateName != ''}
								{$formAtts.selectedCateName}
							{else}
								{l s='All Categories' mod='deotemplate'}
							{/if}
						</span>
						<i class="deo-custom-icons"></i>
					</a>
					<div class="list-cate dropdown-menu" aria-labelledby="{$formAtts.form_id}-dropdownListCate">
						<a href="javascript:void(0);" data-cate-id="" data-cate-name="{l s='All Categories' mod='deotemplate'}" class="cate-item{if isset($formAtts.selectedCateName) && $formAtts.selectedCateName == ''} active{/if}" >{l s='All Categories' mod='deotemplate'}</a>
						<a href="javascript:void(0);" data-cate-id="{$formAtts.cates.id_category|escape:'htmlall':'UTF-8'|stripslashes}" data-cate-name="{$formAtts.cates.name}" class="cate-item cate-level-{$formAtts.cates.level_depth}{if isset($formAtts.selectedCate) && $formAtts.cates.id_category eq $formAtts.selectedCate} active{/if}">
							{if $formAtts.cates.level_depth > 1}{'&nbsp;&nbsp;'|str_repeat:($formAtts.cates.level_depth) nofilter}{/if} {$formAtts.cates.name}
						</a>
						{categories nodes=$formAtts.cates.children}
					</div>
				</div>
			{else}
				<input class="deo-advanced-search-cate-id" name="cate_id" value="" type="hidden">
			{/if}

			<div class="deo-advanced-search-input">
				<div class="deo-advanced-search-loading"></div>
				<input class="advanced_search_query deo-advanced-search-query form-control grey" type="text" name="advanced_search_query"  
					value="{if isset($formAtts.advanced_search_query) && $formAtts.advanced_search_query != ''}{$formAtts.advanced_search_query|escape:'htmlall':'UTF-8'|stripslashes}{/if}" 
					{* data-advanced-search-url="{if isset($formAtts.advanced_search_url) && $formAtts.advanced_search_url}{$formAtts.advanced_search_url}{/if}"  *}
					data-ajax-search="{if isset($formAtts.ajaxsearch) && $formAtts.ajaxsearch}{$formAtts.ajaxsearch}{/if}" 
					data-number-product-display="{if isset($formAtts.limitajaxsearch) && $formAtts.limitajaxsearch}{$formAtts.limitajaxsearch}{/if}" 
					data-ajax-search="{(isset($formAtts.limitajaxsearch) && $formAtts.limitajaxsearch) ? true : false}" 
					data-show-image="{(isset($formAtts.showimage) && $formAtts.showimage) ? true : false}" 
					data-show-price="{(isset($formAtts.showprice) && $formAtts.showprice) ? true : false}" 
					data-show-stock="{(isset($formAtts.showstock) && $formAtts.showstock) ? true : false}" 
					{* data-token="{$formAtts.token|escape:'htmlall':'UTF-8'|stripslashes}"  *}
					data-text-not-found="{l s='No products found' mod='deotemplate'}"
					placeholder="{l s='Search our catalog' mod='deotemplate'}"/>
			</div>
			<button type="submit" class="deo-advanced-search-top-button" class="btn btn-default button button-small">
				<i class="icon-search"></i>
				<span>{l s='Search' mod='deotemplate'}</span>
			</button> 
		</div>
	</form>
</div>