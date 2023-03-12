{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{block name='product_description'}
  <div class="product-description" itemprop="description">
  	{$product.description|strip_tags nofilter}
  </div>
{/block}