{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div data-ride="carousel" class="carousel slide" id="{$carouselName|escape:'html':'UTF-8'}">
	{$NumCount = count($formAtts.slides)}
	{if $NumCount > $itemsperpage}
		<div class="direction">
			<a class="carousel-control left" href="#{$carouselName|escape:'html':'UTF-8'}" data-slide="prev">
				<span class="icon-prev hidden-xs" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="carousel-control right" href="#{$carouselName|escape:'html':'UTF-8'}" data-slide="next">
				<span class="icon-next" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		</div>
	{/if}
	<div class="carousel-inner">
	{$Num=array_chunk($formAtts.slides, $itemsperpage)}
	{foreach from=$Num item=sliders name=val}
		<div class="carousel-item {if $smarty.foreach.val.first}active{/if}">
			{foreach from=$sliders item=slider name="sliders"}
				{if $nbItemsPerLine == 1 || $smarty.foreach.sliders.first || $smarty.foreach.sliders.iteration%$nbItemsPerLine == 1}
					<div class="row">
				{/if}
				<div class="{$scolumn}">
                	{include file=$deo_helper->getTplTemplate('DeoBlockCarouselItem.tpl', $formAtts['override_folder'])}
				</div>
				{if $nbItemsPerLine == 1 || $smarty.foreach.sliders.last || $smarty.foreach.sliders.iteration%$nbItemsPerLine == 0}
					</div>
				{/if}
			{/foreach}
		</div>
	{/foreach}
</div>
</div>
