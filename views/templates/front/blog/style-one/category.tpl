{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{extends file=$layout}
{block name='content'}
	<section id="main">
		{if isset($category) && $category->id_deoblog_category && $category->active}
			{if isset($no_follow) AND $no_follow}
				{assign var='no_follow_text' value='rel="nofollow"'}
			{else}
				{assign var='no_follow_text' value=''}
			{/if}
			<div class="deo-blog-category {$template} {$category->class_css}">
				<div class="inner">
					{if $configures->show_introduce_category}
						<div class="introduce-category">
							{if $category->image}
								<div class="category-image">
									<img src="{$category->image|escape:'html':'UTF-8'}" class="img-fluid" alt="" />
								</div>
							{/if}	
							<div class="category-info">
								<h1 class="section-title">{$category->title|escape:'html':'UTF-8'}</h1>
								<div class="desc-category">
									{$category->content nofilter}{* HTML form , no escape necessary *}
								</div>
							</div>
						</div> 
					{/if}
					{if isset($filter.type)}
						{if $filter.type=='tag'}
							<p class="filter-by">{l s='Filter by tag' mod='deotemplate'} "<span>{$filter.tag|escape:'html':'UTF-8'}</span>"</p>
						{/if}
					{/if}
					{include file="module:deotemplate/views/templates/front/blog/{$template}/_partials/_list_blog.tpl"}
				</div>
			</div>
		{else}
			<div class="alert alert-warning">{l s='Sorry, We are updating data, please come back later!!!!' mod='deotemplate'}</div>
		{/if}
	</section>
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
	</div>
{/block}