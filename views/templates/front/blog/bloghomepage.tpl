{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{extends file=$layout}

{block name='content'}
	<section id="main">
		<div id="blog-listing" class="blogs-container box">
			{if isset($filter.type)}
				<h1 class="section-title blog-lastest-title">{l s='Filter Blogs' mod='deotemplate'}</h1>
				{if $filter.type=='tag'}
					<p class="filter-by">{l s='By tag' mod='deotemplate'} "<span>{$filter.tag|escape:'html':'UTF-8'}</span>"</p>
				{elseif $filter.type=='author'}
					{if isset($filter.id_employee)}
						<p class="filter-by">{l s='By author' mod='deotemplate'} "<span>{$filter.employee->firstname|escape:'html':'UTF-8'} {$filter.employee->lastname|escape:'html':'UTF-8'}</span>"</p>
					{else}
						<p class="filter-by">{l s='By author' mod='deotemplate'} "<span>{$filter.author_name|escape:'html':'UTF-8'}</span>"</p>
					{/if}
				{/if}
			{else}
				<h1 class="section-title blog-lastest-title">{l s='Lastest Blogs' mod='deotemplate'}</h1>
			{/if}
			<div class="inner">
				{if isset($filter.type)}
					{include file="module:deotemplate/views/templates/front/blog/{$template}/_partials/_list_blog.tpl"}
				{else}
					{include file="module:deotemplate/views/templates/front/blog/{$template}/_partials/_list-bloghomepage.tpl"}
				{/if}
			</div>
		</div>
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