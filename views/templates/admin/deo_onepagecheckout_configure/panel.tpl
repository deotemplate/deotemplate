{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div id="blog-dashboard">
	<div class="row">
		<div class="col-md-12">
			<div id="blog-general" class="tab-config-helper">
			    {assign var=tabList value=$tabs.values}
			    <div class="panel-tab">
				    <ul class="nav nav-tabs admin-tabs tab-config-admin" role="tablist">
				        {foreach $tabList as $key => $value name="tabList"}
				            <li role="presentation" class="tabConfig {if $key == $tabs.default}active{/if}">
				                <a href="#{$key|escape:'html':'UTF-8'}" class="deo-tab-config" role="tab" data-toggle="tab" data-value="{$key|escape:'html':'UTF-8'}">{$value|escape:'html':'UTF-8'}</a>
				            </li>
				        {/foreach}
				    </ul>
			    </div>
				<script>
				    $(document).ready(function(){
				        $('.deo-tab-config').click(function(){
				            $('#tab_open').val( $(this).data('value') );
				        });
				        // $('.deo-tab-config.active').trigger('click');
				    });
				    
				    $(document).on('click', '#configuration_form_submit_btn', function(e){
				        e.preventDefault();
				        var active_tab = $('#blog-general .panel-tab ul.nav-tabs li.active a').data('value');
				        $('#tab_open').val( active_tab );
				        $(this).closest('form').submit();
				    });
				</script>
				<div class="panel-content">
					{$globalform}{* HTML form , no escape necessary *}
				</div>
			</div>	
		</div>
		<div class="col-md-6">	
			<div id="blog-list">
				<div class="tab-content panel">
					<ul class="nav nav-tabs admin-tabs tab-config-admin">
						<li class="active">
							<a data-toggle="tab" role="tab" href="#tab_most_viewed">
								{l s='Most Viewed' mod='deotemplate'}
							</a>
						</li>
						<li>
							<a data-toggle="tab" role="tab" href="#tab_latest_comment">
								{l s='Lastest Comments' mod='deotemplate'}
							</a>
						</li>
					</ul>
					<div class="tab-content">
						<div id="tab_most_viewed" class="tab-pane active">
							
							<p>{l s='No blog at this time' mod='deotemplate'}</p>
							
						</div>
						<div id="tab_latest_comment" class="tab-pane">
							
							<p>{l s='No comment at this time' mod='deotemplate'}</p>
							
						</div>
					</div>
				</div>
			</div>	
		</div>
		<div class="col-md-6">
			<div class="panel">
				<div class="panel-heading">{l s='Statistics' mod='deotemplate'}</div>
				<div class="panel-content text-center" id="blog-statistics">
					<div class="row" id="dashtrends_toolbar">
						<dl class="col-xs-4 col-lg-4 active">
							<dt>{l s='Blogs' mod='deotemplate'}</dt>
							<dd class="data_value size_l"><span id="sales_score"></span></dd>
						</dl>
						<dl   class="col-xs-4 col-lg-4">
							<dt>{l s='Categories' mod='deotemplate'}</dt>
							<dd class="data_value size_l"><span id="orders_score"></span></dd>
						</dl>
						<dl  class="col-xs-4 col-lg-4">
							<dt>{l s='Comments' mod='deotemplate'}</dt>
							<dd class="data_value size_l"><span id="cart_value_score"></span></dd>
						</dl>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>