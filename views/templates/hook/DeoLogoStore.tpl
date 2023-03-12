{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="logo block {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
	<div class="media">
		{if isset($formAtts.link_mobile) && $formAtts.link_mobile}
			<a href="{$urls.pages.index}" title="{$shop.name}">
				<i class="deo-custom-icons icon-home"></i>
				<span class="name-simple">{l s='Home'  mod='deotemplate'}</span>
			</a>
		{else}
			<a href="{$urls.pages.index}" class="image" {(isset($formAtts.is_open) && $formAtts.is_open) ? 'target="_blank"' : ''}>
				<img src="{if isset($formAtts.image)}{$formAtts.image}{else}{$shop.logo}{/if}" class="img-fluid {(isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation) ? 'has-animation' : ''}"
					{if isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation} data-animation="{$formAtts.animation}" {/if}
					{if $formAtts.animation_delay != '' && $formAtts.animation_delay} data-animation-delay="{$formAtts.animation_delay}" {/if}
					title="{$shop.name}" alt="{$shop.name}" 
				/>
			</a> 
		{/if}
	</div>
</div>