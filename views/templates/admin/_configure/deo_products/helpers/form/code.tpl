{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
<div class="widget-row code plist-element {if isset($defaultItem)}active{else}{($eItem.form.active && isset($eItem.form.active)) ? 'active' : 'deactive'}{/if}" data-element="code" data-form="{(isset($defaultItem)) ? '' : $eItem.dataForm|escape:'html':'UTF-8'}">
    <a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag me' mod='deotemplate'}" class="widget-action waction-drag label-tooltip"><i class="icon-move"></i> </a> TPL code
    <div class="widget-controll-top pull-right">
        <a href="javascript:void(0)" title="{l s='Expand/Collapse Code' mod='deotemplate'}" class="plist-code label-tooltip"><i class="icon-resize-full"></i></a>
        <a href="javascript:void(0)" title="{l s='Remove Code' mod='deotemplate'}" class="plist-eremove label-tooltip"><i class="icon-trash"></i></a>
        <a href="javascript:void(0)" title="{l s='Disable or Enable Code' mod='deotemplate'}" class="btn-status label-tooltip"><i class="{if isset($defaultItem)}icon-ok{else}{($eItem.form.active && isset($eItem.form.active)) ? 'icon-ok' : 'icon-remove'}{/if}"></i></a>
    </div>
    <div class="content-code">
        <textarea name="filecontent" rows="5" class="">{if !isset($defaultItem)}{$eItem.code nofilter}{/if}</textarea>
    </div>
</div>