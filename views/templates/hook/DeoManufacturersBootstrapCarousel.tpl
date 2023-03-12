{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div data-ride="carousel" class="carousel slide" id="{$carouselName}">
	{$NumManu = count($manufacturers)}
	{if $NumManu > $itemsperpage}
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
	<div class="carousel-inner">

	{if array_key_exists('value_by_manufacture',$formAtts) && $formAtts.value_by_manufacture eq '1'}
		{$Num=array_chunk($manuselect,$itemsperpage)}
	{else}
		{$Num=array_chunk($manuselect,$itemsperpage)}
	{/if}
		{foreach from=$Num item=manuselect name=manuloop}
			<div class="carousel-item {if $smarty.foreach.manuloop.first}active{/if}">
				{$i = 0}
				{foreach from=$manuselect item=manu}
					{$i = $i+1}
					{if ($i mod $nbItemsPerLine) eq 1 || $i eq 1}
						<div class="row">
					{/if}
					<div class="manufacturer-item {$scolumn}">
						{include file=$deo_helper->getTplTemplate('DeoManufacturersItem.tpl', $formAtts['override_folder'])}
					</div>
					{if ($i mod $nbItemsPerLine) eq 0}
						</div>
					{/if}
				{/foreach}
				{if ($i mod $nbItemsPerLine) gt 0}
					</div>
				{/if}
			</div>
		{/foreach}
	</div>
</div>
