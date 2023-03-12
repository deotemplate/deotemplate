{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if $products && count($products) >0}
	{foreach from=$products item=product_item name=for_products}
		{assign var='product' value=$product_item.product_info}
		{assign var='wishlist' value=$product_item.wishlist_info}
		<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 product-miniature js-product-miniature deo-wishlist-product-item deo-wishlist-product-item-{$wishlist.id_wishlist_product} product-{$product.id_product}" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
			<div class="thumbnail-container product-wishlist">
				<div class="deo-delete-wishlist-product">
					<a class="btn" href="javascript:void(0)" title="{l s='Remove from this wishlist' mod='deotemplate'}" data-id-wishlist="{$wishlist.id_wishlist}" data-id-wishlist-product="{$wishlist.id_wishlist_product}" data-id-product="{$product.id_product}"><i class="deo-custom-icons"></i>
					</a>
				</div>
				<div class="product-image wishlist-product-image">
					<a href="{$product.url}" class="thumbnail product-thumbnail">
						<img class="img-fluid"
							src = "{$product.cover.bySize.home_default.url}"
							alt = "{$product.cover.legend}"
							data-full-size-image-url = "{$product.cover.large.url}"
						>
					</a>
					<ul class="product-flags">
						{foreach from=$product.flags item=flag}
							<li class="product-flag {$flag.type}">{$flag.label}</li>
						{/foreach}
					</ul>
				</div>
				<div class="wishlist-product-info">
					<h3 class="h3 product-title" itemprop="name"><a href="{$product.url}">{$product.name}</a></h3>
					{* <div class="form-group">
						<label>{l s='Quantity' mod='deotemplate'}</label>
						<input class="form-control wishlist-product-quantity wishlist-product-quantity-{$wishlist.id_wishlist_product}" type="number" min=1 value="{$wishlist.quantity}">					
					</div>
					<div class="form-group">
						<label>{l s='Priority' mod='deotemplate'}</label>
						<select class="form-control wishlist-product-priority wishlist-product-priority-{$wishlist.id_wishlist_product}">
							{for $i=0 to 2}
								<option value="{$i}"{if $i == $wishlist.priority} selected="selected"{/if}>								
									{if $i == 0}{l s='High' mod='deotemplate'}{/if}
									{if $i == 1}{l s='Medium' mod='deotemplate'}{/if}
									{if $i == 2}{l s='Low' mod='deotemplate'}{/if}								
								</option>
							{/for}
						</select>
					</div> *}
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
								<span itemprop="price" content="{$product.price_amount}">{$product.price}</span>
							</span>
					
							{hook h='displayProductPriceBlock' product=$product type='unit_price'}
							{hook h='displayProductPriceBlock' product=$product type='weight'}
						</div>	  
					{/if}
					{hook h='displayDeoCartCombination' product=$product}
					{hook h='displayDeoCartQuantity' product=$product}
				</div>	
				<div class="wishlist-product-action">
					{hook h='displayDeoCartButton' product=$product}	
					{* <a class="deo-wishlist-product-save-button btn btn-outline" href="javascript:void(0)" title="{l s='Save' mod='deotemplate'}" data-id-wishlist="{$wishlist.id_wishlist}" data-id-wishlist-product="{$wishlist.id_wishlist_product}" data-id-product="{$product.id_product}"><i class="deo-icon-loading-button"></i><span class="text">{l s='Save' mod='deotemplate'}</span>
					</a> *}
					{if isset($wishlists) && count($wishlists) > 0}					
						<div class="dropdown deo-wishlist-button-dropdown">					 
							<button class="deo-wishlist-button btn btn-outline show-list" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{l s='Move' mod='deotemplate'}</button>
							<div class="dropdown-menu deo-list-wishlist deo-list-wishlist-{$product.id_product}">				
								{foreach from=$wishlists item=wishlists_item}							
									<a href="#" class="dropdown-item list-group-item list-group-item-action move-wishlist-item" data-id-wishlist="{$wishlists_item.id_wishlist}" data-id-wishlist-product="{$wishlist.id_wishlist_product}" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" title="{$wishlists_item.name}">{$wishlists_item.name}</a>			
								{/foreach}
							</div>
						</div>
					{/if}
				</div>
			</div>
		</div>
	{/foreach}
{else}
	<div class="col-xl-12">
		<p class="alert alert-danger">{l s='No products' mod='deotemplate'}</p>
	</div>
{/if}

