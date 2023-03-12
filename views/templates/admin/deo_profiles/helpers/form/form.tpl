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