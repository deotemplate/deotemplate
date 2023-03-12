{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{function name=deo_twitter}
	<div id="{$formAtts.form_id}" class="deo-twitter" 
		data-border_color="{(isset($formAtts.border_color) && $formAtts.border_color) ? $formAtts.border_color : ''}" 
		data-show_border="{(isset($formAtts.show_border) && $formAtts.show_border) ? $formAtts.border_color : ''}" 
		data-name_color="{(isset($formAtts.name_color) && $formAtts.name_color) ? $formAtts.name_color : ''}" 
		data-mail_color="{(isset($formAtts.mail_color) && $formAtts.mail_color) ? $formAtts.mail_color : ''}" 
		data-text_color="{(isset($formAtts.text_color) && $formAtts.text_color) ? $formAtts.text_color : ''}" 
		data-link_color="{(isset($formAtts.link_color) && $formAtts.link_color) ? $formAtts.link_color : ''}" 
	>
		<a class="twitter-timeline" href="https://twitter.com/{$formAtts.username}"
			data-width="{$formAtts.width|intval}"
			data-height="{$formAtts.height|intval}"
			data-dnt="true"
			{* data-widget-id="{$formAtts.twidget_id}" *}
			{if isset($formAtts.link_color) && $formAtts.link_color}data-link-color="{$formAtts.link_color}"{/if}
			{if isset($formAtts.border_color) && $formAtts.border_color}data-border-color="{$formAtts.border_color}"{/if}
			{if isset($formAtts.limit) && $formAtts.limit && isset($formAtts.show_scrollbar) && !$formAtts.show_scrollbar}data-tweet-limit="{$formAtts.limit|intval}"{/if}
			data-show-replies="{if isset($formAtts.show_replies) && $formAtts.show_replies}true{else}false{/if}"
			data-chrome="{if isset($formAtts.show_backgroud) && !$formAtts.show_backgroud} transparent{/if} 
						{if isset($formAtts.show_scrollbar) && !$formAtts.show_scrollbar} noscrollbar{/if}
						{if isset($formAtts.show_border) && !$formAtts.show_border} noborders{/if}
						{if isset($formAtts.show_header) && !$formAtts.show_header} noheader{/if}
						{if isset($formAtts.show_footer) && !$formAtts.show_footer} nofooter{/if}"
		>{l s='Tweets by' mod='deotemplate'} {$formAtts.username}</a>
	</div>
{/function}

{if !isset($formAtts.accordion_type) || $formAtts.accordion_type == 'full'}{* Default : always full *}
	<div class="block widget-twitter {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
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
		<div class="block_content">
			{deo_twitter}
		</div>
	</div>
{elseif isset($formAtts.accordion_type) && ($formAtts.accordion_type == 'accordion' || $formAtts.accordion_type == 'accordion_small_screen' || $formAtts.accordion_type == 'accordion_mobile_screen')}
	<div class="block widget-twitter block-toggler {(isset($formAtts.class)) ? $formAtts.class : ''}{if $formAtts.accordion_type == 'accordion_small_screen'} accordion_small_screen{elseif $formAtts.accordion_type == 'accordion_mobile_screen'} accordion_mobile_screen{/if}{if isset($formAtts.sub_title) && $formAtts.sub_title} has-sub-title{/if}">
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			<div class="box-title">
		{/if}
			{if isset($formAtts.title) && $formAtts.title}
				<div class="title clearfix">
					<h4 class="title_block">{$formAtts.title}</h4>
					<span class="navbar-toggler collapse-icons" data-target="#deo-twitter{$formAtts.twidget_id}" data-toggle="collapse">
						<i class="add"></i>
						<i class="remove"></i>
					</span>
				</div>
			{/if}
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			</div>
		{/if}
		<div class="collapse block_content" id="deo-twitter{$formAtts.twidget_id}">
			{deo_twitter}
		</div>
	</div>
{/if}
