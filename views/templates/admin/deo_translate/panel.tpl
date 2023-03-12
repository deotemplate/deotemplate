{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div id="translate-dashboard">
	{foreach from=$translate_file item=domain key=key}
		<form action="{$action_form}" method="post" class="form-translate" onsubmit="return false">
			<input type="hidden" name="domain" value="{$key}">
			<div class="panel">
				<h3 class="panel-heading">{$key}</h3>
				{foreach from=$domain item=trans_unit}
					<div class="form-group group-translate row">
						<label class="control-label source col-lg-12">{$trans_unit.source}</label>
						<div class="col-lg-12">
							<textarea name="target" data-id="{$trans_unit.id}" {if isset($trans_unit.id_translation) && $trans_unit.id_translation}data-id_translation="{$trans_unit.id_translation}"{/if} class="target" rows="3">{$trans_unit.target}</textarea>
						</div>
					</div>
				{/foreach}
				<div class="panel-footer">
					<button type="submit" name="submit-tranlsate" class="btn btn-default pull-right submit-tranlsate"><i class="process-icon-save"></i> {l s='Save' mod='deotemplate'}</button>
				</div>
			</div>
		</form>
	{/foreach}
</div>