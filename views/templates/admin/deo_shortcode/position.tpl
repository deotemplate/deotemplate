{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div class="position-area">
	<div class="hook-wrapper deoshortcode {if isset($data_shortcode_content.deoshortcode.class)}{$data_shortcode_content.deoshortcode.class|escape:'html':'UTF-8'}{/if}" data-hook="deoshortcode">
		<div class="hook-top">
			<div class="pull-left hook-desc"></div>
			<div class="hook-info text-center">
				<a href="javascript:;" tabindex="0" class="open-group label-tooltip" id="deoshortcode" name="deoshortcode">
					{l s='Shortcode Content' mod='deotemplate'} <i class="icon-arrow-down"></i>
				</a>
			</div>
		</div>
		<div class="hook-content">
			{if isset($data_shortcode_content.deoshortcode.content)}
				{$data_shortcode_content.deoshortcode.content}{* HTML form , no escape necessary *}
			{/if}
			<div class="hook-content-footer text-center">
				<a href="javascript:void(0)" tabindex="0" class="btn-new-widget-group" title="{l s='Add New Column In Row' mod='deotemplate'}" data-container="body" data-toggle="popover" data-placement="top" data-trigger="focus">
					<i class="icon-plus"></i> {l s='Add New Column In Row' mod='deotemplate'}
				</a>
			</div>
		</div>
	</div>
</div>
