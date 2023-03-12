{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="form-group tab_fields col-form-group col-lg-4 col-md-6 col-sm-6 col-xs-12">
	<fieldset class="customer-fields">
		<legend>
			{$label}
			<span class="reset-link">
				<a href="javascript:void(0)" data-section="account-fields" data-action="resetAccountFields">{l s='reset default' mod='deotemplate'}</a>
			</span>
		</legend>
		<table id="customer_fields" class="customer-fields table table-condensed table-striped">
			<thead>
				<tr>
					<th></th>
					<th>{l s='Name' mod='deotemplate'}</th>
					<th>{l s='Active' mod='deotemplate'}</th>
					<th>{l s='Required' mod='deotemplate'}</th>
					<th>{l s='Width' mod='deotemplate'}</th>
				</tr>
			</thead>
			<tbody>
			{foreach $fields as $field_name => $field_values}
				<tr class="{$field_name}">
					<td><i class="js-handle icon icon-move"></i></td>
					<td>{$field_name}<input type="hidden" name="field-name" value="{$field_name}"></td>
					{if $field_name == "State:name" || $field_name == "postcode"}
						<td colspan="3" style="text-align: center;">{l s='- managed automatically -' mod='deotemplate'}
							<input type="checkbox" style="display: none;" class="visible" name="visible" checked>
							<input type="checkbox" style="display: none;" class="required" name="required">
						</td>
						<td><input type="number" name="width" value="{$field_values.width}">
					{elseif $field_name == "psgdpr" || $field_name == "ps_dataprivacy"}
						<td colspan="2" style="text-align: center;">- managed by <b>{$field_name}</b> module -
							<input type="checkbox" style="display: none;" class="visible" name="visible" checked>
							<input type="checkbox" style="display: none;" class="required" name="required">
						</td>
						<td>
							{* <input type="number" name="width" value="{$field_values.width}"> *}
							<select class="width" name="width" {if !$field_values.visible}disabled{/if}>
								<option value="12" {if $field_values.width == '12'}selected{/if}>12/12 - (100%)</option>
								<option value="11" {if $field_values.width == '11'}selected{/if}>11/12 - (91.67%)</option>
								<option value="10" {if $field_values.width == '10'}selected{/if}>10/12 - (83.33%)</option>
								<option value="9-6" {if $field_values.width == '9-6'}selected{/if}>9-6/12 - (80%)</option>
								<option value="9" {if $field_values.width == '9'}selected{/if}>9/12 - (75%)</option>
								<option value="8" {if $field_values.width == '8'}selected{/if}>8/12 - (66.67%)</option>
								<option value="7-2" {if $field_values.width == '7-2'}selected{/if}>7-2/12 - (60%)</option>
								<option value="7" {if $field_values.width == '7'}selected{/if}>7/12 - (58.33%)</option>
								<option value="6" {if $field_values.width == '6'}selected{/if}>6/12 - (50%)</option>
								<option value="5" {if $field_values.width == '5'}selected{/if}>5/12 - (41.67%)</option>
								<option value="4-8" {if $field_values.width == '4-8'}selected{/if}>4-8/12 - (40%)</option>
								<option value="4" {if $field_values.width == '4'}selected{/if}>4/12 - (33.33%)</option>
								<option value="3" {if $field_values.width == '3'}selected{/if}>3/12 - (25%)</option>
								<option value="2-4" {if $field_values.width == '2-4'}selected{/if}>2-4/12 - (20%)</option>
								<option value="2" {if $field_values.width == '2'}selected{/if}>2/12 - (16.67%)</option>
								<option value="1" {if $field_values.width == '1'}selected{/if}>1/12 - (8.33%)</option>
							</select>
						</td>
					{else}
						<td><input type="checkbox"  class="visible" name="visible" {if $field_values.visible}checked{/if}></td>
						<td><input type="checkbox" class="required" name="required" {if $field_values.required}checked{/if} {if !$field_values.visible}disabled{/if}></td>
						<td>
							{* <input type="number" name="width" value="{$field_values.width}" {if !$field_values.visible}disabled{/if}> *}
							<select class="width" name="width" {if !$field_values.visible}disabled{/if}>
								<option value="12" {if $field_values.width == '12'}selected{/if}>12/12 - (100%)</option>
								<option value="11" {if $field_values.width == '11'}selected{/if}>11/12 - (91.67%)</option>
								<option value="10" {if $field_values.width == '10'}selected{/if}>10/12 - (83.33%)</option>
								<option value="9-6" {if $field_values.width == '9-6'}selected{/if}>9-6/12 - (80%)</option>
								<option value="9" {if $field_values.width == '9'}selected{/if}>9/12 - (75%)</option>
								<option value="8" {if $field_values.width == '8'}selected{/if}>8/12 - (66.67%)</option>
								<option value="7-2" {if $field_values.width == '7-2'}selected{/if}>7-2/12 - (60%)</option>
								<option value="7" {if $field_values.width == '7'}selected{/if}>7/12 - (58.33%)</option>
								<option value="6" {if $field_values.width == '6'}selected{/if}>6/12 - (50%)</option>
								<option value="5" {if $field_values.width == '5'}selected{/if}>5/12 - (41.67%)</option>
								<option value="4-8" {if $field_values.width == '4-8'}selected{/if}>4-8/12 - (40%)</option>
								<option value="4" {if $field_values.width == '4'}selected{/if}>4/12 - (33.33%)</option>
								<option value="3" {if $field_values.width == '3'}selected{/if}>3/12 - (25%)</option>
								<option value="2-4" {if $field_values.width == '2-4'}selected{/if}>2-4/12 - (20%)</option>
								<option value="2" {if $field_values.width == '2'}selected{/if}>2/12 - (16.67%)</option>
								<option value="1" {if $field_values.width == '1'}selected{/if}>1/12 - (8.33%)</option>
							</select>
						</td>
					{/if}
				</tr>
			{/foreach}
			</tbody>
		</table>
	</fieldset>
</div>

