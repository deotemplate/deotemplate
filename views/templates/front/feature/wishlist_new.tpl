{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<tr class="new">
	<td>
		<a href="javascript:void(0)" class="view-wishlist-product" data-name-wishlist="{$wishlist->name}" data-id-wishlist="{$wishlist->id}">
			<i class="deo-custom-icons deo-icon-loading-inline"></i>
			{$wishlist->name}
			{* <span class="view-wishlist-product-loading view-wishlist-product-loading-{$wishlist->id}"></span> *}
		</a>
	</td>
	<td align="center" class="wishlist-numberproduct wishlist-numberproduct-{$wishlist->id}">0</td>
	<td align="center">0</td>
	<td align="center" class="wishlist-datecreate">{$wishlist->date_add}</td>					
	<td align="center"><a class="view-wishlist" data-token="{$wishlist->token}" target="_blank" href="{$url_view_wishlist}" title="{l s='View' mod='deotemplate'}">{l s='View' mod='deotemplate'}</a></td>
	<td align="center">
		<label class="form-check-label">
			<input class="default-wishlist form-check-input" data-id-wishlist="{$wishlist->id}" type="checkbox" {$checked}>
		</label>
	</td>
	<td align="center"><a class="delete-wishlist" data-id-wishlist="{$wishlist->id}" href="javascript:void(0)" title="{l s='Delete' mod='deotemplate'}"><i class="deo-custom-icons"></i></a></td>
</tr>

