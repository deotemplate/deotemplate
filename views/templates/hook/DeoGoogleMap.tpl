{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{function name=deo_googlemap}
	<div id="{$formAtts.form_id}" class="block deo-google-map" 
		data-zoom="{$formAtts.zoom}" 
		data-marker-list="{$marker_list}" 
		data-marker-center="{$marker_center}" 
		data-is-display-store="{$formAtts.is_display_store}" 
	>
		<div class="google-map-cover {if $formAtts.is_display_store}display-list-store{else}not-display-list-store{/if}" style="height:{if isset($formAtts.height) && $formAtts.height}{$formAtts.height}{else}100%;{/if};">
			<div class="google-map-content">
				<div id="map-canvas-{$formAtts.form_id}" class="gmap" style="min-width:100px; min-height:100px;
					width:{if isset($formAtts.width) && $formAtts.width}{$formAtts.width}{else}100%;{/if}; 
					height:{if isset($formAtts.height) && $formAtts.height}{$formAtts.height}{else}100%;{/if};">
				</div>
			</div>
			{if $formAtts.is_display_store}
				<div class="google-map-stores">
					<div id="google-map-stores-list-{$formAtts.form_id}" class="google-map-stores-list"></div>
				</div>
			{/if}
		</div>
	</div>
{/function}

{if !isset($formAtts.accordion_type) || $formAtts.accordion_type == 'full'}{* Default : always full *}
	<div class="block widget-google-map {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			<div class="box-title">
		{/if}
			{if isset($formAtts.title) && $formAtts.title}
				<h4 class="title_block">{$formAtts.title}</h4>
			{/if}
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			</div>
		{/if}
		<div class="block_content">
			{deo_googlemap}
		</div>
	</div>
{elseif isset($formAtts.accordion_type) && ($formAtts.accordion_type == 'accordion' || $formAtts.accordion_type == 'accordion_small_screen' || $formAtts.accordion_type == 'accordion_mobile_screen')}
	<div class="block widget-google-map block-toggler {(isset($formAtts.class)) ? $formAtts.class : ''}{if $formAtts.accordion_type == 'accordion_small_screen'} accordion_small_screen{elseif $formAtts.accordion_type == 'accordion_mobile_screen'} accordion_mobile_screen{/if}{if isset($formAtts.sub_title) && $formAtts.sub_title} has-sub-title{/if}">
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			<div class="box-title">
		{/if}	
			{if isset($formAtts.title) && $formAtts.title}
				<div class="title clearfix">
					<h4 class="title_block">{$formAtts.title}</h4>
					<span class="navbar-toggler collapse-icons" data-target="#deo-google-map{$formAtts.form_id}" data-toggle="collapse">
						<i class="add"></i>
						<i class="remove"></i>
					</span>
				</div>
			{/if}
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			</div>
		{/if}
		<div class="collapse block_content" id="deo-google-map{$formAtts.form_id}">
			{deo_googlemap}
		</div>
	</div>
{/if}