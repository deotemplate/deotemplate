{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{*form for group*}
<div id="form_content" style="display:none;" data-select="{l s='You are sure data saved, before select other profile?' mod='deotemplate'}" data-delete="{l s='Are you sure you want to delete?' mod='deotemplate'}" data-reduce="{l s='Minimum value of width is 1' mod='deotemplate'}" data-increase="{l s='Maximum value of width is 12' mod='deotemplate'}">
    <a id="export_process" href="" title="{l s='Export Process' mod='deotemplate'}" download='group.txt' target="_blank" >{l s='Export Process' mod='deotemplate'}</a>
    <div id="addnew-group-form">
        <ul class="list-group dropdown-menu">
            {foreach from=$widthList item=itemWidth}
                <li>
                    <a href="javascript:void(0);" data-width="{$itemWidth|escape:'html':'UTF-8'}" class="number-column">
                        <span class="width-val deo-w-{if $itemWidth|strpos:"."|escape:'html':'UTF-8'}{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}{else}{$itemWidth|escape:'html':'UTF-8'}{/if}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</span>
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>
    <div id="addnew-column-form">
        <ul class="list-group dropdown-menu">
            {for $i=1 to 6}
                  <li>
                      <a href="javascript:void(0);" data-col="{$i|escape:'html':'UTF-8'}" data-width="{(12/$i)|replace:'.':'-'|escape:'html':'UTF-8'}" class="column-add">
                          <span class="width-val deo-w-{$i|escape:'html':'UTF-8'}">{$i|escape:'html':'UTF-8'} {l s='column per row' mod='deotemplate'} - ({(100/$i)|string_format:"%.2f"}%)</span>
                      </a>
                  </li>
            {/for}
        </ul>
    </div>
    <div id="addnew-widget-group-form">
        <ul class="list-group dropdown-menu">
            <li>
                <a href="javascript:void(0);" data-col="0" data-width="0" class="group-add">
                    <span class="width-val deo-w-0">{l s='Create a group blank' mod='deotemplate'}</span>
                </a>
            </li>
            {for $i=1 to 6}
              <li>
                  <a href="javascript:void(0);" data-col="{$i|escape:'html':'UTF-8'}" data-width="{(12/$i)|escape:'html':'UTF-8'}" class="group-add">
                      <span class="width-val deo-w-{$i|escape:'html':'UTF-8'}">{$i|escape:'html':'UTF-8'} {l s='column per row' mod='deotemplate'} - ({(100/$i)|string_format:"%.2f"}%)</span>
                  </a>
              </li>
            {/for}
        </ul>
    </div>
    {foreach from=$shortcodeForm item=sform}
        {include file=$sform}
    {/foreach}
</div>


<div class="modal fade" id="modal_form"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
        <span class="sr-only">{l s='Close' mod='deotemplate'}</span></button>
        <h4 class="modal-title" id="myModalLabel" data-addnew="{l s='Add new Widget' mod='deotemplate'}" data-edit="{l s='Editting' mod='deotemplate'}"></h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-back-to-list pull-left">{l s='Back to List' mod='deotemplate'}</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='deotemplate'}</button>
        <button type="button" class="btn btn-primary btn-savewidget">{l s='Save changes' mod='deotemplate'}</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal_select_image" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
        <span class="sr-only">{l s='Close' mod='deotemplate'}</span></button>
        <h4 class="modal-title2">{l s='Image manager' mod='deotemplate'}</h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='deotemplate'}</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal_import" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
        <span class="sr-only">{l s='Close' mod='deotemplate'}</span></button>
        <h4 class="modal-title2">{l s='Import' mod='deotemplate'}</h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-savewidget">{l s='Import' mod='deotemplate'}</button>
      </div>
    </div>
  </div>
</div>