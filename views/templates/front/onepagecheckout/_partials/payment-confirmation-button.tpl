{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div id="payment-confirmation">
  <div class="ps-shown-by-js">
    <button type="submit" class="btn btn-primary center-block">
      {l s='Pay' mod='deotemplate'} <span class="pay-amount">{$cart.totals.total_including_tax.value}</span>
    </button>
  </div>
</div>
