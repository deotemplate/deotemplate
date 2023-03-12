{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if $cart.vouchers.allowed}
  {block name='cart_voucher'}
    <hr class="separator">
    <div class="block-promo">
      <div class="cart-voucher">
        {if $cart.vouchers.added}
          {block name='cart_voucher_list'}
            <ul class="promo-name card-block">
              {foreach from=$cart.vouchers.added item=voucher}
                <li class="cart-summary-line">
                  <span class="label">{$voucher.name}</span>
                  <a href="{$voucher.delete_url}" data-discount-id="{$voucher.id_cart_rule}" data-link-action="deo-remove-voucher"><span class="icon-delete"></span></a>
                  <div class="float-xs-right">
                    {$voucher.reduction_formatted}
                  </div>
                </li>
              {/foreach}
            </ul>
          {/block}
        {/if}

        <p class="promo-code-button display-promo{if $cart.discounts|count > 0} with-discounts{/if}">
          <a class="collapse-button" href="#promo-code">
            {l s='Have a promo code?' d='Shop.Theme.Checkout'}
          </a>
        </p>

        <div id="promo-code" class="collapse{if $cart.discounts|count > 0} in{/if}">
          <div class="promo-code">
            {block name='cart_voucher_form'}
              <form action="{$urls.pages.cart}" data-link-action="add-voucher" method="post">
                <input type="hidden" name="token" value="{$static_token}">
                <input type="hidden" name="addDiscount" value="1">
                <div class="input-group">
                  <input class="promo-input form-control" type="text" name="discount_name" placeholder="{l s='Promo code' d='Shop.Theme.Checkout'}">
                  <span class="input-group-btn">
                    <button type="submit" class="btn btn-outline"><span>{l s='Add' d='Shop.Theme.Actions'}</span></button>
                  </span>
                </div>
              </form>
            {/block}

            {block name='cart_voucher_notifications'}
              <div class="alert alert-danger js-error" role="alert">
                <i class="material-icons">&#xE001;</i><span class="ml-1 js-error-text"></span>
              </div>
            {/block}
            
            <div class="close-promo text-sp-center">
              <a href="javascript:void(0)" class="collapse-button promo-code-button cancel-promo" role="button" data-toggle="collapse" data-target="#promo-code" aria-expanded="true" aria-controls="promo-code">{l s='Close' d='Shop.Theme.Checkout'}</a>
            </div>
             
          </div>
        </div>

        {if $cart.discounts|count > 0}
          <p class="block-promo promo-highlighted">
            {l s='Take advantage of our exclusive offers:' d='Shop.Theme.Actions'}
          </p>
          <ul class="js-discount card-block promo-discounts">
          {foreach from=$cart.discounts item=discount}
            <li class="cart-summary-line">
              <span class="label"><span class="code">{$discount.code}</span> - {$discount.name}</span>
            </li>
          {/foreach}
          </ul>
        {/if}
      </div>
    </div>
  {/block}
{/if}
