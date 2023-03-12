{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div {if !isset($deoInfo)}id="default_column"{/if} class="{if !isset($deoInfo)}new-shortcode {/if}column-row{if !isset($deoInfo)} col-sp-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xxl-12{/if}{if isset($formAtts.class)} {$formAtts.class|escape:'html':'UTF-8'}{/if}{if isset($colClass)} {$colClass|replace:'.':'-'|escape:'html':'UTF-8'}{/if}{if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}{if isset($formAtts)} {$formAtts.form_id|escape:'html':'UTF-8'}{/if}" data-type="DeoColumn" data-class="{if isset($formAtts.class)}{$formAtts.class|escape:'html':'UTF-8'}{/if}{if isset($colClass)} {$colClass|replace:'.':'-'|escape:'html':'UTF-8'}{/if}">
	<div class="cover-column">
		<div class="column-controll-top clearfix">
			<div class="column-controll-top-left pull-left">
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag to sort Column' mod='deotemplate'}" class="column-action caction-drag label-tooltip"><i class="icon-move"></i> </a> Column
				{* <div class="btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<span>{l s='Column' mod='deotemplate'}</span> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu for-column-row" role="menu">
						<li><a href="javascript:void(0)" title="{l s='Add new Widget' mod='deotemplate'}" class="column-action btn-new-widget "><i class="icon-plus-sign"></i> {l s='Add new Widget' mod='deotemplate'}</a></li>
						<li><a href="javascript:void(0)" title="{l s='Edit Column' mod='deotemplate'}" class="column-action btn-edit " data-type="DeoColumn" data-for=".column-row"><i class="icon-cog"></i> {l s='Edit Column' mod='deotemplate'}</a></li>
						<li><a href="javascript:void(0)" title="{l s='Delete Column' mod='deotemplate'}" class="column-action btn-delete "><i class="icon-trash"></i> {l s='Delete Column' mod='deotemplate'}</a></li>
						<li><a href="javascript:void(0)" title="{l s='Duplicate Group' mod='deotemplate'}" class="column-action btn-duplicate "><i class="icon-paste"></i> {l s='Duplicate Column' mod='deotemplate'}</a></li>
						<li><a href="javascript:void(0)" title="{l s='Disable or Enable Column' mod='deotemplate'}" class="column-action btn-status {if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if}"><i class="{if isset($formAtts.active) && !$formAtts.active}icon-remove{else}icon-ok{/if}"></i> {l s='Disable or Enable Column' mod='deotemplate'}</a></li>
					</ul>
				</div>  *}
			</div>
			{if (int) DeoHelper::getConfig('ANIMATION')}
				<div class="btn-group animation-section">
					<button type="button" class="btn btn-default animation-button">
						<i class="icon-magic"></i>
						<span class="animation-status" data-text-default="{l s='Animation' mod='deotemplate'}" data-text-infinite="{l s='Infinite' mod='deotemplate'}">{l s='Animation' mod='deotemplate'}</span>
					</button>
					<div class="form-horizontal animation-wrapper column-animation-wrapper">
						<div class="form-group">
							<label class="control-label col-lg-5">
								{l s='Select Animation' mod='deotemplate'}
							</label>
							<div class="col-lg-7">
								<select name="animation" class="animation_select fixed-width-xl">
									{if isset($listAnimation)}
										{foreach $listAnimation as $listAnimation_val}
											<optgroup label="{$listAnimation_val.name}">
												{foreach $listAnimation_val.query as $option}
													<option value="{$option.id}">{$option.name}</option>
												{/foreach}
											</optgroup>
										{/foreach}
									{/if}
								</select>
							</div>
						</div>
						<div class="form-group animate_sub">
							<div class="col-lg-10 col-lg-offset-2">
								<div class="animationSandbox">Prestashop.com</div>								
							</div>
						</div>
						<div class="form-group animate_sub">
							<label class="control-label col-lg-5">
								{l s='Delay' mod='deotemplate'} ({l s='Default' mod='deotemplate'}: 1)
							</label>
							<div class="col-lg-7">						
								<div class="input-group fixed-width-xs">
									<input name="animation_delay" value="1" class="fixed-width-xs animation_delay" type="text">
									<span class="input-group-addon">{l s='s' mod='deotemplate'}</span>							
								</div>						
							</div>
						</div>
						<div class="form-group animate_sub">
							<label class="control-label col-lg-5">
								{l s='Duration' mod='deotemplate'} ({l s='Default' mod='deotemplate'}: 1)
							</label>
							<div class="col-lg-7">						
								<div class="input-group fixed-width-xs">
									<input name="animation_duration" value="1" class="fixed-width-xs animation_duration" type="text">
									<span class="input-group-addon">{l s='s' mod='deotemplate'}</span>							
								</div>						
							</div>
						</div>
						<div class="form-group animate_sub animate_loop">
							<label class="control-label col-lg-5">
								{l s='Iteration count' mod='deotemplate'} ({l s='Default' mod='deotemplate'}: 1)
							</label>
							<div class="col-lg-7">						
								<div class="input-group fixed-width-xs">
									<input name="animation_iteration_count" value="1" class="fixed-width-xs animation_iteration_count" type="text">
									<span class="input-group-addon">{l s='times' mod='deotemplate'}</span>							
								</div>							
							</div>
						</div>
						<div class="form-group animate_sub">
							<div class="col-lg-7 col-lg-offset-5">
								<div class="checkbox">
									<label for="animation_infinite">
										<input name="animation_infinite" class="checkbox-group animation_infinite" value="1" type="checkbox">{l s='Infinite' mod='deotemplate'}
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-lg-12">
								<button type="button" class="btn btn-primary pull-right btn-save-animation">{l s='Save' mod='deotemplate'}</button>
								<button type="button" class="btn btn-default pull-right animate-it animate_sub">{l s='Animate demo' mod='deotemplate'}</button>						
							</div>
						</div>
					</div>
				</div>
			{/if}
			<div class="column-controll-top-right pull-right">
				<a href="javascript:void(0)" data-toggle="tooltip"  title="{l s='Add new Widget' mod='deotemplate'}" class="column-action btn-new-widget label-tooltip"><i class="icon-plus-sign"></i></a>
				<a href="javascript:void(0)" data-toggle="tooltip"  title="{l s='Edit Column' mod='deotemplate'}" class="column-action btn-edit label-tooltip" data-type="DeoColumn" data-for=".column-row"><i class="icon-cog"></i></a>
				<a href="javascript:void(0)" data-toggle="tooltip"  title="{l s='Disable or Enable Column' mod='deotemplate'}" class="column-action btn-status {if isset($formAtts.active) && !$formAtts.active} deactive{else} active{/if} label-tooltip"><i class="{if isset($formAtts.active) && !$formAtts.active}icon-remove{else}icon-ok{/if}"></i></a>
				<a href="javascript:void(0)" data-toggle="tooltip"  title="{l s='Duplicate Group' mod='deotemplate'}" class="column-action btn-duplicate label-tooltip"><i class="icon-paste"></i></a>
				<a href="javascript:void(0)" data-toggle="tooltip"  title="{l s='Delete Column' mod='deotemplate'}" class="column-action btn-delete label-tooltip"><i class="icon-trash"></i></a>

				{* <span class="change-size-column">
					<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Reduce size' mod='deotemplate'}" class="column-action btn-change-colwidth" data-value="-1"><i class="icon-minus-sign-alt"></i></a>
					<div class="btn-group">
						<button type="button" class="btn" tabindex="-1" data-toggle="dropdown">
							<span class="width-val deo-w-6"></span>
						</button>
						<ul class="dropdown-menu dropdown-menu-right">
							{foreach from=$widthList item=itemWidth}
							<li class="col-{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}">
								<a class="change-colwidth" data-width="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}" href="javascript:void(0);" tabindex="-1">                                          
									<span data-width="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}" class="width-val deo-w-{if $itemWidth|strpos:"."|escape:'html':'UTF-8'}{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}{else}{$itemWidth|escape:'html':'UTF-8'}{/if}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</span>
								</a>
							</li>
							{/foreach}
						</ul>
					</div>
					<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Increase size' mod='deotemplate'}" class="column-action btn-change-colwidth" data-value="1"><i class="icon-plus-sign-alt"></i></a>
				</span> *}
			</div>
		</div>
		<div class="column-content">
			{if isset($deoInfo)}{$deo_html_content}{* HTML form , no escape necessary *}{/if}
		</div>
		<div class="column-controll-bottom">
			<span class="change-size-column">
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Reduce size' mod='deotemplate'}" class="column-action btn-change-colwidth" data-value="-1"><i class="icon-minus-sign-alt"></i></a>
				<div class="btn-group">
					<button type="button" class="btn" tabindex="-1" data-toggle="dropdown">
						<span class="width-val deo-w-6"></span>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						{foreach from=$widthList item=itemWidth}
						<li class="col-{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}">
							<a class="change-colwidth" data-width="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}" href="javascript:void(0);" tabindex="-1">                                          
								<span data-width="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}" class="width-val deo-w-{if $itemWidth|strpos:"."|escape:'html':'UTF-8'}{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}{else}{$itemWidth|escape:'html':'UTF-8'}{/if}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</span>
							</a>
						</li>
						{/foreach}
					</ul>
				</div>
				<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Increase size' mod='deotemplate'}" class="column-action btn-change-colwidth" data-value="1"><i class="icon-plus-sign-alt"></i></a>
			</span>
			<a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Add New Widget In Column' mod='deotemplate'}" class="column-action btn-new-widget label-tooltip"><i class="icon-plus"></i> {l s='Add New Widget In Column' mod='deotemplate'}</a>
			{* <a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Edit Column' mod='deotemplate'}" class="column-action btn-edit label-tooltip" data-type="DeoColumn"><i class="icon-cog"></i></a> *}
		</div>
	</div>
</div>