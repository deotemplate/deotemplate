{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{assign var=name_module value=''}
{if isset($formAtts.title) && $formAtts.title}
    {assign var=name_module value=$formAtts.title|rtrim|escape:'html':'UTF-8'}
{elseif isset($formAtts.name_module) && $formAtts.name_module}
    {assign var=name_module value=$formAtts.name_module|escape:"html"}
{elseif isset($deoInfo.name) && $deoInfo.name}   
    {assign var=name_module value=$deoInfo.name|escape:"html"}
{/if}

<div {if !isset($deoInfo)}id="default_module"{/if} class="{if !isset($deoInfo)}new-shortcode {/if}widget-row widget-module clearfix{if isset($deoInfo)} {$deoInfo.name|escape:'html':'UTF-8'}{/if}{if isset($formAtts)} {$formAtts.form_id|escape:'html':'UTF-8'}{/if}{if isset($formAtts.class)} {$formAtts.class|escape:'html':'UTF-8'}{/if}{if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}" data-type="DeoModule" data-class="{if isset($formAtts.class)}{$formAtts.class|escape:'html':'UTF-8'}{/if}" {if isset($kshort) && isset($deoInfo.config)}data-form="{$deoInfo.config|json_encode|escape:'html':'UTF-8'}"{/if}>
    <div class="widget-controll-top pull-right">
        <a href="javascript:void(0)" title="{l s='Drag to sort Widget' mod='deotemplate'}" class="widget-action waction-drag label-tooltip"><i class="icon-move"></i> </a>
        <a href="javascript:void(0)" title="{l s='Disable or Enable Column' mod='deotemplate'}" class="widget-action btn-status  {if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if} label-tooltip">
            {if isset($formAtts.active) && !$formAtts.active}
                <i class="icon-remove"></i>
            {else}
                <i class="icon-ok"></i>
            {/if}
        </a>
        <a href="javascript:void(0)" title="{l s='Edit Widget' mod='deotemplate'}" class="widget-action btn-edit label-tooltip" data-type="DeoModule"><i class="icon-cog"></i></a>
        <a href="javascript:void(0)" title="{l s='Duplicate Widget' mod='deotemplate'}" class="widget-action btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
        <a href="javascript:void(0)" title="{l s='Delete Column' mod='deotemplate'}" class="widget-action btn-delete label-tooltip"><i class="icon-trash"></i></a>
    </div>
    <div class="widget-content">
        <img class="w-img" {if $name_module}src="../modules/{$name_module}/logo.png"{/if} title="{$name_module}" alt="{$name_module}"/>
        <i class="icon w-icon{if isset($deoInfo) && isset($deoInfo.icon_class)} {$deoInfo.icon_class|escape:'html':'UTF-8'}{/if}"></i>
        <span class="widget-title">
            <span class="widget-name">{$name_module}{* -  {if isset($deoInfo)}{$deoInfo.label|escape:'html':'UTF-8'}{/if} *}</span>
            <span class="widget-desc">{if isset($deoInfo.description)}{$deoInfo.description|truncate:120|escape:'html':'UTF-8'}{/if}</span>
        </span>
    </div>
</div>