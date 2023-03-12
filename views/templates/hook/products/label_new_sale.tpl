{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
<div class="box-label">
  {block name='box_label'}
    {foreach from=$product.flags item=flag}
      {if $flag.type == 'discount' || $flag.type ==  'new'}
        <label class="label product-flag {$flag.type}"><span>{if $flag.type ==  'new'}{l s='New' d='Shop.Theme.Global'}{else}{l s='Sale' d='Shop.Theme.Actions'}{/if}</span></label>
      {/if}
    {/foreach}
  {/block}
</div>