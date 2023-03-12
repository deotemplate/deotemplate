{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{block name='product_description_short'}
  <div class="product-description-short" itemprop="description">{$product.description_short|strip_tags:false|truncate:150:'...' nofilter}</div>
{/block}