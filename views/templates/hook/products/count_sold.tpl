{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="deo-count-sold">
    <a href="javascript:void(0)" class="progress" data-toggle="deo-tooltip" data-position="top" title="{l s='Total' mod='deotemplate'} {$product.quantity_all_versions}">
        <span class="progressing" style="width: {$percent_sold};"></span>
        <span class="count-sold">{l s='Sold' mod='deotemplate'} {$count_sold}</span>
    </a>
</div>