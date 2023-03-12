{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
<div class="highlighted-informations{if !$product.main_variants} no-variants{/if}">
	{block name='product_variants'}
		{if $product.main_variants}
			{include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
		{/if}
	{/block}
</div>