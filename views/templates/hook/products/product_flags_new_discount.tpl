{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{block name='product_flags'}
	<ul class="product-flags">
		{foreach from=$product.flags item=flag}
			{if $flag.type != 'new' && $flag.type != 'discount'}
				<li class="product-flag {$flag.type}">{$flag.label}</li>
			{/if}
		{/foreach}
	</ul>
{/block}
