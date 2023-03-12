{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if isset($product.category_name) && isset($product.id_category_default)}
	<div class="category-default">
		<a href="{$link->getCategoryLink($product.id_category_default)|escape:'html':'UTF-8'}" title="{$product.category_name}">{$product.category_name}</a>
	</div>
{/if}