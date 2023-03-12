{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="block-slide-container" data-id-slide="{$slider.id|intval}">
	<div class="slide-image">
		{if $slider.link_slide}
			<a href="{$slider.link_slide}" class="link-slide">
		{/if}
			{if isset($slider.image) && !empty($slider.image)}
				{if isset($formAtts.slick_lazyload) && $formAtts.slick_lazyload}
					<span class="lazyload-wrapper" style="padding-bottom: {$slider.rate_image};">
						<span class="lazyload-icon"></span>
					</span>
					{* <img class="img-fluid {if isset($formAtts.carousel_type) && $formAtts.carousel_type == "owlcarousel"}lazyOwl{elseif isset($formAtts.carousel_type) && $formAtts.carousel_type == "boostrap"}lazyload{/if}" {if isset($formAtts.carousel_type) && $formAtts.carousel_type == "slickcarousel"}data-lazy{else}
						data-src{/if}="{$slider.image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt=""/> *}
					<img class="img-fluid" data-lazy="{$slider.image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt=""/>
				{else}
					<img class="img-fluid" src="{$slider.image}" alt="" loading="lazy"/> 
				{/if}
			{/if}
		{if $slider.link_slide}
			</a>
		{/if}
	</div>
	<div class="slide-text {$slider.align_text}" style="top: {(isset($slider.top) && $slider.top) ? $slider.top : 'auto'};{if $slider.align_text == 'right-text-slide'}right{else}left{/if}:{(isset($slider.left) && $slider.left) ? $slider.left : 'auto'};">
		{if isset($slider.first_text) && !empty($slider.first_text)}
			<div class="first-text text-slide animate-wait {(isset($slider.class_first_text) && $slider.class_first_text) ? $slider.class_first_text : ''}" data-effect="{$slider.effect_first_text}" data-delay="{$slider.delay_first_text}">{$slider.first_text nofilter}</div>
		{/if}
		{if isset($slider.second_text) && !empty($slider.second_text)}
			<div class="second-text text-slide animate-wait {(isset($slider.class_second_text) && $slider.class_second_text) ? $slider.class_second_text : ''}" data-effect="{$slider.effect_second_text}" data-delay="{$slider.delay_second_text}">{$slider.second_text nofilter}</div>
		{/if}
		{if isset($slider.third_text) && !empty($slider.third_text)}
			<div class="third-text text-slide animate-wait {(isset($slider.class_third_text) && $slider.class_third_text) ? $slider.class_third_text : ''}" data-effect="{$slider.effect_third_text}" data-delay="{$slider.delay_third_text}">{$slider.third_text nofilter}</div>
		{/if}
		{if isset($slider.text_btn) && !empty($slider.text_btn)}
			<a href="{(isset($slider.link_btn) && $slider.link_btn) ? $slider.link_btn : 'javascript:void(0)'}" class="btn btn-outline btn-slideshow text-slide animate-wait {(isset($slider.class_link_btn) && $slider.class_link_btn) ? $slider.class_link_btn : ''}" data-effect="{$slider.effect_link_btn}" data-delay="{$slider.delay_btn_slide}">{$slider.text_btn nofilter}</a>
		{/if}
	</div>
</div>