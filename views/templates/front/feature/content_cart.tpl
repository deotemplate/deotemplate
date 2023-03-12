{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if $only_content_cart != 1}
	<div class="deo-content-cart clearfix{if $enable_update_quantity} enable-update-qty{/if}">
		<div class="list-cart-item-warpper">
			<ul class="list-items">
				{foreach from=$cart.products item=product name="cart_product"}
					<li {* style="width: {$width_cart_item}px; height: {$height_cart_item}px" *} class="cart-item clearfix{if ($product.attributes|count && $show_combination) || ($product.customizations|count && $show_customization)} has-view-additional{/if}{if $smarty.foreach.cart_product.first} first{/if}{if $smarty.foreach.cart_product.last} last{/if}">
						<div class="cart-item-img">
							{if $product.images}
								<a href="{$product.url}" class="img" title="{$product.name}"><img class="img-fluid" src="{$product.images.0.bySize.small_default.url}" alt="{$product.name}" title="{$product.name}"/></a>
								<span class="product-qty"><span class="qty-number">{$product.quantity}</span> {l s='item(s)' mod='deotemplate'}</span>
							{/if}	
						</div>						
						<div class="cart-item-info">					
							<div class="product-name"><a href="{$product.url}" title="{$product.name}">{$product.name}</a></div>
							<div class="product-price">
								{if $product.has_discount}
									<span class="product-discount">
										<span class="regular-price">{$product.regular_price}</span>
										{if $product.discount_type === 'percentage'}
											<span class="discount discount-percentage">-{$product.discount_percentage_absolute}</span>
										{else}
											<span class="discount discount-amount">-{$product.discount_to_display}</span>
										{/if}
									</span>
								{/if}
								<span class="price">{$product.price}</span>
								{if $product.unit_price_full}
									<span class="unit-price-cart">{$product.unit_price_full}</span>
								{/if}
							</div>

							{if $product.attribute_infomations|count && $show_combination}							
								<div class="combinations additional-cart-infor">
									{foreach from=$product.attribute_infomations item="attribute" name="attribute"}
										<span class="product-info-line">
											{* {if $attribute.group_type == 'color'}
												<label class="color" title="{$attribute.group_name}: {$attribute.attribute_name}" style="background-color: {$attribute.attribute_color}" data-toggle="deo-tooltip" data-position="top">
													<span class="attribute-name">{$attribute.attribute_name}</span>
												</label>
											{else} *}
												<span class="title">{$attribute.group_name}:</span>
												<span class="value">{$attribute.attribute_name}</span>
											{* {/if} *}
										</span>
									{/foreach}
								</div>
							{/if}
							{if $product.customizations|count && $show_customization}
								<div class="customizations additional-cart-infor">
									{foreach from=$product.customizations item='customization'}			
										{foreach from=$customization.fields item='field'}
											<span class="product-info-line">
												<span class="title">{$field.label}:</span>
												<span class="value">
													{if $field.type == 'text'}
														{$field.text nofilter}
													{else if $field.type == 'image'}
														<img src="{$field.image.small.url}" class="img-fluid">
													{/if}
												</span>
											</span>
										{/foreach}							
									{/foreach}								
								</div>
							{/if}

							{if $enable_update_quantity}
								<div class="product-quantity">												
									<a href="javascript:void(0)" class="btn-qty btn-qty-down"></a>
									<input
										class="input-product-qty input-group"
										data-down-url="{$product.down_quantity_url}"
										data-up-url="{$product.up_quantity_url}"
										data-update-url="{$product.update_quantity_url}"
										data-id-product = "{$product.id_product}"
										data-id-product-attribute = "{$product.id_product_attribute}"
										data-id-customization = "{$product.id_customization}"
										data-min-quantity="{$product.minimal_quantity}"
										data-product-quantity="{$product.quantity}"
										data-quantity-available="{$product.quantity_available}"									
										type="text"
										value="{$product.quantity}"								
										min="{$product.minimal_quantity}"
									/>
									<a href="javascript:void(0)" class="btn-qty btn-qty-up"></a>
								</div>
							{/if}
							<a class="remove-cart"					
								href="javascript:void(0)"					
								title="{l s='Remove from cart' mod='deotemplate'}" 
								data-link-url="{$product.remove_from_cart_url}"
								data-id-product = "{$product.id_product}"
								data-id-product-attribute = "{$product.id_product_attribute}"
								data-id-customization = "{$product.id_customization}"
							><i class="material-icons">&#xE872;</i></a>
						</div>
						<div class="overlay">
							<div class="loading-icon"></div>
						</div>
					</li>
				{/foreach}
			</ul>
		</div>
		<div class="cart-total-wrapper">
{/if}
			<div class="cart-total" data-cart-total="{$cart.products_count}">
				<div class="cart-subtotals">
					{foreach from=$cart.subtotals item="subtotal"}
						{if $subtotal}
							<div class="{$subtotal.type} total-line">
								<span class="title">{$subtotal.label}</span>
								<span class="value">{$subtotal.value}</span>
							</div>
						{/if}
					{/foreach}
				</div>
				<div class="total total-line">
					<span class="title">{$cart.totals.total.label}</span>
					<span class="value">{$cart.totals.total.value}</span>
				</div>
			</div>
{if $only_content_cart != 1}
			<div class="cart-buttons clearfix">
				<a class="view-cart btn btn-outline" href="{$cart_url}">{l s='View cart' mod='deotemplate'}</a>
				<a class="checkout btn btn-outline" href="{$order_url}">{l s='Checkout' mod='deotemplate'}</a>
				{if (defined('_DEO_MODE_DEV_') && _DEO_MODE_DEV_ === true)}
					<label class="custom-checkbox label-inherit">
						<input name="use_onepagecheckout" type="checkbox" value="1">
						<span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
						<span>{l s='Checkout with One Page Checkout' mod='deotemplate'}</span>
					</label>
				{/if}
			</div>
		</div>
	</div>
{/if}
