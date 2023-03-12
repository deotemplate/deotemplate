{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{if $widget_selected}
	{$form}{* HTML form , no escape necessary *}
	<script type="text/javascript">
		$('#widget_type').change( function(){
			location.href = '{html_entity_decode($action|escape:'html':'UTF-8')}&wtype='+$(this).val();
		});
	</script>	
{else}
	<div id="choose-list-widgets" class="row">
		{foreach $types as $widget => $text}
			{if $text.for != 'manage'}
				<div class="col-sm-6 col-xs-12">
					<a href="javascript:void(0);" class="widget-type" data-widget_type="{$widget|escape:'html':'UTF-8'}">
						<h4>{$text.label|escape:'html':'UTF-8'}</h4>
						<p><i>{$text.explain|escape:'html':'UTF-8'}{* HTML form , no escape necessary *}</i></p>	
					</a>
				</div>
			{/if}	
		{/foreach} 
	</div>		
{/if}
