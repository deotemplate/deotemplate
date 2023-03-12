{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if (int) DeoHelper::getConfig('ENABLE_PRODUCT_WISHLIST')}
	<div class="deo-count-wishlist deo-count-feature block {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
		<a class="deo-btn-wishlist" href="{url entity='module' name='deotemplate' controller='mywishlist'}" title="{l s='Wishlist' mod='deotemplate'}" rel="nofollow">
			<i class="deo-custom-icons icon-wishlist"></i>
			<span class="text">
				<span class="name-simple">{l s='Wishlist'  mod='deotemplate'}</span>{if isset($formAtts.count) && $formAtts.count} <span class="deo-total-wishlist deo-total">0</span>{/if}
			</span>
		</a>
	</div>
{/if}
