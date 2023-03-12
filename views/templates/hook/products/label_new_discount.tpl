{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
<div class="box-label">
  {block name='box_label'}
    {foreach from=$product.flags item=flag}
      {if $flag.type ==  'new'}
        <label class="label product-flag {$flag.type}"><span>{l s='New' d='Shop.Theme.Global'}</span></label>
      {/if}
    {/foreach}
    {if $product.has_discount}
      {if $product.discount_type === 'percentage'}
        <label class="label product-flag discount discount-percentage"><span>{$product.discount_percentage}</span></label>
      {elseif $product.discount_type === 'amount'}
        <label class="label product-flag discount discount-amount"><span>{$product.discount_amount_to_display}</span></label>
      {/if}
    {/if}
  {/block}
</div>