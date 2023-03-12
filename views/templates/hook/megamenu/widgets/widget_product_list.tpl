{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{assign var="path_widget_base" value="`$path_widget_base`widget.tpl"}
{extends file=$path_widget_base}

{block name='widget-content'}
	{if isset($products) && !empty($products)}
		<div class="product-block">
			{foreach from=$products item=product name=homeFeaturedProducts}
				<div class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
					<div class="thumbnail-container clearfix">
						<div class="product-image">
							{block name='product_thumbnail'}
								{if $product.cover}
									<a href="{$product.url}" class="thumbnail product-thumbnail">
										{if $deo_lazyload && $backoffice == 0}
											<span class="lazyload-wrapper" style="padding-bottom: {$rate_images.small_default};">
												<span class="lazyload-icon"></span>
											</span>
											<img
												class="img-fluid lazyload"
												data-src = "{$product.cover.bySize.small_default.url}"
												src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
												alt = "{$product.cover.legend}"
												data-full-size-image-url = "{$product.cover.large.url}"
											>
										{else}
											<img
												class="img-fluid"
												src = "{$product.cover.bySize.small_default.url}"
												alt = "{$product.cover.legend}"
												data-full-size-image-url = "{$product.cover.large.url}" 
												loading="lazy"
											>
										{/if}
									</a>
								{else}
									<a href="{$product.url}" class="thumbnail product-thumbnail">
										<img
											class="img-fluid"
											src = "{$urls.no_picture_image.bySize.small_default.url}"
											alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name}{/if}"
										>
									</a>
								{/if} 
							{/block}
						</div>
						<div class="product-meta">
							{block name='product_name'}
								<h4 class="h3 product-title" itemprop="name"><a href="{$product.url}">{$product.name}</a></h4>
							{/block}

							{block name='product_price_and_shipping'}
								{if $product.show_price}
									<div class="product-price-and-shipping{if $product.has_discount} has_discount{/if}">
										{if $product.has_discount}
											{hook h='displayProductPriceBlock' product=$product type="old_price"}
											<span class="regular-price">{$product.regular_price}</span>
											{if $product.discount_type === 'percentage'}
												<span class="discount-percentage">{$product.discount_percentage}</span>
											{/if}
										{/if}
										{hook h='displayProductPriceBlock' product=$product type="before_price"}

										<span class="price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
											<span itemprop="priceCurrency" content="{$currency.iso_code}"></span><span itemprop="price" content="{$product.price_amount}">{$product.price}</span>
										</span>

										{hook h='displayProductPriceBlock' product=$product type='unit_price'}

										{hook h='displayProductPriceBlock' product=$product type='weight'}
									</div>
								{/if}
							{/block}
						</div>
					</div>
				</div>			
			{/foreach}
		</div>
	{else}
		<p class="alert alert-info">{l s='No products found.' mod='deotemplate'}</p>
	{/if}
{/block}