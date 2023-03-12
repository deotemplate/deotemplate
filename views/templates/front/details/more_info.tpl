{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if isset($product.category_name) && isset($product.id_category_default)}
  <div class="more-infor-product category-default">
    <label>{l s='Category' d='Shop.Theme.Global'}:</label> <a href="{$link_deo->getCategoryLink($product.id_category_default)|escape:'html':'UTF-8'}" title="{$product.category_name}">{$product.category_name}</a>
  </div>
{/if}
{if (isset($product.reference) && $product.reference neq '') || (isset($product.reference_to_display) && $product.reference_to_display neq '')}
  <div class="more-infor-product reference">
    <label>{l s='Reference' d='Shop.Theme.Global'}:</label> <span>{if isset($product.reference_to_display) && $product.reference_to_display neq ''}{$product.reference_to_display}{else}ddd{$product.reference}{/if}</span>
  </div>
{/if}
{if $product.show_quantities}
  <div class="more-infor-product product-quantities">
    <label class="label">{l s='In stock' d='Shop.Theme.Global'}:</label> <span data-stock="{$product.quantity}" data-allow-oosp="{$product.allow_oosp}">{$product.quantity} {$product.quantity_label}</span>
  </div>
{/if}