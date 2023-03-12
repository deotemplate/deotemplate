{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="btn-wishlist-product-list">
	{if isset($wishlists) && count($wishlists) > 1}
		<div class="dropdown deo-wishlist-button-dropdown">
			<button class="deo-wishlist-button show-list btn-product btn{if $added_wishlist} added{/if}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id-wishlist="{$id_wishlist}" data-id-product="{$id_product}" data-id-product-attribute="{$id_product_attribute}" data-toggle="deo-tooltip" data-position="top" title="{l s='Add to Wishlist' mod='deotemplate'}">
				<span class="content-btn-product">
					<i class="loading-btn-product"></i>
					<i class="icon-btn-product icon-wishlist"></i>
					<span class="name-btn-product">{l s='Wishlist' mod='deotemplate'}</span>
				</span>
			</button>
			<div class="dropdown-menu deo-list-wishlist deo-list-wishlist-{$id_product}">
				{foreach from=$wishlists item=wishlists_item}
					<a href="javascript:void(0)" class="dropdown-item list-group-item list-group-item-action wishlist-item{if in_array($wishlists_item.id_wishlist, $wishlists_added)} added {/if}" data-id-wishlist="{$wishlists_item.id_wishlist}" data-id-product="{$id_product}" data-id-product-attribute="{$id_product_attribute}" title="{if in_array($wishlists_item.id_wishlist, $wishlists_added)}{l s='Remove from Wishlist' mod='deotemplate'}{else}{l s='Add to Wishlist' mod='deotemplate'}{/if}">{$wishlists_item.name}</a>			
				{/foreach}
			</div>
		</div>
	{else}
		<a class="deo-wishlist-button btn-product btn{if $added_wishlist} added{/if}" href="javascript:void(0)" data-id-wishlist="{$id_wishlist}" data-id-product="{$id_product}" data-id-product-attribute="{$id_product_attribute}" title="{if $added_wishlist}{l s='Remove from Wishlist' mod='deotemplate'}{else}{l s='Add to Wishlist' mod='deotemplate'}{/if}" data-toggle="deo-tooltip" data-position="top">
			<span class="content-btn-product">
				<i class="loading-btn-product"></i>
				<i class="icon-btn-product icon-wishlist"></i>
				<span class="name-btn-product">{l s='Wishlist' mod='deotemplate'}</span>
			</span>
		</a>
	{/if}
</div>