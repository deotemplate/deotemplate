{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if (int) DeoHelper::getConfig('ENABLE_PRODUCT_COMPARE')}
	<div class="deo-count-compare deo-count-feature block {(isset($formAtts.class)) ? $formAtts.class : ''} {if isset($formAtts.sub_title) && $formAtts.sub_title}has-sub-title{/if}">
		<a class="deo-btn-compare" href="{url entity='module' name='deotemplate' controller='compare'}" title="{l s='Compare' mod='deotemplate'}" rel="nofollow">
			<i class="deo-custom-icons icon-compare"></i>
			<span class="text">
				<span class="name-simple">{l s='Compare'  mod='deotemplate'}</span>{if isset($formAtts.count) && $formAtts.count} <span class="deo-total-compare deo-total">0</span>{/if}
			</span>
		</a>
	</div>
{/if}
