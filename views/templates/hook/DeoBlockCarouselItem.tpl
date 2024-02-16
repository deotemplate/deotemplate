{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="block-carousel-container{if isset($slider.class) && $slider.class} {$slider.class}{/if}" data-id="{$slider.id|intval}">
	{if isset($slider.image) && $slider.image}
		<div class="image">
			{if $slider.link}
				<a title="{$slider.title nofilter}" {if $formAtts.is_open}target="_blank"{/if} href="{$slider.link}" class="link-image">
			{/if}
				{if isset($formAtts.slick_lazyload) && $formAtts.slick_lazyload}
					<span class="lazyload-wrapper" style="padding-bottom: {$slider.rate_image};">
						<span class="lazyload-icon"></span>
					</span>
					{* <img class="img-fluid {if isset($formAtts.carousel_type) && $formAtts.carousel_type == "owlcarousel"}lazyOwl{/if}" {if isset($formAtts.carousel_type) &&  $formAtts.carousel_type == "owlcarousel"}data-src{elseif isset($formAtts.carousel_type) && $formAtts.carousel_type == "slickcarousel"}data-lazy{/if}="{$slider.image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="{$slider.title nofilter}"/> *}
					<img class="img-fluid" data-lazy="{$slider.image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="{$slider.title nofilter}" loading="lazy"/>
				{else}
					<img class="img-fluid" src="{$slider.image}" alt="{$slider.title nofilter}" loading="lazy"/> 
				{/if}
			{if $slider.link}{*full link can not escape*}
				</a>
			{/if}
		</div>
	{/if}
	<div class="content">
		{if isset($slider.title) && !empty($slider.title)}
			<div class="title">{$slider.title nofilter}</div>
		{/if}
		{if isset($slider.sub_title) && !empty($slider.sub_title)}
			<p class="sub-title">{$slider.sub_title nofilter}</p>
		{/if}
		{if isset($slider.description) && !empty($slider.description)}
			<div class="descript">{$slider.description|rtrim nofilter}{* HTML form , no escape necessary *}</div>
		{/if}
	</div>
</div>