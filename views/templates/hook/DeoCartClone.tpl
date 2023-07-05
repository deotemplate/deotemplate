{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
<div class="block {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
	<div class="deo-cart-solo solo ">
		<div class="icon-cart-sidebar-wrapper">
			<a href="javascript:void(0)" class="icon-cart-sidebar"></a>
			<span class="icon-cart-total">0</span>
			<span class="name-simple">{l s='Cart'  mod='deotemplate'}</span>
		</div>
		<div class="deo-icon-cart-loading"></div>
	</div>
</div>