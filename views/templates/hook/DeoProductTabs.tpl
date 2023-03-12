{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}


<div id="{$formAtts.form_id}" class="block deo-tabs {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}" 
	data-fade_effect="{(isset($formAtts.fade_effect) && $formAtts.fade_effect) ? 'true' : 'false'}" 
>
	<input type="hidden" name="data_form" class="data_form" value="{$data_form nofilter}">
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		<div class="box-title">
	{/if}
		{if isset($formAtts.title) && $formAtts.title}
			<h4 class="title_block">{$formAtts.title}</h4>
		{/if}
		{if isset($formAtts.sub_title) && $formAtts.sub_title}
			<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
		{/if}
	{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
		</div>
	{/if}
	<p class="box-select">
		<select class="product-tab-option form-control form-control-select">
			{foreach from=$tabs item=tab}
				<option value="{$tab.id}" {if $tab.active_tab}selected="selected"{/if}>{$tab.name}</option>
			{/foreach}
		</select>
	</p>

	{if $formAtts.tab_type =='tabs-left'}
		<div class="block_content">
			<i class="icon-loading"></i>
			<div class="row">
				<ul class="nav nav-tabs col-xxl-2-4 col-xl-3 col-lg-3 col-md-12 col-sm-12 col-xs-12 col-sp-12" role="tablist">
					{foreach from=$tabs item=tab}
						<li class="nav-item">
							<a href="#{$formAtts.form_id}_{$tab.id}" class="nav-link{if $tab.active_tab} active processed{/if}" role="tab" data-tab="{$tab.id}" aria-expanded="{if $tab.active_tab}true{else}false{/if}">{$tab.name}</a>
						</li>
					{/foreach}
				</ul>
				<div class="tab-content col-xxl-9-6 col-xl-9 col-lg-9 col-md-12 col-sm-12 col-xs-12 col-sp-12">
					{foreach from=$tabs item=tab}
						<div id="{$formAtts.form_id}_{$tab.id}" class="tab-pane{if $tab.active_tab} active in{/if}" aria-expanded="{if $tab.active_tab}true{else}false{/if}">
							{if $tab.active_tab}
								{if !empty($products)}
									{include file=$deo_helper->getTplTemplate('DeoProductSlickCarousel.tpl', $formAtts['override_folder'])}
								{else}
									<p class="alert alert-info">{l s='No products at this time.' mod='deotemplate'}</p>	
								{/if}
							{/if}
						</div>
					{/foreach}
				</div>
			</div>
		</div>
	{/if}
	{if $formAtts.tab_type =='tabs-right'}
		<div class="block_content">
			<i class="icon-loading"></i>
			<div class="row">
				<div class="tab-content col-xxl-9-6 col-xl-9 col-lg-9 col-md-12 col-sm-12 col-xs-12 col-sp-12">
					{foreach from=$tabs item=tab}
						<div id="{$formAtts.form_id}_{$tab.id}" class="tab-pane{if $tab.active_tab} active in{/if}" aria-expanded="{if $tab.active_tab}true{else}false{/if}">
							{if $tab.active_tab}
								{if !empty($products)}
									{include file=$deo_helper->getTplTemplate('DeoProductSlickCarousel.tpl', $formAtts['override_folder'])}
								{else}
									<p class="alert alert-info">{l s='No products at this time.' mod='deotemplate'}</p>	
								{/if}
							{/if}
						</div>
					{/foreach}
				</div>
				<ul class="nav nav-tabs col-xxl-2-4 col-xl-3 col-lg-3 col-md-12 col-sm-12 col-xs-12 col-sp-12" role="tablist">
					{foreach from=$tabs item=tab}
						<li class="nav-item">
							<a href="#{$formAtts.form_id}_{$tab.id}" class="nav-link{if $tab.active_tab} active processed{/if}" role="tab" data-tab="{$tab.id}" aria-expanded="{if $tab.active_tab}true{else}false{/if}">{$tab.name}</a>
						</li>
					{/foreach}
				</ul>
			</div>
		</div>
	{/if}
	{if $formAtts.tab_type =='tabs-below'}
		<div class="block_content">
			<i class="icon-loading"></i>
			<div class="tab-content">
				{foreach from=$tabs item=tab}
					<div id="{$formAtts.form_id}_{$tab.id}" class="tab-pane{if $tab.active_tab} active in{/if}" aria-expanded="{if $tab.active_tab}true{else}false{/if}">
						{if $tab.active_tab}
							{if !empty($products)}
								{include file=$deo_helper->getTplTemplate('DeoProductSlickCarousel.tpl', $formAtts['override_folder'])}
							{else}
								<p class="alert alert-info">{l s='No products at this time.' mod='deotemplate'}</p>	
							{/if}
						{/if}
					</div>
				{/foreach}
			</div>
			<ul class="nav nav-tabs" role="tablist">
				{foreach from=$tabs item=tab}
					<li class="nav-item">
						<a href="#{$formAtts.form_id}_{$tab.id}" class="nav-link{if $tab.active_tab} active processed{/if}" role="tab" data-tab="{$tab.id}" aria-expanded="{if $tab.active_tab}true{else}false{/if}">{$tab.name}</a>
					</li>
				{/foreach}
			</ul>
		</div>
	{/if}
	{if $formAtts.tab_type =='tabs-top'}
		<ul class="nav nav-tabs " role="tablist">
			{foreach from=$tabs item=tab}
				<li class="nav-item">
					<a href="#{$formAtts.form_id}_{$tab.id}" class="nav-link{if $tab.active_tab} active processed{/if}" role="tab" data-tab="{$tab.id}" aria-expanded="{if $tab.active_tab}true{else}false{/if}">{$tab.name}</a>
				</li>
			{/foreach}
		</ul>
		<div class="clearfix border-title"></div>
		<div class="block_content">
			<i class="icon-loading"></i>
			<div class="tab-content">
				{foreach from=$tabs item=tab}
					<div id="{$formAtts.form_id}_{$tab.id}" class="tab-pane{if $tab.active_tab} active in{/if}" aria-expanded="{if $tab.active_tab}true{else}false{/if}">
						{if $tab.active_tab}
							{if !empty($products)}
								{include file=$deo_helper->getTplTemplate('DeoProductSlickCarousel.tpl', $formAtts['override_folder'])}
							{else}
								<p class="alert alert-info">{l s='No products at this time.' mod='deotemplate'}</p>	
							{/if}
						{/if}
					</div>
				{/foreach}
			</div>
		</div>
	{/if}
</div>