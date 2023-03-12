{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if isset($reloadModule) && $reloadModule}
	{foreach from=$listModule key=kshort item=item}
		<div class="item{if isset($col)} {$col}{/if}" data-tag="{if $item.author}{$item.author|escape:'html':'UTF-8'}{else}other{/if}">
			{if !$sidebar}
				<div class="cover-short-code">
					<a href="javascript:void(0)" title="{$item.name|escape:'html':'UTF-8'}" class="shortcode new-shortcode module" data-type="{$item.name|escape:'html':'UTF-8'}"></a>
			{/if}
					{include file='../deo_shortcodes/DeoModule.tpl' deoInfo=$item kshort=$kshort}
			{if !$sidebar}
				</div>
			{/if}
			{* <div class="cover-short-code">
				<a href="javascript:void(0)" title="{$item.name|escape:'html':'UTF-8'}" class="shortcode new-shortcode module" data-type="{$item.name|escape:'html':'UTF-8'}">
					<img class="icon" src="../modules/{$item.name|escape:'html':'UTF-8'}/logo.png"/>
					<h4 class="name">{$item.name|escape:'html':'UTF-8'}</h4>
					<p class="desc">{$item.description_short|escape:'html':'UTF-8'}</p>
				</a>
			</div> *}
		</div>
	{/foreach}
{else}
	{assign var=random_id value=10|mt_rand:200}
	<div class="nav-container">
		<ul class="nav nav-tabs admin-tabs tab-list" role="tablist">
			<li role="presentation" class="widget"><a href="#widget{$random_id}" aria-controls="widget{$random_id}" data-controls="widget" role="tab" data-toggle="tab">Widgets</a></li>
			<li role="presentation" class="module"><a href="#module{$random_id}" aria-controls="module{$random_id}" data-controls="module" role="tab" data-toggle="tab">Modules</a></li>
		</ul>
		<div class="tab-content nav-tag-widget">
			<div class="box-search-widget">
				<i class="icon-search"></i>
				<input type="text" class="txt-search" placeholder="{l s='Search' mod='deotemplate'}"/>
			</div>
			<div role="tabpanel" class="tab-pane tab-pane-widget active" id="widget{$random_id}">
				<ol class="breadcrumb in-widget filters for-widget" data-for="widget">
					<li><a href="javascript:void(0)" data-filter="*" class="is-checked all">All</a></li>
					<li><a href="javascript:void(0)" data-filter="image">Image</a></li>
					<li><a href="javascript:void(0)" data-filter="code">Code</a></li>
					<li><a href="javascript:void(0)" data-filter="content">Content</a></li>
					<li><a href="javascript:void(0)" data-filter="slider">Slider</a></li>
					<li><a href="javascript:void(0)" data-filter="social">Social</a></li>
				</ol>
				<div class="clearfix"></div>
				<div class="row widget_container container-fillter" data-col="item {$col}">
					{foreach from=$shortCodeList key=kshort item=shortCode}

						{if $kshort != 'DeoModule'}
							<div class="item{if isset($col)} {$col}{/if}" data-tag="{$shortCode.tag|escape:'html':'UTF-8'}">
								{if !$sidebar}
									<div class="cover-short-code">
										<a href="javascript:void(0)" title="{$shortCode.name|escape:'html':'UTF-8'}" class="shortcode new-shortcode" data-type='{$kshort|escape:'html':'UTF-8'}'></a>
								{/if}
										{if $kshort == 'DeoTabs'}
											{include file='../deo_shortcodes/DeoTabs.tpl' deoInfo=$shortCode kshort=$kshort}
										{elseif $kshort == 'DeoAccordions'}
											{include file='../deo_shortcodes/DeoAccordions.tpl' deoInfo=$shortCode kshort=$kshort}
										{elseif $kshort == 'DeoPopup'}
											{include file='../deo_shortcodes/DeoPopup.tpl' deoInfo=$shortCode kshort=$kshort}
										{else}
											{include file='../deo_shortcodes/DeoGeneral.tpl' deoInfo=$shortCode kshort=$kshort}
										{/if}
								{if !$sidebar}
									</div>
								{/if}
								{* <div class="cover-short-code">
									<a href="javascript:void(0)" title="{$shortCode.desc|escape:'html':'UTF-8'}" class="shortcode new-shortcode" data-type='{$kshort|escape:'html':'UTF-8'}'>
										<i class="icon {if isset($shortCode.icon_class)}{$shortCode.icon_class|escape:'html':'UTF-8'}{/if}"> </i>
										<h4 class="name">{$shortCode.label|escape:'html':'UTF-8'}</h4>
										<p class="desc">{$shortCode.desc|escape:'html':'UTF-8'}</p>
									</a>
								</div> *}
							</div>
						{/if} 
					{/foreach}
				</div>
			</div>
			<div role="tabpanel" class="tab-pane tab-pane-module" id="module{$random_id}">
				<ol class="breadcrumb in-widget filters for-module" data-for="module">
					<li><a href="javascript:void(0)" data-filter="*" class="is-checked all">{l s='All' mod='deotemplate'}</a></li>
					{foreach from=$author item=item}
						<li><a href="javascript:void(0)" data-filter="{$item|escape:'html':'UTF-8'}">{$item|escape:'html':'UTF-8'}</a></li>
					{/foreach}
					<li><a href="javascript:void(0)" data-filter="other">{l s='Other' mod='deotemplate'}</a></li>
				</ol>
				<div class="reload"><a href="javascript:void(0)"class="btn-new-widget reload-module">{l s='(Click here if you add new module or controller)' mod='deotemplate'}</a></div>
				<div class="row module_container container-fillter" data-col="item {$col}">
					{foreach from=$listModule key=kshort item=item}
						<div class="item{if isset($col)} {$col}{/if}" data-tag="{if $item.author}{$item.author|escape:'html':'UTF-8'}{else}other{/if}">
							{if !$sidebar}
								<div class="cover-short-code">
									<a href="javascript:void(0)" title="{$item.name|escape:'html':'UTF-8'}" class="shortcode new-shortcode module" data-type="{$item.name|escape:'html':'UTF-8'}"></a>
							{/if}
									{include file='../deo_shortcodes/DeoModule.tpl' deoInfo=$item kshort=$kshort}
							{if !$sidebar}
								</div>
							{/if}
							{* <div class="cover-short-code">
								<a href="javascript:void(0)" title="{$item.name|escape:'html':'UTF-8'}" class="shortcode new-shortcode module" data-type="{$item.name|escape:'html':'UTF-8'}">
									<img class="icon" src="../modules/{$item.name|escape:'html':'UTF-8'}/logo.png"/>
									<h4 class="name">{$item.name|escape:'html':'UTF-8'}</h4>
									<p class="desc">{$item.description_short|escape:'html':'UTF-8'}</p>
								</a>
							</div> *}
						</div>
					{/foreach}
				</div>
			</div>
		</div>
	</div>
{/if}