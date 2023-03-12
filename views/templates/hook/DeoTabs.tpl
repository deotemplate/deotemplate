{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if $tab_name == 'DeoTabs'}
	<div id="{$formAtts.id}" class="deo-tabs {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}" 
		data-fade_effect="{(isset($formAtts.fade_effect) && $formAtts.fade_effect) ? 'true' : 'false'}" 
	>
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
				{foreach from=$subTabContent item=subTab name=tab_option}
					<option value="{$subTab.id}" {if isset($subTab.active_tab) && $subTab.active_tab}selected="selected"{/if}>{$subTab.title}</option>
				{/foreach}
			</select>
		</p>
		{if $formAtts.tab_type =='tabs-left'}
			<div class="block_content">
				<div class="row">
					<ul class="nav nav-tabs col-xxl-2-4 col-xl-3 col-lg-3 col-md-12 col-sm-12 col-xs-12 col-sp-12" role="tablist">
						{foreach from=$subTabContent item=subTab name=foo}
							<li class="nav-item {(isset($subTab.css_class)) ? $subTab.css_class : ''}">
								<a href="#{$subTab.id}" class="nav-link {$subTab.form_id}{if isset($subTab.active_tab) && $subTab.active_tab} active{/if}" role="tab" data-tab="{$subTab.id}" data-toggle="tab" aria-expanded="{if isset($subTab.active_tab) && $subTab.active_tab}true{else}false{/if}">
									{if isset($subTab.image) && $subTab.image}
										<div class="left-block">
											<img class="img-fluid" src="{$path}{$subTab.image}" alt="{$subTab.title}"/>
										</div>
									{/if}
									<div class="right-block">
										{$subTab.title}
										{if isset($subTab.sub_title) && $subTab.sub_title}
											<span class="sub-title-widget">{$subTab.sub_title nofilter}</span>
										{/if}
									</div>
								</a>
							</li>
						{/foreach}
					</ul>
					<div class="tab-content col-xxl-9-6 col-xl-9 col-lg-9 col-md-12 col-sm-12 col-xs-12 col-sp-12">
						{$deo_html_content nofilter}{* HTML form , no escape necessary *}
					</div>
				</div>
			</div>
		{/if}
		{if $formAtts.tab_type =='tabs-right'}
			<div class="block_content">
				<div class="row">
					<div class="tab-content col-xxl-9-6 col-xl-9 col-lg-9 col-md-12 col-sm-12 col-xs-12 col-sp-12">
						{$deo_html_content nofilter}{* HTML form , no escape necessary *}
					</div>
					<ul class="nav nav-tabs col-xxl-2-4 col-xl-3 col-lg-3 col-md-12 col-sm-12 col-xs-12 col-sp-12" role="tablist">
						{foreach from=$subTabContent item=subTab name=foo}
							<li class="nav-item {(isset($subTab.css_class)) ? $subTab.css_class : ''}">
								<a href="#{$subTab.id}" class="nav-link {$subTab.form_id}{if isset($subTab.active_tab) && $subTab.active_tab} active{/if}" role="tab" data-tab="{$subTab.id}" data-toggle="tab" aria-expanded="{if isset($subTab.active_tab) && $subTab.active_tab}true{else}false{/if}">
									{if isset($subTab.image) && $subTab.image}
										<div class="left-block">
											<img class="img-fluid" src="{$path}{$subTab.image}" alt="{$subTab.title}"/>
										</div>
									{/if}
									<div class="right-block">
										{$subTab.title}
										{if isset($subTab.sub_title) && $subTab.sub_title}
											<span class="sub-title-widget">{$subTab.sub_title nofilter}</span>
										{/if}
									</div>
								</a>
							</li>
						{/foreach}
					</ul>
				</div>
			</div>
		{/if}
		{if $formAtts.tab_type =='tabs-below'}
			<div class="block_content">
				<div class="tab-content">
					{$deo_html_content nofilter}{* HTML form , no escape necessary *}
				</div>
				<ul class="nav nav-tabs" role="tablist">
					{foreach from=$subTabContent item=subTab name=foo}
						<li class="nav-item {(isset($subTab.css_class)) ? $subTab.css_class : ''}">
							<a href="#{$subTab.id}" class="nav-link {$subTab.form_id}{if isset($subTab.active_tab) && $subTab.active_tab} active{/if}" role="tab" data-tab="{$subTab.id}" data-toggle="tab" aria-expanded="{if isset($subTab.active_tab) && $subTab.active_tab}true{else}false{/if}">
								{if isset($subTab.image) && $subTab.image}
									<div class="left-block">
										<img class="img-fluid" src="{$path}{$subTab.image}" alt="{$subTab.title}"/>
									</div>
								{/if}
								<div class="right-block">
									{$subTab.title}
									{if isset($subTab.sub_title) && $subTab.sub_title}
										<span class="sub-title-widget">{$subTab.sub_title nofilter}</span>
									{/if}
								</div>
							</a>
						</li>
					{/foreach}
				</ul>
			</div>
		{/if}
		{if $formAtts.tab_type =='tabs-top'}
			<ul class="nav nav-tabs " role="tablist">
				{foreach from=$subTabContent item=subTab name=foo}
					<li class="nav-item {(isset($subTab.css_class)) ? $subTab.css_class : ''}">
						<a href="#{$subTab.id}" class="nav-link {$subTab.form_id}{if isset($subTab.active_tab) && $subTab.active_tab} active{/if}" role="tab" data-tab="{$subTab.id}" data-toggle="tab" aria-expanded="{if isset($subTab.active_tab) && $subTab.active_tab}true{else}false{/if}">
							{if isset($subTab.image) && $subTab.image}
								<div class="left-block">
									<img class="img-fluid" src="{$path}{$subTab.image}" alt="{$subTab.title}"/>
								</div>
							{/if}
							<div class="right-block">
								{$subTab.title}
								{if isset($subTab.sub_title) && $subTab.sub_title}
									<span class="sub-title-widget">{$subTab.sub_title nofilter}</span>
								{/if}
							</div>
						</a>
					</li>
				{/foreach}
			</ul>
			<div class="clearfix border-title"></div>
			<div class="block_content">
				<div class="tab-content">
					{$deo_html_content nofilter}{* HTML form , no escape necessary *}
				</div>
			</div>
		{/if}
	</div>
{/if}

{if $tab_name == 'DeoTab'}
	<div id="{$tabID}" class="tab-pane{if isset($formAtts.active_tab) && $formAtts.active_tab} active in{/if}" aria-expanded="{if isset($formAtts.active_tab) && $formAtts.active_tab}true{else}false{/if}">
		{$deo_html_content nofilter}{* HTML form , no escape necessary *}
	</div>
{/if}
