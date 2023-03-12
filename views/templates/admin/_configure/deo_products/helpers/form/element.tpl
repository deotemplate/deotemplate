{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
<div class="widget-row {(isset($defaultItem)) ? $eItem.file : $eItem.config.file} plist-element {if isset($defaultItem)}active{else}{($eItem.form.active && isset($eItem.form.active)) ? 'active' : 'deactive'}{/if}" data-element="{(isset($defaultItem)) ? $eItem.file : $eItem.config.file}" data-form="{(!isset($eItem.dataForm)) ? '' : $eItem.dataForm|escape:'html':'UTF-8'}">
	<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag me to set layout' mod='deotemplate'}" class="widget-action waction-drag label-tooltip"><i class="icon-move"></i></a>
	<i class="{(isset($defaultItem)) ? $eItem.icon : $eItem.config.icon} widget-icon"></i>
	<span class="name">
		{(isset($defaultItem)) ? $eItem.name : $eItem.config.name}
		{if isset($defaultItem) && ($eItem.file == "more_image")}
			<span class="type">{$input.type_more_image[$eItem['data-form']['type']]}</span>
		{else if isset($eItem.config.file) && ($eItem.config.file == "more_image")}
			<span class="type">{(isset($eItem.form.type)) ? $input.type_more_image[$eItem.form.type] : $input.type_more_image[$eItem.config['data-form']['type']]}</span>
		{/if}
		{if isset($defaultItem) && ($eItem.file == "more_image" || $eItem.file == "product_thumbnail")}
			<span class="size">{$eItem['data-form']['size']}</span>
			{if $eItem.file == "product_thumbnail"}
				<span class="labelflag">{(isset($eItem['data-form']['labelflag'])) ? $input.labelflag[$eItem['data-form']['labelflag']] : ''}</span>
				<span class="effecthover">{(isset($eItem['data-form']['effecthover'])) ? $input.effecthover[$eItem['data-form']['effecthover']] : ''}</span>
			{/if}
		{else if isset($eItem.config.file) && ($eItem.config.file == "more_image" || $eItem.config.file == "product_thumbnail")}
			<span class="size">{(isset($eItem.form.size)) ? $eItem.form.size : $eItem.config['data-form']['size']}</span>
			{if $eItem.config.file == "product_thumbnail"}
				<span class="labelflag">{(isset($eItem.form.labelflag)) ? $input.labelflag[$eItem.form.labelflag] : $input.labelflag[$eItem.config['data-form']['labelflag']]}</span>
				<span class="effecthover">{(isset($eItem.form.effecthover)) ? $input.effecthover[$eItem.form.effecthover] : $input.effecthover[$eItem.config['data-form']['effecthover']]}</span>
			{/if}
		{/if}
	</span>
	<div class="widget-controll-top pull-right">
		{if (isset($defaultItem) && isset($eItem['data-form'])) || isset($eItem.config['data-form'])}
            <a href="javascript:void(0)" data-config="{(isset($defaultItem)) ? $eItem.file : $eItem.config.file}" title="{l s='Configure Element' mod='deotemplate'}" class="element-config label-tooltip"><i class="icon-cog"></i></a>
        {/if}
        <a href="javascript:void(0)" title="{l s='Remove Element' mod='deotemplate'}" class="plist-eremove label-tooltip"><i class="icon-trash"></i></a>
        <a href="javascript:void(0)" title="{l s='Disable or Enable Element' mod='deotemplate'}" class="btn-status label-tooltip"><i class="{if isset($defaultItem)}icon-ok{else}{($eItem.form.active && isset($eItem.form.active)) ? 'icon-ok' : 'icon-remove'}{/if}"></i></a>
    </div>
    {* {if (defined('_DEO_MODE_DEV_') && _DEO_MODE_DEV_ === true)} *}
		<div class="pull-right">
			<a class="plist-eedit" data-element="{(isset($defaultItem)) ? $eItem.file : $eItem.config.file}"><i class="icon-edit"></i></a>
		</div>
	{* {/if} *}
</div>