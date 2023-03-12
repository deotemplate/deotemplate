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
	<div class="col-lg-12" style="padding:20px;">
		<div class="col-lg-6 col-lg-offset-1">
			{foreach $types as $widget => $text}
				{if $text.for != 'manage'}
					<div class="col-lg-6">
						<h4><a href="{html_entity_decode($action|escape:'html':'UTF-8')}&wtype={$widget|escape:'html':'UTF-8'}">{$text.label|escape:'html':'UTF-8'}</a></h4>
						<p><i>{$text.explain}{* HTML form , no escape necessary *}</i></p>	
					</div>
				{/if}	
			{/foreach} 
		</div>
	</div>		
{/if}
