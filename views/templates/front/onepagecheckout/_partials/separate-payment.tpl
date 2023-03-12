{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<script>
    var amazon_ongoing_session = ("{$amazon_ongoing_session}" == "1");
</script>
<div style="display: none;">
  {* Inner container will be taken out by JS in separate-payment.js *}
  <section class="checkout-step" id="separate-payment-order-review">

    <div class="customer-block-container">
      <div id="customer-block">
        {$customer.firstname} {$customer.lastname} - {$customer.email}
      </div>
    </div>

    <div class="address-block-container">
      <div class="address-block" id="invoice_address">
        <span class="address-title-heading">{l s='Your Invoice Address' d='Shop.Theme.Checkout'}</span>
        {$formatted_addresses.invoice nofilter}
      </div>
    </div>
    <div class="address-block-container">
      <div class="address-block" id="delivery_address">
        <span class="address-title-heading">{l s='Your Delivery Address' d='Shop.Theme.Checkout'}</span>
        {$formatted_addresses.delivery nofilter}
      </div>
    </div>

    <div class="shipping-method-container">
      <div id="shipping-method">
        <span class="shipping-method-header">{l s='Shipping Method' d='Shop.Theme.Checkout'}</span>
        {if $shipping_logo}
          <img src="{$shipping_logo}" />
        {/if}
        {$shipping_method->name} - {$shipping_method->delay[$language.id]}
      </div>
      {if $delivery_message}
        <div id="delivery-message">
          <span class="delivery-message-header">{l s='Message' d='Shop.Forms.Labels'}</span>
          {$delivery_message}
        </div>
      {/if}
    </div>

    <div id="edit-button-block">
      <button id="deo-checkout-edit" data-href="{$urls.pages.order}" class="btn btn-primary">{l s='Edit' d='Shop.Theme.Actions'}</button>
    </div>

  </section>

</div>
