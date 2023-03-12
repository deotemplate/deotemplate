{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{assign var=random value=1|mt_rand:999999999}
<div id="gmap_{$random}" class="block deo-google-map DeoGoogleMap custom-gmap title-normal title-center" 
	data-zoom="{$zoom}" 
	data-marker-list="{$marker_list}" 
	data-marker-center="{$marker_center}" 
	data-is-display-store="{$is_display_store}" 
>
	<div class="box-title">
		<h4 class="title_block">{l s='Our location' mod='deotemplate'}</h4>
	</div>
	<div class="google-map-cover {if $is_display_store}display-list-store{else}not-display-list-store{/if}" style="height:{$height};">
		<div class="google-map-content">
			<div id="map-canvas-gmap_{$random}" class="gmap" style="min-width:100px; min-height:100px;width:{$width};height:{$height};"></div>
		</div>
		{if $is_display_store}
			<div class="google-map-stores">
				<div id="google-map-stores-list-gmap_{$random}" class="google-map-stores-list"></div>
			</div>
		{/if}
	</div>
</div>
