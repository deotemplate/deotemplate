{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div id="{if !isset($deoInfo)}default_DeoPopup{/if}" class="{if !isset($deoInfo)}new-shortcode {/if}widget-row clearfix DeoPopup{if isset($formAtts.form_id)} {$formAtts.form_id|escape:'html':'UTF-8'}{/if}{if isset($formAtts.class)} {$formAtts.class|escape:'html':'UTF-8'}{/if}{if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}{if isset($deoInfo.icon_class)} widget-icon{/if}" data-type="DeoPopup" data-class="{if isset($formAtts.class)}{$formAtts.class|escape:'html':'UTF-8'}{/if}" {if isset($kshort) && isset($deoInfo.config)}data-form="{$deoInfo.config|json_encode|escape:'html':'UTF-8'}"{/if}>
    <div class="float-center-control text-center">
        <a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag to sort popup' mod='deotemplate'}" class="popup-action waction-drag label-tooltip"><i class="icon-move"></i> </a>
        <a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Disable or Enable Popup' mod='deotemplate'}" class="popup-action btn-status label-tooltip{if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}"><i class="{if isset($formAtts.active) && !$formAtts.active}icon-remove{else}icon-ok{/if}"></i></a>
        <a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Setting Popup' mod='deotemplate'}" class="popup-action btn-edit label-tooltip" data-type="DeoPopup"><i class="icon-cog"></i></a>
        <a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Duplicate Popup' mod='deotemplate'}" class="popup-action btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
        <a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Delete Popup' mod='deotemplate'}" class="popup-action btn-delete label-tooltip"><i class="icon-trash"></i></a>
    </div>
    <div class="widget-heading">
        <img class="w-img" src="{if isset($deoInfo.image) && $deoInfo.image}{$moduleDir|escape:'html':'UTF-8'}/deotemplate/views/img/icons/{$deoInfo.image}{else}{$moduleDir|escape:'html':'UTF-8'}deotemplate/logo.png{/if}" title="{l s='Widget' mod='deotemplate'}" alt="{l s='Widget' mod='deotemplate'}"/>
        <i class="icon w-icon{if isset($deoInfo) && isset($deoInfo.icon_class)} {$deoInfo.icon_class|escape:'html':'UTF-8'}{/if}"></i>
        <span class="widget-title">
            <span class="title-name">{if isset($formAtts.title) && $formAtts.title}{$formAtts.title|rtrim|escape:'html':'UTF-8'}{/if}</span>
            <span class="widget-name">{l s='Popup' mod='deotemplate'}</span>
            <span class="widget-desc">{if isset($deoInfo.desc)}{$deoInfo.desc|truncate:120|escape:'html':'UTF-8'}{/if}</span>
        </span>
    </div>
    <div class="panel-group" >
        <div class="subwidget-content">
            {if !isset($formAtts.form_id)}
                
            {else}
                {$deo_html_content}
            {/if}
        </div>
        <div class="text-center popup-content-control widget-container-heading">
            <a id="{if isset($formAtts.form_id)}{$formAtts.form_id|escape:'html':'UTF-8'}{/if}" href="javascript:void(0)" class="tabcontent-action popup btn-new-widget label-tooltip" title=""><i class="icon-plus"></i> {l s='Add widget' mod='deotemplate'}</a> |
            <a href="javascript:void(0)" title="" class="popup-action btn-edit label-tooltip" data-type="DeoPopup"><i class="icon-cog"></i> {l s='Setting Popup' mod='deotemplate'}</a>
        </div>
    </div>
</div>

