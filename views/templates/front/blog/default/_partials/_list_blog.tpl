{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if count($blogs)}
	<div class="row list-category-blogs">  
		{foreach from=$blogs item=blog}
			<div class="col-blog-item {$configures->col_class}">
				{include file="module:deotemplate/views/templates/front/blog/{$template}/_partials/_item_blog.tpl"}
			</div>	
		{/foreach}
	</div>
	<div class="top-pagination-content clearfix bottom-line">
		{include file="module:deotemplate/views/templates/front/blog/{$template}/_partials/_pagination.tpl"}
	</div>
{else}
	<div class="alert alert-warning">{l s='Sorry, We are updating data, please come back later!!!!' mod='deotemplate'}</div>
{/if}  