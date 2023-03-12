{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<ul id="list-slider" class="clearfix" data-inputs_lang="{$inputs_lang|@json_encode|escape:'html':'UTF-8' nofilter}" data-inputs="{$inputs|@json_encode|escape:'html':'UTF-8' nofilter}">
    {foreach from=$arr item=i}
        {if $i}
            <li id="{$i}" class="list-item {(isset($config_val.temp_active.$i) && $config_val.temp_active.$i) ? '' : 'disable'}">
                {foreach from=$languages item=lang}
                    {if $default_lang == $lang.id_lang}
                        <div class="col-lg-8 left-content">
                            <span class="img-preview">
                                {if $config_val.temp_image.$i.{$lang.id_lang} || $config_val.temp_image_link.$i.{$lang.id_lang}}
                                    <img class="img-thumbnail" src="
                                        {if (isset($config_val.temp_use_image_link.$i) && $config_val.temp_use_image_link.$i)}
                                            {$config_val.temp_image_link.$i.{$lang.id_lang}}
                                        {else}
                                            {$path}{$config_val.temp_image.$i.{$lang.id_lang}}
                                        {/if}
                                    ">
                                {/if}
                            </span>
                            <span class="name-preview">
                                {if $config_val.temp_title.$i.{$lang.id_lang}}
                                    {$config_val.temp_title.$i.{$lang.id_lang}}
                                {/if}
                            </span>
                        </div>
                        <div class="col-lg-4 right-button">
                            <button class="btn-edit-level2 btn btn-info" type="button"><i class="icon-pencil"></i>
                                {* {l s='Edit' mod='deotemplate'} *}
                            </button>
                            <button class="btn-delete-level2 btn btn-danger" type="button"><i class="icon-trash"></i>
                                {* {l s='Delete' mod='deotemplate'} *}
                            </button>
                            <button class="btn-duplicate-level2 btn btn-success" type="button"><i class="icon-copy"></i> 
                                {* {l s='Duplicate' mod='deotemplate'} *}
                            </button>
                        </div>
                    {/if}

                    {assign var=temp_name value="{$i}_{$lang.id_lang}"}
                    
                    {foreach from=$inputs_lang item=input}
                        {if isset($config_val.$input.$i.{$lang.id_lang})}
                            <input type="hidden" id="{$input}_{$temp_name}" class="multiple-lang" data-name="{$input}" data-lang="{$lang.id_lang}" value="{$config_val.$input.$i.{$lang.id_lang}|escape:'html':'UTF-8'}" name="{$input}_{$temp_name}"/>
                        {/if}
                    {/foreach}
                    
                {/foreach}
                
                {foreach from=$inputs item=input}
                    {if isset($config_val.$input.$i)}
                        <input type="hidden" id="{$input}_{$i}" class="no-lang" data-name="{$input}" value="{$config_val.$input.$i}" name="{$input}_{$i}"/>
                    {/if}
                {/foreach}
            </li>
        {/if}
    {/foreach}
</ul>
<ul id="temp-list" class="hide clearfix">
    <li id="" class="list-item">
        <div class="col-lg-8 left-content">
            <span class="img-preview"></span>
            <span class="label-preview"></span>
        </div>
        <div class="col-lg-4 right-button">
            <button class="btn-edit-level2 btn btn-info" type="button"><i class="icon-pencil"></i>{*  {l s='Edit' mod='deotemplate'} *}</button>
            <button class="btn-delete-level2 btn btn-danger" type="button"><i class="icon-trash"></i>{*  {l s='Delete' mod='deotemplate'} *}</button>
            <button class="btn-duplicate-level2 btn btn-success" type="button"><i class="icon-copy"></i>{*  {l s='Duplicate' mod='deotemplate'} *}</button>
        </div>
    </li>
</ul>
<p class="help-block">{l s='Drag slide to sort position slide in Front End.' mod='deotemplate'}</p>