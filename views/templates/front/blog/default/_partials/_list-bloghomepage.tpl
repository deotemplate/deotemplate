{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if count($categories)}
	<div class="leading-blog {$template} row">
		{foreach from=$categories item=category}
			<div class="col-xxl-6 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 col-sp-12">
				<div class="leading-item">
					<div class="left-block">
						<div class="blog-category-image">
							<a href="{$category.category_link}" title="{$category.title}">
								{if isset($lazyload) && $lazyload}
									<span class="lazyload-wrapper" style="padding-bottom: {$category.rate_image};">
					                    <span class="lazyload-icon"></span>
					                </span>
					                <img data-src="{$category.image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="img-fluid lazyload" title="{$category.title}" alt="{$category.title}"/>
								{else}
									<img src="{$category.image}" title="{$category.title}" alt="{$category.title}" class="img-fluid"/>
								{/if}
							</a>
						</div>
					</div>
					<div class="right-block">
						<div class="heading">
							<h3 class="title"><a href="{$category.category_link}" title="{$category.title}">{$category.title}</a></h3>
						</div>
						{if count($category['blogs'])}
							<div class="blog-posts">
								{foreach from=$category['blogs'] item=blog}
									{include file="module:deotemplate/views/templates/front/blog/{$template}/_partials/_item-bloghomepage.tpl"}
								{/foreach}
							</div>
						{/if}  
					</div>
					{include file="module:deotemplate/views/templates/front/blog/{$template}/_partials/_tags.tpl" tags=$category.tags}
				</div>
			</div>
		{/foreach}
	</div>
{else}
	<div class="alert alert-warning">{l s='Sorry, We are updating blog. Please come back later!!!!' mod='deotemplate'}</div>
{/if}  


