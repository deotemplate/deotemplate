{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{assign var='images' value=$product.images}
{if isset($images) && count($images) >= 2}
	<!-- thumbnails -->
	<div class="views_block clearfix {if isset($images) && count($images) < 2}hidden{/if}">
		<div class="list-thumbs">
			{foreach from=$images item=image name=thumbnails}
				{assign var=imageIds value="`$product.id_product`-`$image.id_image`"}
				<div id="thumbnail_{$image.id_image|intval}">
					<a href="{$link->getImageLink($product.link_rewrite, $imageIds, 'large_default')}" data-idproduct="{$product.id_product|intval}" rel="other-views" class="link-img image-hover thickbox-ajax-{$product.id_product|intval}{if $smarty.foreach.thumbnails.first} shown{/if}" title="{if $image.legend}{$image.legend|htmlspecialchars}{else}{$image.name|htmlspecialchars}{/if}">
						{if $deo_lazyload}
							<span class="lazyload-wrapper" style="padding-bottom: {$rate_images.cart_default};">
								<span class="lazyload-icon"></span>
							</span>
							<img id="thumb_{$image.id_image|intval}" 
								src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" 
								data-lazy="{$link->getImageLink($product.link_rewrite, $imageIds, 'cart_default')}"
								alt="{if $image.legend}{$image.legend|htmlspecialchars}{else}{$image.name|htmlspecialchars}{/if}"
								rel="{$link->getImageLink($product.link_rewrite, $imageIds, 'home_default')}" 
								data-full-size-image-url = "{$link->getImageLink($product.link_rewrite, $imageIds, 'large_default')}" 
								class="img-fluid"
							/>
						{else}
							<img id="thumb_{$image.id_image|intval}" 
								src="{$link->getImageLink($product.link_rewrite, $imageIds, 'cart_default')}"
								alt="{if $image.legend}{$image.legend|htmlspecialchars}{else}{$image.name|htmlspecialchars}{/if}"
								rel="{$link->getImageLink($product.link_rewrite, $imageIds, 'home_default')}" 
								data-full-size-image-url = "{$link->getImageLink($product.link_rewrite, $imageIds, 'large_default')}" 
								class="img-fluid" 
								loading="lazy"
							/>
						{/if}
					</a>
				</div>
			{/foreach}
		</div>
	</div>
{/if}
