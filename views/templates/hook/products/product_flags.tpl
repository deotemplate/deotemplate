{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{block name='product_flags'}
	<ul class="product-flags">
		{foreach from=$product.flags item=flag}
			<li class="product-flag {$flag.type}">{$flag.label}</li>
		{/foreach}
	</ul>
{/block}
