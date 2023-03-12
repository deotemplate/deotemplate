{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{extends file="helpers/form/form.tpl"}
{block name="label"}
    {$smarty.block.parent}
{/block}
{block name="field"}
    {if $input.type == 'tabConfig'}
        <div class="row">
            {assign var=tabList value=$input.values}
            <ul class="nav nav-tabs admin-tabs tab-config-admin" role="tablist">
                {foreach $tabList as $key => $value name="tabList"}
                    <li role="presentation" class="tabConfig {if $key == $input.default}active{/if}">
                        <a href="#{$key|escape:'html':'UTF-8'}" class="deo-tab-config" role="tab" data-toggle="tab" data-value="{$key|escape:'html':'UTF-8'}">{$value|escape:'html':'UTF-8'}</a>
                    </li>
                {/foreach}
            </ul>
        </div>
            
        <input type="hidden" id="tab_open" name="tab_open" value="{$input.default}">
        <script type="text/javascript">
            $(document).ready(function(){
                $('.deo-tab-config').click(function(){
                    $('#tab_open').val( $(this).data('value') );
                });
            });
            
            $(document).on('click', '#configuration_form_submit_btn', function(e){
                e.preventDefault();
                var active_tab = $('.form-wrapper ul.nav-tabs li.active a').data('value');
                $('#tab_open').val( active_tab );
                $(this).closest('form').submit();
            });
        </script>
	{elseif $input.type == 'modules_block'}
        {assign var=moduleList value=$input.values}
        {if isset($input.exist_module) && !$input.exist_module}
            <label class="control-label" style="color: red; margin-bottom: 15px; margin-left: 10px;"> {l s='Empty module because not exist file config.xml in theme folder.' mod='deotemplate'}</label>
            <br />
        {/if}
        <div class="col-lg-8 ">
        {if isset($moduleList) && count($moduleList) > 0}
            <p class="help-block">{l s='You can select one or more Module.' mod='deotemplate'}</p>
            <table cellspacing="0" cellpadding="0" class="table" style="min-width:40em;">
                <tr>
                    <th>
                        <input type="checkbox" name="checkme" id="checkme" class="noborder" onclick="checkDelBoxes(this.form, '{$input.name|escape:'html':'UTF-8'}[]', this.checked)" />
                    </th>
                    <th>{l s='Name' mod='deotemplate'}</th>
                    <th>{l s='Back-up File' mod='deotemplate'}
                        <p class="help-block" style="display: inline;">
                        {$backup_dir}
                        </p>
                    </th>
                </tr>

                {foreach from=$moduleList item=module name=moduleItem}
                    <tr {if $smarty.foreach.moduleItem.index % 2}class="alt_row"{/if}>
                        <td> 
                            <input type="checkbox" class="cmsBox" name="{$input.name|escape:'html':'UTF-8'}[]" id="chk_{$module.name|escape:'html':'UTF-8'}" value="{$module.name|escape:'html':'UTF-8'}"/>
                        </td>
                        <td><label for="chk_{$module.name|escape:'html':'UTF-8'}" class="t"><strong>{$module.name|escape:'html':'UTF-8'}</strong></label></td>
                        <td>
                            {if isset($module.files)}
                            <select style="max-width: 500px;" name="file_{$module.name|escape:'html':'UTF-8'}">
                            {foreach from=$module.files item=file name=Modulefile}
                                <option value="{$file|escape:'html':'UTF-8'}">{$file|escape:'html':'UTF-8'}</option>
                            {/foreach}
                            </select>
                            {/if}
                        </td>
                    </tr>
                {/foreach}

            </table>
        {/if}
        </div>
        <div class="form-group button-wrapper">
            <div class="col-lg-8 col-lg-offset-4">
                <button class="button btn btn-success" name="submitSample" id="submitSample" type="submit">
                    {l s='Export Sample Data' mod='deotemplate'}
                </button>
                <button class="button btn btn-danger" name="submitImport" data-confirm="{l s='Are you sure you want to restore data sample of template. You will lost all data of module' mod='deotemplate'}" id="submitImport" type="submit">
                    {l s='Restore Sample Data' mod='deotemplate'}
                </button>
                <p class="help-block">{l s='Data Sample is only for theme developer' mod='deotemplate'}</p>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-8 col-lg-offset-4">
                <div class="alert alert-info">
                    {l s='With restore function, you will lost all data of module in site for all shop' mod='deotemplate'}
                    <hr>
                    {l s='You should back-up before do any thing' mod='deotemplate'}
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-8 col-lg-offset-4">
                <button class="button btn btn-success" name="submitExportDBStruct" id="submitExportDBStruct" type="submit">
                    {l s='Export Data Struct' mod='deotemplate'}
                </button>
                <p class="help-block">
                    {$input.folder_data_struct}
                </p>
            </div>
        </div>
            
        <div class="form-group">
            <div class="col-lg-8 col-lg-offset-4">
                <button class="button btn btn-success" name="submitUpdateModule" id="submitUpdateModule" type="submit" onclick="javascript:return confirm(text_confirm_correct)">
                        <i class="icon-AdminParentPreferences"></i> {l s='Update and Correct Module' mod='deotemplate'}
                </button>
                <button class="button btn btn-info" name="submitImportDataHosting" id="submitImportDataHosting" type="submit">
                    <i class="icon-cloud-upload"></i> {l s='Import Data Hosting' mod='deotemplate'}
                </button>
            </div>
        </div>
            
        <script type="text/javascript">
            var text_confirm_correct = "{l s='Are you sure you want to Update and Correct Module. Please back-up all things before?' mod='deotemplate'}";
            $(".button-wrapper .button").click(function(){
                hasCheckedE = 0;
                $("[name='moduleList[]']").each(function(){
                    if($(this).is(":checked")){
                        hasCheckedE = 1;
                        return false;
                    }
                });
                if(!hasCheckedE){
                    alert("You have to select atleast one module");
                    return false;
                }
                dataConfirm = $(this).attr("data-confirm");
                if(dataConfirm){
                    return confirm(dataConfirm);
                }
            });
        </script>
    {elseif $input.type == 'color'}
        <div class="col-lg-8">
            <div class="input-group colorpicker-element fixed-width-xxl">
                <input type="text" class="color-picker form-control" name="{$input.name}" value="{if isset($fields_value[$input.name]) && ($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'} {/if}">
                <span class="input-group-addon"><i></i></span>
            </div>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}