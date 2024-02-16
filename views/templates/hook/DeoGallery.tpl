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
	<div class="block deotemplate {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			<div class="box-title">
		{/if}
			{if isset($formAtts.title) && $formAtts.title}
				<h4 class="title_block">{$formAtts.title nofilter}</h4>
			{/if}
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			</div>
		{/if}
		<div class="block_content">
			{if !empty($formAtts.tags)}
				<div class="filter-tags">
					<ul>
						<li class="filter-item"><a href="javascript:void(0)" data-filter="*" class="is-checked">{l s='All' mod='deotemplate'}{if isset($formAtts.show_count) && $formAtts.show_count}<span class="filter-count"></span>{/if}</a></li>
						{foreach from=$formAtts.tags item=tag}
							<li class="filter-item"><a href="javascript:void(0)" data-filter="{$tag}">{$tag}{if isset($formAtts.show_count) && $formAtts.show_count}<span class="filter-count"></span>{/if}</a></li>
						{/foreach}
					</ul>
				</div>
			{/if}
			{if !empty($formAtts.sliders)}
				<div class="row galleries">
					<div class="gallery-size {if isset($formAtts.class_col_width) && $formAtts.class_col_width}{$formAtts.class_col_width}{/if}"></div>
					{foreach from=$formAtts.sliders item=slider}
						<div class="gallery-item {if isset($slider.class) && $slider.class}{$slider.class}{else}{$formAtts.class_col_width}{/if}" data-tags="{' '|implode:$slider.tags|escape:'html':'UTF-8'}" data-id="{$slider.id|intval}">
							<div class="box-content">
								<div class="image">
									<a href="{$slider.image}" rel="gallery-{$formAtts.form_id}" title="{$slider.title nofilter}" class="link-image">
										{if isset($slider.image) && $slider.image}
											{if isset($formAtts.lazyload) && $formAtts.lazyload}
												<span class="lazyload-wrapper" style="padding-bottom: {$slider.rate_image};">
													<span class="lazyload-icon"></span>
												</span>
												{* <img class="img-fluid {if isset($formAtts.carousel_type) && $formAtts.carousel_type == "owlcarousel"}lazyOwl{/if}" {if isset($formAtts.carousel_type) &&  $formAtts.carousel_type == "owlcarousel"}data-src{elseif isset($formAtts.carousel_type) && $formAtts.carousel_type == "slickcarousel"}data-lazy{/if}="{$slider.image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="{$slider.title nofilter}"/> *}
												<img class="lazyload img-fluid" data-src="{$slider.image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="{$slider.title nofilter}" loading="lazy"/>
											{else}
												<img class="img-fluid" src="{$slider.image}" alt="{$slider.title nofilter}" loading="lazy"/> 
											{/if}
										{/if}
									</a>
								</div>
								<div class="content">
									{if isset($slider.description) && !empty($slider.description)}
										<div class="description">{$slider.description|rtrim nofilter}{* HTML form , no escape necessary *}</div>
									{/if}
									{if $formAtts.display_tags && count($slider.tags)}
										<ul class="tags-gallery">
											{foreach from=$slider.tags item=tag}
												<span class="tag-gallery">#{$tag}</span>
											{/foreach}
										</ul>
									{/if}
								</div>
							</div>
						</div>
					{/foreach}
				</div>
			{else}
				<p class="alert alert-info">{l s='No image at this time.' mod='deotemplate'}</p>
			{/if}
		</div>
	</div>
{/if}