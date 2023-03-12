{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="cate_content">
	{if isset($category.image)}
		<div class="cover-img">
			<a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)}"  title="{$category.name}">
				{if isset($category.rate_image) && isset($formAtts.lazyload) && $formAtts.lazyload}
					<span class="lazyload-wrapper" style="padding-bottom: {$category.rate_image};">
						<span class="lazyload-icon"></span>
					</span>
					<img class="img-fluid lazyload" data-src='{$category["image"]}' src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt='{$category["name"]}'/>
				{else}
					<img class="img-fluid" src='{$category["image"]}' alt='{$category["name"]}' loading="lazy"/>
				{/if}
			</a>
		</div>
	{/if}
	<div class="cate-meta">
		<div class="box-cate">
			<h3 class="cate-name"><a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)}" title="{$category.name}">{$category.name}</a></h3>
			{if isset($formAtts.quantity) && $formAtts.quantity}
				<span data-id="{$category.id_category}" class="deo-qty-category deo-cat-{$category.id_category}" data-str="{l s=' items' mod='deotemplate'}"></span>
			{/if}
			{if isset($formAtts.description) && $formAtts.description}
				<div class="description">{$category.description|truncate:120 nofilter}</div>
			{/if}
		</div>
	</div>
</div>