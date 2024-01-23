{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{block name='product_name'}
  <h3 class="h3 product-title"><a href="{$product.url}" content="{$product.url}">{$product.name|truncate:100:'...'}</a></h3>
{/block}
