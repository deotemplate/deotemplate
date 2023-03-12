{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div id="static-customer-info-container" class="">
  {if !$s_customer.is_guest && $s_customer.is_logged}
    <a class="edit-customer-info" href="{$urls.pages.identity}">
      <div class="static-customer-info" data-edit-label="{l s='Edit' d='Shop.Theme.Actions'}">
        <div class="customer-name">{$s_customer.firstname} {$s_customer.lastname}</div>
        <div class="customer-email">{$s_customer.email}</div>
      </div>
    </a>
  {/if}
</div>
