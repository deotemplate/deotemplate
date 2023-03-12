{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}


{if isset($product.combinations) && count($product.combinations) > 0}		
	<div class="dropdown deo-dropdown-select-attr">
		<button class="btn btn-outline dropdown-toggle deo-btn-select-attr dropdownListAttrButton_{$product.id_product}" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			{$product.attribute_designation}
		</button>
		<div class="dropdown-menu deo-list-attr">
			{foreach from=$product.combinations item=attribute}
				{if $attribute.add_to_cart_url == '' && !$PS_DISPLAY_UNAVAILABLE_ATTR}
					{* continue next loop *}
				{else}
					<a href="javascript:void(0)" class="dropdown-item deo-select-attr{if (isset($id_product_attr) && $attribute.id_product_attribute == $id_product_attribute)}{else}{if $attribute.id_product_attribute == $product.id_product_attribute} selected{/if}{/if}{if $attribute.add_to_cart_url == ''} disable{/if}" data-id-attr="{$attribute.id_product_attribute}">{$attribute.attribute_designation}</a>
				{/if}
			{/foreach}
		</div>
	</div>
{/if}

