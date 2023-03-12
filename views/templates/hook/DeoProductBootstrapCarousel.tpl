{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="carousel slide" id="{$carouselName}">
    {assign var="condition" value=false}
    {if count($products) > $itemsperpage}
        {$condition = true}
    {/if}
    <div class="carousel-inner">
        {$mproducts=array_chunk($products,$itemsperpage)}
        {foreach from=$mproducts item=products name=mypLoop}
            <div class="carousel-item {if $smarty.foreach.mypLoop.first}active{/if}">
                <ul class="product_list row grid {if isset($productClassWidget)}{$productClassWidget}{/if}">
                {foreach from=$products item=product name=products}
                    <li class="ajax_block_product product_block {$scolumn} col-sp-12 {if $smarty.foreach.products.first}first_item{elseif $smarty.foreach.products.last}last_item{/if}">
                        {if isset($product_item_path)}
                            {include file="$product_item_path"}
                        {/if}
                    </li>
                {/foreach}
                </ul>
            </div>		
        {/foreach}
    </div>
    {if $condition}
        <div class="direction">
            <a class="carousel-control left" href="#{$carouselName}" data-slide="prev">
                <span class="icon-prev hidden-xs" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control right" href="#{$carouselName}" data-slide="next">
                <span class="icon-next" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    {/if}
</div>
