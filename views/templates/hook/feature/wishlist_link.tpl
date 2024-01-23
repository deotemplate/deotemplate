{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_WISHLIST'))}
	<li id="mywishlist-link" class="link-item">
		<a href="{$wishlist_link}">
			<i class="deo-custom-icons icon-wishlist"></i>
			<span>{l s='My Wishlist' mod='deotemplate'}</span>
		</a>
	</li>
{/if}

{if ((int) DeoHelper::getConfig('DELETE_ACCOUNT_LINK'))}
	<li id="delete-account-link" class="link-item">
		<a href="javascript:void(0)">
			<i class="material-icons">delete</i>
			<span>{l s='Delete account' mod='deotemplate'}</span>
		</a>
	</li>
{/if}

