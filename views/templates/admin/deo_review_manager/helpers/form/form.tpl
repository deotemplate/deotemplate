{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{extends file="helpers/form/form.tpl"}

{block name="input"}
	{if $input.type == 'products'}
		<table id="{$input.name}" class="table">
			<thead>
				<tr>
					<th width="50px"></th>
					<th width="80px">ID</th>
					<th>{l s='Product Name' mod='deotemplate'}</th>
				</tr>
			</thead>
			<tbody>
				{foreach $input.values as $value}
					<tr>
						<td width="50px">
							<input type="checkbox" name="{$input.name}[]" value="{$value.id_product}" {if isset($value.selected) && $value.selected == 1} checked {/if}/>
		                    <i class="md-checkbox-control"></i>
						</td>
						<td width="80px">{$value.id_product}</td>
						<td>{$value.name}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
    {elseif $input.type == 'switch' && $smarty.const._PS_VERSION_|@addcslashes:'\'' < '1.6'}
		{foreach $input.values as $value}
			<input type="radio" name="{$input.name}" id="{$value.id}" value="{$value.value|escape:'html':'UTF-8'}"
					{if $fields_value[$input.name] == $value.value}checked="checked"{/if}
					{if isset($input.disabled) && $input.disabled}disabled="disabled"{/if} />
			<label class="t" for="{$value.id}">
			 {if isset($input.is_bool) && $input.is_bool == true}
				{if $value.value == 1}
					<img src="../img/admin/enabled.gif" alt="{$value.label}" title="{$value.label}" />
				{else}
					<img src="../img/admin/disabled.gif" alt="{$value.label}" title="{$value.label}" />
				{/if}
			 {else}
				{$value.label}
			 {/if}
			</label>
			{if isset($input.br) && $input.br}<br />{/if}
			{if isset($value.p) && $value.p}<p>{$value.p}</p>{/if}
		{/foreach}
	{else}
		{$smarty.block.parent}
    {/if}

{/block}
