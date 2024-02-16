{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}


{function name=category level=0}
	<ul class="level{$level|intval} cate-items">
		{foreach $data as $category}
			<li class="cate_{$category.id_category|intval} cate-item{if isset($category.deo_count) && $category.deo_count >= $formAtts.limit} hidden-cate-item{/if}" {if isset($category.deo_count) && $category.deo_count >= $formAtts.limit}style="display: none;"{/if}>
				<div class="cate_content">
					{if isset($category.children) && is_array($category.children)}
						{if isset($category.image)}
							<div class="cover-img">
								<a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)}" title="{$category.name}">
									{if isset($category.rate_image) && isset($formAtts.lazyload) && $formAtts.lazyload}
										<span class="lazyload-wrapper" style="padding-bottom: {$category.rate_image};">
											<span class="lazyload-icon"></span>
										</span>
										<img class="img-fluid lazyload" data-src='{$category["image"]}' src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt='{$category["name"]}' loading="lazy"/>
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
							{if isset($formAtts.disable_html_tree_structure) && $formAtts.disable_html_tree_structure}
							{else}
								{category data=$category.children level=$level+1}
							{/if}
						</div>
					{else}
						{if isset($category.image)}
							<div class="cover-img">
								<a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)}"  title="{$category.name}">
									{if isset($category.rate_image) && isset($formAtts.lazyload) && $formAtts.lazyload}
										<span class="lazyload-wrapper" style="padding-bottom: {$category.rate_image};">
											<span class="lazyload-icon"></span>
										</span>
										<img class="img-fluid lazyload" data-src='{$category["image"]}' src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt='{$category["name"]}' loading="lazy"/>
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
					{/if}
				</div>
			</li>
		{/foreach}
	</ul>
{/function}

{if isset($categories)}
	<div class="deo-category-image block {if isset($formAtts.class)}{$formAtts.class}{/if} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}{if isset($formAtts.description) && $formAtts.description} show-description{/if}{if isset($disable_html_tree_structure) && $disable_html_tree_structure} disable-html-tree-structure{/if}" 
		data-limit="{$formAtts.limit|intval}" 
		data-cate_depth="{$formAtts.cate_depth|intval}" 
		data-viewall="{(isset($formAtts.viewall) && $formAtts.viewall) ? $formAtts.viewall : 0}" 
		data-link_viewall="{(isset($formAtts.link_viewall) && $formAtts.link_viewall) ? $formAtts.link_viewall : 0}"  
	>
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
            <div class="box-title">
        {/if}
			{if isset($formAtts.title) && !empty($formAtts.title)}
				<h4 class="title_block">{$formAtts.title}</h4>
			{/if}
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
	        </div>
	    {/if}
		<div class="block_content">
			{if isset($formAtts.carousel_type) && $formAtts.carousel_type == 'slickcarousel'}
				{if !empty($categories)}
					{include file=$deo_helper->getTplTemplate('DeoCategoryImageSlickCarousel.tpl', $formAtts['override_folder'])}
				{else}
					<p class="alert alert-info">{l s='No slide at this time.' mod='deotemplate'}</p>
				{/if}
			{else}	  		 
				{foreach from=$categories key=key item=cate}
					{category data=$cate}
				{/foreach}
				{if $formAtts.limit > 1 && isset($formAtts.viewall) && $formAtts.viewall}
					<div class="view_all_wapper" {if $total <= $formAtts.limit}style="display: none;"{/if}>
						<div class="view_all">
							<a class="btn{if isset($formAtts.link_viewall) && $formAtts.link_viewall}{else} active-js-view-all{/if}" href="{if isset($formAtts.link_viewall) && $formAtts.link_viewall}{$formAtts.link_viewall}{else}javascript:void(0){/if}" data-hide-less="{l s='Hide Less' mod='deotemplate'}" data-view-more="{if isset($formAtts.text_link_viewall) && $formAtts.text_link_viewall}{$formAtts.text_link_viewall}{else}{l s='View more' mod='deotemplate'}{/if}">{if isset($formAtts.text_link_viewall) && $formAtts.text_link_viewall}{$formAtts.text_link_viewall}{else}{l s='View more' mod='deotemplate'}{/if}</a>
						</div>
					</div> 
				{/if}
			{/if}
		</div>
	</div>
{/if}