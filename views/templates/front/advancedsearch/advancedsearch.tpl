{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{extends file=$layout}

{block name='content'}
	{capture name=path}{l s='Search' mod='deotemplate'}{/capture}
	<h1 {if isset($instant_search) && $instant_search}id="instant_search_results"{/if} class="page-heading {if !isset($instant_search) || (isset($instant_search) && !$instant_search)} product-listing{/if}">
		{l s='Search' mod='deotemplate'}
		{if $nbProducts > 0}
			<span class="lighter">{if isset($advanced_search_query) && $advanced_search_query}{$advanced_search_query|escape:'html':'UTF-8'}{elseif $search_tag}{$search_tag|escape:'html':'UTF-8'}{elseif $ref}{$ref|escape:'html':'UTF-8'}{/if}</span>
		{/if}
		{if isset($instant_search) && $instant_search}
			<a href="#" class="close">
				{l s='Return to the previous page' mod='deotemplate'}
			</a>
		{else}
			<span class="heading-counter">
				{if $nbProducts == 1}{l s='%d result has been found.' sprintf=[$nbProducts|intval] mod='deotemplate'}{else}{l s='%d results have been found.' sprintf=[$nbProducts|intval] mod='deotemplate'}{/if}
			</span>
		{/if}
	</h1>
	
	{if !$nbProducts}
		<p class="alert alert-warning">
			{if isset($advanced_search_query) && $advanced_search_query}
				{l s='No results were found for your search' mod='deotemplate'} "{if isset($advanced_search_query)}{$advanced_search_query|escape:'html':'UTF-8'}{/if}"
			{elseif isset($search_tag) && $search_tag}
				{l s='No results were found for your search' mod='deotemplate'} "{$search_tag|escape:'html':'UTF-8'}"
			{else}
				{l s='Please enter a search keyword' mod='deotemplate'}
			{/if}
		</p>
	{else}
		{if isset($instant_search) && $instant_search}
			<p class="alert alert-info">
				{if $nbProducts == 1}{l s='%d result has been found.' sprintf=[$nbProducts|intval] mod='deotemplate'}{else}{l s='%d results have been found.' sprintf=[$nbProducts|intval] mod='deotemplate'}{/if}
			</p>
		{/if}
		
		<section id="products">
			<div id="">
			  {block name='product_list_top'}
				{include file='catalog/_partials/products-top.tpl' listing=$search_products}
			  {/block}
			</div>

			{block name='product_list_active_filters'}
			  <div id="" class="hidden-sm-down">
				{$search_products.rendered_active_filters nofilter}{* HTML form , no escape necessary *}
			  </div>
			{/block}
			<div id="">
				{block name='product_list'}
					{include file='catalog/_partials/products.tpl' listing=$search_products}
				{/block}
			</div>
		</section>
		
	{/if}
{/block}
