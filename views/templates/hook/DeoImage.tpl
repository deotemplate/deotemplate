{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{function name=deo_content}
	{if isset($formAtts.url) && $formAtts.url}
		<a href="{$formAtts.url}" class="image" {(isset($formAtts.is_open) && $formAtts.is_open) ? 'target="_blank"' : ''}>
	{/if}
		{if isset($formAtts.image) && $formAtts.image}
			{if isset($formAtts.lazyload) && $formAtts.lazyload}
				<span class="lazyload-wrapper" style="padding-bottom: {$formAtts.rate_image};">
					<span class="lazyload-icon"></span>
				</span>
				<img data-src="{$formAtts.image}" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="img-fluid lazyload {(isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation) ? 'has-animation' : ''}"
					{if isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation} data-animation="{$formAtts.animation}" {/if}
					{if $formAtts.animation_delay != '' && $formAtts.animation_delay} data-animation-delay="{$formAtts.animation_delay}" {/if}
					title="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')}"
					alt="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')}" loading="lazy"/>
			{else}
				<img src="{$formAtts.image}" class="img-fluid {(isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation) ? 'has-animation' : ''}"
					{if isset($formAtts.animation) && $formAtts.animation != 'none' && $formAtts.animation} data-animation="{$formAtts.animation}" {/if}
					{if $formAtts.animation_delay != '' && $formAtts.animation_delay} data-animation-delay="{$formAtts.animation_delay}" {/if}
					title="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')}"
					alt="{((isset($formAtts.alt) && $formAtts.alt) ? $formAtts.alt : '')}" loading="lazy"/>
			{/if}
		{/if}
	{if isset($formAtts.url) && $formAtts.url}
		</a> 
	{/if}
	{if isset($formAtts.description) && $formAtts.description}
		<div class='image_description'>
			{($formAtts.description) ? $formAtts.description:'' nofilter}{* HTML form , no escape necessary *}
		</div>
	{/if}
{/function}

{if !isset($formAtts.accordion_type) || $formAtts.accordion_type == 'full'}{* Default : always full *}
	<div class="block {(isset($formAtts.class)) ? $formAtts.class : ''}{if isset($formAtts.sub_title) && $formAtts.sub_title} has-sub-title{/if}">
		<div class="media">
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				<div class="box-title">
			{/if}
				{if isset($formAtts.title) && $formAtts.title}
					<h4 class="title_block">{$formAtts.title nofilter}{* HTML form , no escape necessary *}</h4>
				{/if}
				{if isset($formAtts.sub_title) && $formAtts.sub_title}
					<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
				{/if}
			{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
				</div>
			{/if}
			{deo_content}
		</div>
	</div>
{elseif isset($formAtts.accordion_type) && ($formAtts.accordion_type == 'accordion' || $formAtts.accordion_type == 'accordion_small_screen' || $formAtts.accordion_type == 'accordion_mobile_screen')}
	<div class="block block-toggler {(isset($formAtts.class)) ? $formAtts.class : ''}{if $formAtts.accordion_type == 'accordion_small_screen'} accordion_small_screen{elseif $formAtts.accordion_type == 'accordion_mobile_screen'} accordion_mobile_screen{/if}{if isset($formAtts.sub_title) && $formAtts.sub_title} has-sub-title{/if}">
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			<div class="box-title">
		{/if}
			{if isset($formAtts.title) && $formAtts.title}
				<div class="title clearfix">
					<h4 class="title_block">{$formAtts.title nofilter}</h4>
					<span class="float-xs-right">
						<span class="navbar-toggler collapse-icons" data-target="#footer-html-{$formAtts.form_id}" data-toggle="collapse">
							<i class="add"></i>
							<i class="remove"></i>
						</span>
					</span>
				</div>
			{/if}
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			</div>
		{/if}
		<div class="collapse block_content" id="footer-html-{$formAtts.form_id}">
			{deo_content}
		</div>
	</div>
{/if}
