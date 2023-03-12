{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{* https://www.magictoolbox.com/magic360/integration/ *}
<div class="block {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
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
	<div class="block-image">
		<a href="{$formAtts.image}" class="Magic360" data-options="{strip}
			filename:{$formAtts.filename};
			columns:{$formAtts.columns};
			rows:{if $formAtts.rows && $formAtts.multiple_row}{$formAtts.rows}{else}1{/if};
			fullscreen:{if $formAtts.fullscreen}true{else}false{/if};
			spin:{if $formAtts.spin}{$formAtts.spin}{else}drag{/if};
			speed:{if $formAtts.speed}{$formAtts.speed}{else}50{/if};
			initialize-on:{if $formAtts.initialize_on}{$formAtts.initialize_on}{else}load{/if};
			{if $formAtts.use_large_image && $formAtts.large_filename}
				large-filename:{$formAtts.large_filename};
			{/if}
			{if $formAtts.autospin != 'off'}
				autospin:{if $formAtts.magnifier_width}{$formAtts.magnifier_width}{else}once{/if};
				autospin-speed:{if $formAtts.autospin_speed}{$formAtts.autospin_speed}{else}2000{/if};
				autospin-direction:{if $formAtts.autospin_direction}{$formAtts.autospin_direction}{else}clockwise{/if}; 
				autospin-start:{if $formAtts.autospin_start}{$formAtts.autospin_start}{else}load{/if};
				autospin-stop:{if $formAtts.autospin_stop}{$formAtts.autospin_stop}{else}never{/if};
				start-row:{if $formAtts.start_row && $formAtts.multiple_row}{$formAtts.start_row}{else}auto{/if};
				start-column:{if $formAtts.start_column}{$formAtts.start_column}{else}auto{/if};
			{else}
				autospin:off;
			{/if}
			{if $formAtts.magnify}
				magnify:true;
				magnifier-width:{if $formAtts.magnifier_width}{$formAtts.magnifier_width}{else}80%{/if};
				magnifier-shape:{if $formAtts.magnifier_shape}{$formAtts.magnifier_shape}{else}inner{/if};
			{else}
				magnify:false;
			{/if}
			mousewheel-step: 1;
			hint:true;"{/strip}
		>
			<img src="{$formAtts.image}" class="img-fluild" alt="">
		</a>
	</div>
</div>
