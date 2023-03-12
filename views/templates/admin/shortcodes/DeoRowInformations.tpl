{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{assign var="layout_homepage" value=($row_layout) ? 'Fullwidth' : 'Boxed'}
{assign var="layout_other_page" value=($row_layout_other) ? 'Fullwidth' : 'Boxed'}

<div class="alert alert-info">
	<span>{l s='This row in hook' mod='deotemplate'} <strong>{$hook_name}</strong></span></br>
	<span>{l s='Layout for homepage is' mod='deotemplate'} <strong>{$layout_homepage}</strong></span></br>
	<span>{l s='Layout for other page is' mod='deotemplate'} <strong>{$layout_other_page}</strong></span></br>
	<span>{l s='Configure layout for this hook' mod='deotemplate'} <a href="{$url_profile_edit}" target="blank">{l s='here' mod='deotemplate'}</a></span></br>
</div>