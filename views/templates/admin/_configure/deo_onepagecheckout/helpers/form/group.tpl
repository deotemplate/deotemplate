{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if isset($defaultGroup)}
	{$data = $dataDefaultGroup|json_decode}
{else}
	{$data = $gridElement.dataForm|json_decode}
{/if}
{$class = $data->class}

<div {if isset($defaultGroup)}id="default_group" {/if}class="group-row plist-element {($class) ? $class : 'row'} {if isset($defaultGroup)}{var_dump($dataDefaultGroup)}{else}{$gridElement.dataForm|escape:'html':'UTF-8'}{/if} {if isset($defaultGroup)}hide active{else}{($gridElement.form.active && isset($gridElement.form.active)) ? 'active' : 'deactive'}{/if}" data-element='group' data-form="{if isset($defaultGroup)}{$dataDefaultGroup|escape:'html':'UTF-8'}{else}{$gridElement.dataForm|escape:'html':'UTF-8'}{/if}">
	<div class="group-controll-left pull-left">
		<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag to sort row' mod='deotemplate'}" class="group-action gaction-drag label-tooltip"><i class="icon-move"></i></a> Row
		{* <div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span>{l s='Row' mod='deotemplate'}</span> <span class="caret"></span>
			</button>
			<ul class="dropdown-menu for-group-row">
				<li><a href="javascript:void(0)" title="{l s='Edit Row' mod='deotemplate'}" class="column-action btn-edit-group" data-type="DeoColumn" data-for=".column-row"><i class="icon-pencil"></i> {l s='Edit Row' mod='deotemplate'}</a></li>
				<li><a class="plist-eremove"> <i class="icon-trash"></i> {l s='Delete Row' mod='deotemplate'}</a></li>
				<li><a href="javascript:void(0)" title="{l s='Duplicate Row' mod='deotemplate'}" class="btn-duplicate"><i class="icon-paste"></i> {l s='Duplicate Column' mod='deotemplate'}</a></li>
				<li><a href="javascript:void(0)" title="{l s='Disable or Enable Row' mod='deotemplate'}" class="btn-status"><i class="{if isset($defaultGroup)}icon-ok{else}{($gridElement.form.active && isset($gridElement.form.active)) ? 'icon-ok' : 'icon-remove'}{/if}"></i> {l s='Disable or Enable Column' mod='deotemplate'}</a></li>
			</ul>
		</div> *}
	</div>
	<div class="group-controll-right pull-right">
		<span class="group-action btn-group label-tooltip" data-toggle="tooltip" title="{l s='Add New Column' mod='deotemplate'}">
			<button class="btn-add-column dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"><i class="icon-plus"></i> </button>
			<ul class="dropdown-menu dropdown-menu-right for-group-row">
				{for $i=1 to 6}
					<li>
						<a href="javascript:void(0);" data-col="{$i|escape:'html':'UTF-8'}" data-width="{(12/$i)|replace:'.':'-'|escape:'html':'UTF-8'}" class="column-add">
							<span class="width-val deo-w-{$i|escape:'html':'UTF-8'}">{$i|escape:'html':'UTF-8'} {l s='column per row' mod='deotemplate'} - ({(100/$i)|string_format:"%.2f"}%)</span>
						</a>
					</li>
				{/for}
			</ul>
		</span>
		<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Edit Row' mod='deotemplate'}" class="group-action btn-edit-group label-tooltip" data-type="DeoColumn" data-for=".column-row"><i class="icon-pencil"></i></a>
		<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Duplicate Row' mod='deotemplate'}" class="group-action btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
		<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Disable or Enable Row' mod='deotemplate'}" class="group-action btn-status label-tooltip"><i class="{if isset($defaultGroup)}icon-ok{else}{($gridElement.form.active && isset($gridElement.form.active)) ? 'icon-ok' : 'icon-remove'}{/if}"></i></a>
		<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Remove Row' mod='deotemplate'}" class="group-action plist-eremove label-tooltip"> <i class="icon-trash"></i></a>
	</div>
	<div class="group-content sort-content clearfix">
		{if !isset($defaultGroup)}
			{foreach $gridElement.columns item=column}
				{include file='./column.tpl' column=$column}
			{/foreach}
		{/if}
	</div>
</div>