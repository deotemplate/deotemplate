{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}


{* {assign var="initialslide" value=0}
{foreach from=$product.images item=item name=obj}
	{if $item.id_image == $product.cover.id_image}
	    {$initialslide = $smarty.foreach.obj.index}
	    {break}
	{/if}
{/foreach} *}

{if isset($more_product_img) && $more_product_img && isset($product.images) && count($product.images) >= 2}
	<div class="deo-more-product-img pro" data-idproduct="{$product.id}">
		<!-- thumbnails -->
		<div class="views_block slick-row">
			<div class="list-thumbs slick-slider deo-carousel deo-carousel-loading"
				data-centermode="{$centermode}" 
				data-dots="{$dots}" 
				data-adaptiveheight="false" 
				data-infinite="false" 
				data-vertical="{$vertical}" 
				data-verticalswiping="{$vertical}" 
				data-autoplay="false" 
				data-autoplayspeed="false" 
				data-pauseonhover="false" 
				data-arrows="true" 
				data-slidestoshow="{$slidestoshow}" 
				data-slidestoscroll="1"
				data-rtl="{if isset($IS_RTL) && $IS_RTL}true{else}false{/if}" 
				data-lazyload="{$lazyload}" 
				data-lazyloadtype="ondemand" 
				data-responsive="{$responsive}" 
				data-mousewheel="{$mousewheel}" 
				data-fade="{$fade}" 
				data-col_loading="{$col_loading}" 
				data-deo_size="{$deo_size}"
				{* data-initialslide="{$initialslide}" *}
			>
				{foreach from=$product.images item=image name=thumbnails}
					<div id="thumbnail_{$image.id_image|intval}" class="thumbnail-image slick-slide{$col_loading}{* {if ($image.cover)} initial-slide{/if} *}">
						<a href="{$image.bySize.large_default.url}" data-idproduct="{$product.id_product|intval}" rel="other-views" class="link-img image-hover thickbox-ajax-{$product.id_product|intval} thickbox-ajax{if ($image.cover)} shown{/if}" title="{if !empty($image.legend)}{$image.legend}{else}{$product.name}{/if}">
							{if $deo_lazyload}
								<span class="lazyload-wrapper" style="padding-bottom: {DeoHelper::calculateRateImage($image.bySize[$deo_size].width,$image.bySize[$deo_size].height)};">
									<span class="lazyload-icon"></span>
								</span>
								<img id="thumb_{$image.id_image|intval}" 
									src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" 
									data-lazy="{$image.bySize[$deo_size].url}"
									alt="{if !empty($image.legend)}{$image.legend}{else}{$product.name}{/if}"
									rel="{$image.bySize.home_default.url}" 
									data-full-size-image-url = "{$image.bySize.large_default.url}" 
									class="img-fluid"
								/>
							{else}
								<img id="thumb_{$image.id_image|intval}" 
									src="{$image.bySize[$deo_size].url}"
									alt="{if !empty($image.legend)}{$image.legend}{else}{$product.name}{/if}"
									rel="{$image.bySize.home_default.url}" 
									data-full-size-image-url = "{$image.bySize.large_default.url}" 
									class="img-fluid" 
									loading="lazy"
								/>
							{/if}
						</a>
					</div>
				{/foreach}
			</div>
		</div>
	</div>
{/if}
