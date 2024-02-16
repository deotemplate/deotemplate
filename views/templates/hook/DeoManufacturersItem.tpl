{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="manufacturer-container manufacturer-block" itemscope itemtype="https://schema.org/Brand">
	<div class="manufacturer-image-container image">
		<a title="{$manu.name}" href="{$link_deo->getmanufacturerLink($manu.id_manufacturer, $manu.link_rewrite)}" itemprop="url">
			{if isset($formAtts.slick_lazyload) && $formAtts.slick_lazyload}
				<span class="lazyload-wrapper" style="padding-bottom: {$formAtts.rate_image};">
					<span class="lazyload-icon"></span>
				</span>
				{* <img class="img-fluid {if isset($formAtts.carousel_type) && $formAtts.carousel_type == "owlcarousel"}lazyOwl{/if}" {if isset($formAtts.carousel_type) &&  $formAtts.carousel_type == "owlcarousel"}data-src{elseif isset($formAtts.carousel_type) && $formAtts.carousel_type == "slickcarousel"}data-lazy{/if}="{$img_manu_dir}{$manu.id_manufacturer|intval}-{$image_type}.jpg" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="{$manu.name}" itemprop="image" /> *}
				<img class="img-fluid" data-lazy="{$img_manu_dir}{$manu.id_manufacturer|intval}-{$image_type}.jpg" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="{$manu.name}" itemprop="image" loading="lazy"/>
			{else}
				<img class="img-fluid" src="{$img_manu_dir}{$manu.id_manufacturer|intval}-{$image_type}.jpg" alt="{$manu.name}" itemprop="image" loading="lazy"/>
			{/if}
		</a>
	</div>
	<div class="manufacturer-name">
		<h3 class="name">
			<a title="{$manu.name}" href="{$link_deo->getmanufacturerLink($manu.id_manufacturer, $manu.link_rewrite)}" itemprop="url">{$manu.name}</a>
		</h3>
	</div>
</div>