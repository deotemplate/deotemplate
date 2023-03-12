{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{extends file='customer/page-sidebar.tpl'}

{block name='page_title_sidebar'}
  <h1 class="page-title">{l s='Wishlist' mod='deotemplate'} "{$current_wishlist.name}"</h1>
{/block}

{block name='page_right_content'}
	<section id="main">
		<div id="view_wishlist">
			{if isset($current_wishlist)}
				{if $wishlists}
					<p>
						{l s='Other wishlists of ' mod='deotemplate'}{$current_wishlist.firstname} {$current_wishlist.lastname} :
						{foreach from=$wishlists item=wishlist_item name=i}				
							<a href="{$view_wishlist_url}{if $is_rewrite_active}?{else}&{/if}token={$wishlist_item.token}" title="{$wishlist_item.name}" rel="nofollow">{$wishlist_item.name}</a>
							{if !$smarty.foreach.i.last}
								/
							{/if}				
						{/foreach}
					</p>
				{/if}
				<section id="products">
					<div class="deo-wishlist-product products row">
						{if $products && count($products) > 0}
							{foreach from=$products item=product_item name=for_products}
								{assign var='product' value=$product_item.product_info}
								{assign var='wishlist' value=$product_item.wishlist_info}
								<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 product-miniature js-product-miniature deo-wishlist-product-item deo-wishlist-product-item-{$wishlist.id_wishlist_product} product-{$product.id_product}" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
									<div class="thumbnail-container clearfix">
										<div class="product-image">
											{block name='product_thumbnail'}
												<a href="{$product.url}" target="_blank" class="thumbnail product-thumbnail">
													<img class="img-fluid"
														src = "{$product.cover.bySize.home_default.url}"
														alt = "{$product.cover.legend}"
														data-full-size-image-url = "{$product.cover.large.url}"
													>
												</a>
											{/block}

											{block name='product_flags'}
												<ul class="product-flags">
													{foreach from=$product.flags item=flag}
														<li class="product-flag {$flag.type}">{$flag.label}</li>
													{/foreach}
												</ul>
											{/block}
										</div>
										<div class="product-meta">
											{block name='product_name'}
												<h3 class="h3 product-title" itemprop="name"><a href="{$product.url}" target="_blank">{$product.name}</a></h3>
											{/block}

											{block name='product_price_and_shipping'}
												{if $product.show_price}
													<div class="product-price-and-shipping{if $product.has_discount} has_discount{/if}">
														{if $product.has_discount}
															{hook h='displayProductPriceBlock' product=$product type="old_price"}
															<span class="regular-price">{$product.regular_price}</span>
															{if $product.discount_type === 'percentage'}
																<span class="discount-percentage">{$product.discount_percentage}</span>
															{elseif $product.discount_type === 'amount'}
																<span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
															{/if}
														{/if}
														{hook h='displayProductPriceBlock' product=$product type="before_price"}
														<span class="price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
															<span itemprop="priceCurrency" content="{$currency.iso_code}"></span>
															<span itemprop="price" content="{$product.price_amount}">{$product.price}</span>
														</span>
												
														{hook h='displayProductPriceBlock' product=$product type='unit_price'}
														{hook h='displayProductPriceBlock' product=$product type='weight'}
													</div>	  
												{/if}
											{/block}

											{hook h='displayDeoCartCombination' product=$product}
											{hook h='displayDeoCartQuantity' product=$product}
											{hook h='displayDeoCartButton' product=$product}

											{* <div class="wishlist-product-info">										
												<input class="form-control wishlist-product-quantity wishlist-product-quantity-{$wishlist.id_wishlist_product}" type="{if $show_button_cart}hidden{else}number{/if}" data-min=1 value="{$wishlist.quantity}">					
												<div class="form-group">
													<label>
														<strong>{l s='Priority' mod='deotemplate'}: </strong>
														{if $wishlist.priority == 0}{l s='High' mod='deotemplate'}{/if}
														{if $wishlist.priority == 1}{l s='Medium' mod='deotemplate'}{/if}
														{if $wishlist.priority == 2}{l s='Low' mod='deotemplate'}{/if}
													</label>									
												</div>
											</div> *}
										</div>										
									</div>											
								</div>
							{/foreach}
						{else}
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div class="alert alert-danger">{l s='No products' mod='deotemplate'}</div>
							</div>
						{/if}
					</div>
				</section>
			{else}
				<div class="alert alert-danger">{l s='Wishlist does not exist' mod='deotemplate'}</div>
			{/if}
		</div>
	</section>
{/block}

