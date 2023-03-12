{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if !isset($formAtts.accordion_type) || $formAtts.accordion_type == 'full'}
	<div class="block {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			<div class="box-title">
		{/if}
			{if isset($formAtts.title) && $formAtts.title}
				<h4 class="title_block">{$formAtts.title nofilter}</h4>
			{/if}
			{if isset($formAtts.sub_title) && $formAtts.sub_title}
				<div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
			{/if}
		{if (isset($formAtts.title) && $formAtts.title) || (isset($formAtts.sub_title) && $formAtts.sub_title)}
			</div>
		{/if}
		{if isset($formAtts.content_html)}
			<div class="block_content">
				{$formAtts.content_html nofilter}
			</div>
		{/if}
	</div>
{elseif isset($formAtts.accordion_type) && ($formAtts.accordion_type == 'accordion' || $formAtts.accordion_type == 'accordion_small_screen' || $formAtts.accordion_type == 'accordion_mobile_screen')}
	<div class="block block-toggler {(isset($formAtts.class)) ? $formAtts.class : ''} {if $formAtts.accordion_type == 'accordion_small_screen'} accordion_small_screen{elseif $formAtts.accordion_type == 'accordion_mobile_screen'} accordion_mobile_screen{/if}{if isset($formAtts.sub_title) && $formAtts.sub_title} has-sub-title{/if}">
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
			{if isset($formAtts.content_html)}
				{$formAtts.content_html nofilter}
			{/if}
		</div>
	</div>
{/if}