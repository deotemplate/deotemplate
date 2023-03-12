{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
<div class="widget-row box plist-element {if isset($defaultItem)}active{else}{($eItem.form.active && isset($eItem.form.active)) ? 'active' : 'deactive'}{/if}" data-element='box' data-form="{$eItem.dataForm|escape:'html':'UTF-8'}">
    <a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag me' mod='deotemplate'}" class="widget-action waction-drag label-tooltip"><i class="icon-move"></i></a> Group
    <div class="group-class-css">
        <input type="text" placeholder="{l s='Class CSS box' mod='deotemplate'}" name="css" class="form-control css" value="{(isset($defaultItem)) ? '' : $eItem.form.css}">
    </div>
    <div class="widget-controll-top pull-right">
        <a href="javascript:void(0)" title="{l s='Remove Element' mod='deotemplate'}" class="plist-eremove label-tooltip"><i class="icon-trash"></i></a>
        <a href="javascript:void(0)" title="{l s='Disable or Enable Element' mod='deotemplate'}" class="btn-status label-tooltip"><i class="{if isset($defaultItem)}icon-ok{else}{($eItem.form.active && isset($eItem.form.active)) ? 'icon-ok' : 'icon-remove'}{/if}"></i></a>
    </div>
    <div class="box-content sort-content clearfix">
    	{if !isset($defaultItem)}
            {foreach $eItem.element item=eItem}
                {include file='./element.tpl' eItem=$eItem}
            {/foreach}
        {/if}
    </div>
</div>