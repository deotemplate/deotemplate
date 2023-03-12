{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="title-heading address-name-header h2">
    <span class="title">{l s='Address' mod='deotemplate'}</span>
</div>
<div id="deoonepagecheckout-address-invoice" class="opc-element opc-address" {if !$showBillToDifferentAddress && !$isInvoiceAddressPrimary}style="display: none;"{/if}>
    {include file='module:deotemplate/views/templates/front/onepagecheckout/blocks/address-invoice.tpl'}
</div>
<div id="deoonepagecheckout-address-delivery" class="opc-element opc-address" {if !$showShipToDifferentAddress && $isInvoiceAddressPrimary}style="display: none;"{/if}>
    {include file='module:deotemplate/views/templates/front/onepagecheckout/blocks/address-delivery.tpl'}
</div>