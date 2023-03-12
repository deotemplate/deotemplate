{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{block name='product_price_and_shipping'}
  {if $product.show_price}
    <div class="product-price-and-shipping {if $product.has_discount}has_discount{/if}">
      {if $product.has_discount}
        {hook h='displayProductPriceBlock' product=$product type="old_price"}
        <span class="regular-price" aria-label="{l s='Regular price' d='Shop.Theme.Catalog'}">{$product.regular_price}</span>
        {if $product.discount_type === 'percentage'}
          <span class="discount-percentage discount-produc">{$product.discount_percentage}</span>
        {elseif $product.discount_type === 'amount'}
          <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
        {/if}
      {/if}

      {hook h='displayProductPriceBlock' product=$product type="before_price"}
      
      <span class="price" aria-label="{l s='Price' d='Shop.Theme.Catalog'}">
        {capture name='custom_price'}{hook h='displayProductPriceBlock' product=$product type='custom_price' hook_origin='products_list'}{/capture}
        {if '' !== $smarty.capture.custom_price}
          {$smarty.capture.custom_price nofilter}
        {else}
          {$product.price}
        {/if}
      </span>
      <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="invisible">
        <meta itemprop="priceCurrency" content="{$currency.iso_code}" />
        <meta itemprop="price" content="{$product.price_amount}" />
      </div>

      {hook h='displayProductPriceBlock' product=$product type='unit_price'}

      {hook h='displayProductPriceBlock' product=$product type='weight'}
    </div>
  {/if}
{/block}
