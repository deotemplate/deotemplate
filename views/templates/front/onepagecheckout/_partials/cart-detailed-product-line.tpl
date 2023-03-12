{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}


<div class="product-line-grid row">
	<!--  product left content: image-->
	<div class="product-line-grid-left col-sp-7">
		<div class="product-line-grid-body">
			<span class="product-image">
				{if $product.cover}
					<img src="{$product.cover.bySize.cart_default.url}" alt="{$product.name|escape:'quotes'}">
				{else}
					<img src="{$urls.no_picture_image.bySize.cart_default.url}" />
				{/if}
			</span>
			<div class="product-meta">
				<div class="product-line-info product-name">
					<a class="link-product" href="{$product.url}" data-id_customization="{$product.id_customization|intval}">{$product.name}</a>
				</div>

				<div class="product-line-info product-price {if $product.has_discount}has-discount{/if}">
					{if $product.has_discount}
						<div class="product-discount">
							<span class="regular-price">{$product.regular_price}</span>
							{if $product.discount_type === 'percentage'}
								<span class="discount discount-percentage">
									-{$product.discount_percentage_absolute}
								</span>
							{else}
								<span class="discount discount-amount">
									-{$product.discount_to_display}
								</span>
							{/if}
						</div>
					{/if}
					<div class="current-price">
						<span class="price">{$product.price}</span>
						{if $product.unit_price_full}
							<div class="unit-price-cart">{$product.unit_price_full}</div>
						{/if}
					</div>
					{hook h='displayProductPriceBlock' product=$product type="unit_price"}
				</div>

				{if count($product.attributes)}
					<div class="product-attributes-block">
						{foreach from=$product.attributes key="attribute" item="value"}
							<div class="product-line-info attributes {$attribute|lower}">
								<span class="label">{$attribute}:</span>
								<span class="value">{$value}</span>
							</div>
						{/foreach}
					</div>
				{/if}

				{if is_array($product.customizations) && $product.customizations|count}
					<br>
					{block name='cart_detailed_product_line_customization'}
						{foreach from=$product.customizations item="customization"}
							<a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
							<div class="modal fade customization-modal js-customization-modal" id="product-customizations-modal-{$customization.id_customization}" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}">
												<span aria-hidden="true">&times;</span>
											</button>
											<h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>
										</div>
										<div class="modal-body">
											{foreach from=$customization.fields item="field"}
												<div class="product-customization-line row">
													<div class="col-sp-3 label">
														{$field.label}
													</div>
													<div class="col-sp-9 value">
														{if $field.type == 'text'}
															{if (int)$field.id_module}
																{$field.text nofilter}
															{else}
																{$field.text}
															{/if}
														{elseif $field.type == 'image'}
															<img src="{$field.image.small.url}" loading="lazy">
														{/if}
													</div>
												</div>
											{/foreach}
										</div>
									</div>
								</div>
							</div>
						{/foreach}
					{/block}
				{/if}

				{* module config*}
				{if $show_product_stock_info}
					<div class="product-line-info quantity-info">
						<span class="{if $product.quantity_available <= 0 && !$product.allow_oosp}qty-label label-warning{else}qty-label label-success{/if}{if $product.quantity_available <= 0} label-later{/if}">
							{if $product.quantity_available <= 0}
								{if $product.allow_oosp}
									{if isset($product.available_later) && $product.available_later}
										{$product.available_later}
									{else}
										{*{$product.availability_message}*}
										{l s='In supplier stock' mod='deotemplate'}
									{/if}
								{else}
									{l s='Out of stock' mod='deotemplate'}
								{/if}
							{else}
								{if isset($product.available_now) && $product.available_now}
									{$product.available_now}
								{else}
									{l s='In stock' d='Shop.Theme.Catalog'}
								{/if}
							{/if}
						</span>
						<div class='qty-insufficient-stock{if $product.quantity_available>=$product.quantity || $product.quantity_available<=0} hidden{/if}'>
							<span class='qty-in-stock-only'>{l s='In stock only' mod='deotemplate'} {$product.quantity_available} {l s='pcs.' mod='deotemplate'}</span>
							{if $product.allow_oosp}
								<span class='qty-remaining-on'>{l s='Remaining pcs. in' mod='deotemplate'} {$product.available_later}</span>
							{else}
								<span class='qty-remaining-on no-longer-available'>{l s='Please adjust quantity' mod='deotemplate'}</span>
							{/if}
						</div>
						{*hook h="displayProductDeliveryTime" product=$product*}
					</div>
				{/if}
			</div>
		</div>
	</div>

	<!--  product left body: description -->
	<div class="product-line-grid-right product-line-actions col-sp-5">
		<div class="row">
			<div class="col-sp-10">
				<div class="row">
					<div class="col-sp-6 qty">
						<div class="product-line-qty" data-qty-control="{$product.id_product|escape:'javascript':'UTF-8'}-{$product.id_product_attribute|escape:'javascript':'UTF-8'}-{$product.id_customization|escape:'javascript':'UTF-8'}">
							<div class="qty-container">
								<div class="qty-box">
									{if isset($product.is_gift) && $product.is_gift}
										<span class="gift-quantity">{$product.quantity}</span>
									{else}
										<input
											class="cart-line-product-quantity"
											data-link-action="deo-update-cart-quantity"
											data-update-url="{$product.update_quantity_url}"
											data-id-product="{$product.id_product|escape:'javascript':'UTF-8'}"
											data-id-product-attribute="{$product.id_product_attribute|escape:'javascript':'UTF-8'}"
											data-id-customization="{$product.id_customization|default|escape:'javascript':'UTF-8'}"
											data-qty-orig="{$product.quantity|escape:'javascript':'UTF-8'}"
											type="text"
											value="{$product.quantity}"
											name="product-quantity-spin"
											min="{$product.minimal_quantity}"
										/>
										<a class="cart-line-product-quantity-up"
											href="{$product.up_quantity_url}"
											data-link-action="deo-update-cart-quantity-up"></a>
										<a class="cart-line-product-quantity-down"
											href="{$product.down_quantity_url}"
											data-link-action="deo-update-cart-quantity-down"></a>
									{/if}
								</div>
							</div>
						</div>
					</div>
					<div class="col-sp-6 price text-sp-right">
						<div class="product-line-price">
							{if isset($product.is_gift) && $product.is_gift}
								<span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
							{else}
								{$product.total}
							{/if}
						</div>
					</div>
				</div>
			</div>
			<div class="col-sp-2 text-sp-right">
				<div class="cart-line-product-actions product-line-delete">
					<a
						class="remove-from-cart"
						rel="nofollow"
						href="{$product.remove_from_cart_url}"
						data-link-action="deo-delete-from-cart"
						data-id-product="{$product.id_product|escape:'javascript':'UTF-8'}"
						data-id-product-attribute="{$product.id_product_attribute|escape:'javascript':'UTF-8'}"
						data-id-customization="{$product.id_customization|default|escape:'javascript':'UTF-8'}"
						title="{l s='Delete' d='Shop.Theme.Actions'}"
					>
						{if empty($product.is_gift)}
							<i class="deo-custom-icons delete"></i>
						{/if}
					</a>

					{block name='hook_cart_extra_product_actions'}
						{hook h='displayCartExtraProductActions' product=$product}
					{/block}
				</div>
			</div>
		</div>
	</div>
</div>
