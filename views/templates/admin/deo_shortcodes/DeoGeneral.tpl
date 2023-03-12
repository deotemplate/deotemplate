{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div {if !isset($deoInfo)}id="default_widget"{/if} class="{if !isset($deoInfo)}new-shortcode {/if}widget-row clearfix{if isset($deoInfo)} {$deoInfo.name|escape:'html':'UTF-8'}{if isset($deoInfo.icon_class)} widget-icon{/if}{/if}{if isset($formAtts)} {$formAtts.form_id|escape:'html':'UTF-8'}{/if}{if isset($formAtts.class)} {$formAtts.class|escape:'html':'UTF-8'}{/if}{if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}" {if isset($deoInfo)}data-type="{$deoInfo.name|escape:'html':'UTF-8'}"{/if} data-class="{if isset($formAtts.class)}{$formAtts.class|escape:'html':'UTF-8'}{/if}" {if isset($kshort) && isset($deoInfo.config)}data-form="{$deoInfo.config|json_encode|escape:'html':'UTF-8'}"{/if}>
	{if isset($formAtts)}
	   <a id="{$formAtts.form_id|escape:'html':'UTF-8'}" name="{$formAtts.form_id|escape:'html':'UTF-8'}"></a>
	{/if}
    <div class="widget-controll-top pull-right">
        <a href="javascript:void(0)" title="{l s='Drag to sort Widget' mod='deotemplate'}" class="widget-action waction-drag label-tooltip"><i class="icon-move"></i> </a>
        <a href="javascript:void(0)" title="{l s='Disable or Enable Column' mod='deotemplate'}" class="widget-action btn-status{if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if} label-tooltip"><i class="{if isset($formAtts.active) && !$formAtts.active}icon-remove{else}icon-ok{/if}"></i></a>
        <a href="javascript:void(0)" title="{l s='Edit Widget' mod='deotemplate'}" class="widget-action btn-edit label-tooltip" {if isset($deoInfo)}data-type="{$deoInfo.name|escape:'html':'UTF-8'}"{/if}><i class="icon-cog"></i></a>
        <a href="javascript:void(0)" title="{l s='Duplicate Widget' mod='deotemplate'}" class="widget-action btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
        <a href="javascript:void(0)" title="{l s='Delete Column' mod='deotemplate'}" class="widget-action btn-delete label-tooltip"><i class="icon-trash"></i></a>
    </div>
    <div class="widget-content">
        <img class="w-img" src="{if isset($deoInfo.image) && $deoInfo.image}{$moduleDir|escape:'html':'UTF-8'}/deotemplate/views/img/icons/{$deoInfo.image}{else}{$moduleDir|escape:'html':'UTF-8'}deotemplate/logo.png{/if}" title="{l s='Widget' mod='deotemplate'}" alt="{l s='Widget' mod='deotemplate'}"/>
        <i class="icon w-icon{if isset($deoInfo) && isset($deoInfo.icon_class)} {$deoInfo.icon_class|escape:'html':'UTF-8'}{/if}"></i>
        <span class="widget-title">
            <span class="title-name">{if isset($formAtts.title) && $formAtts.title}{$formAtts.title|rtrim|escape:'html':'UTF-8'}{/if}</span>
            <span class="widget-name">{if isset($deoInfo)}{$deoInfo.label|escape:'html':'UTF-8'}{/if}</span>
            <span class="widget-desc">{if isset($deoInfo.desc)}{$deoInfo.desc|truncate:120|escape:'html':'UTF-8'}{/if}</span>
        </span>
    </div>
</div>