{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="btn-quickview-product-list{if !$product.main_variants} no-variants{/if} hidden-sm-down">
	<a
		href="javascript:void(0)"
		class="deo-quick-view btn-product btn quick-view js-quick-view"
		data-link-action="quickview"
		data-source=".thumb-gallery-{$product.id}-{$product.id_product_attribute}"
		title="{l s='Quick view' d='Shop.Theme.Actions'}"
		data-toggle="deo-tooltip" data-position="top"
	>
		<span class="content-btn-product">
			<i class="loading-btn-product"></i>
			<i class="icon-btn-product icon-quick-view"></i>
			<span class="name-btn-product">{l s='Quick view' d='Shop.Theme.Actions'}</span>
		</span>
	</a>
</div>
