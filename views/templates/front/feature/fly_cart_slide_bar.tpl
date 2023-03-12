{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if $enable_overlay_background}
	<div class="deo-cart-mask"></div>
{/if}

<div class="deo-sidebar-cart {$type}">
	<div class="cart-sidebar-heading">
		<h3 class="title">{l s='Basket' mod='deotemplate'} <span class="alias"><span class="icon-cart-total"></span> {l s='item(s)' mod='deotemplate'}</span></h3>
		<a href="javascript:void(0)" class="close-sidebar-cart"></a>
	</div>
	<div class="cart-sidebar-header">
		<div class="icon-cart-sidebar-wrapper">
			<a href="javascript:void(0)" class="icon-cart-sidebar"></a>
			<span class="icon-cart-total"></span>
		</div>
		<div class="deo-icon-cart-loading"></div>
	</div>
</div>