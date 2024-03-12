{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if $showed==true}
 {*{$toolbar}*}{* HTML form , no escape necessary *}
<div id="deo-page" class="clearfix">
	<div class="note">
		<p>+ {l s='Drop modules from hooks layouts to "<b>UnHook Modules</b>" Panel to unhook them'  mod='deotemplate'}. {l s='Drag and drop modules from hooks layouts to update theirs order and hook position'  mod='deotemplate'}</p>
		<p>+  {l s='Override hook feature only applies for <b>HOOK_HEADERRIGHT, HOOK_SLIDESHOW, HOOK_TOPNAVIATION, HOOK_SLIDESHOW, HOOK_PROMOTETOP, HOOK_CONTENTBOTTOM, HOOK_BOTTOM</b>'  mod='deotemplate'}</p>
		<p>+ {l s='Here only shows all of installed modules having hooks supportting for Deotemplate Layout.' mod='deotemplate'}
	</div>	
	<div class="deo-container holdposition" id="noposition">
		<div class="pos">{l s='UnHook Modules'  mod='deotemplate'} </div>
		 {foreach from=$modules item=module}
			<div class="module-pos" id="module-{$module->id|escape:'html':'UTF-8'}">
				<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module->name|escape:'html':'UTF-8'}&configure={$module->name|escape:'html':'UTF-8'}" data-mod="{$module->name|escape:'html':'UTF-8'}"></a>
				<div class="deo-editmodule" rel="{$module->author|escape:'html':'UTF-8'}">
				<img src="{$URI|escape:'html':'UTF-8'}{$module->name|escape:'html':'UTF-8'}/logo.png"/>
				{$module->displayName|escape:'html':'UTF-8'}
				</div>
			
			</div>
		 {/foreach}
	</div>
	<div class="deotheme-layout">
		<div id="deo-header">
			<div id="deo-displaynav" class="deo-container overridehook" data-position="displayNav1"><div class="pos">HOOK_NAV1</div>
			{if isset($hookModules['displayNav1']) && $hookModules['displayNav1']['module_count'] > 0}
				{foreach $hookModules['displayNav1']['modules'] as $position => $module} 
					{if isset($module['instance'])}
						<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
							<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
							<div class="deo-editmodule">
								<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
								{$module['instance']->displayName|escape:'html':'UTF-8'}
							</div>
						</div>
					{/if}
				{/foreach}
			{/if}
			</div>
			
			
			<div id="deo-displaynav" class="deo-container overridehook" data-position="displayNav2"><div class="pos">HOOK_NAV2</div>
			{if isset($hookModules['displayNav2']) && $hookModules['displayNav2']['module_count'] > 0}
				{foreach $hookModules['displayNav2']['modules'] as $position => $module} 
					{if isset($module['instance'])}
						<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
							<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
							<div class="deo-editmodule">
								<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
								{$module['instance']->displayName|escape:'html':'UTF-8'}
							</div>
						</div>
					{/if}
				{/foreach}
			{/if}
			</div>
			
			
			<div id="deo-displaynav" class="deo-container overridehook" data-position="displayTop"><div class="pos">HOOK_TOP</div>
			{if isset($hookModules['displayTop']) && $hookModules['displayTop']['module_count'] > 0}
				{foreach $hookModules['displayTop']['modules'] as $position => $module} 
					{if isset($module['instance'])}
						<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
							<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
							<div class="deo-editmodule">
								<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
								{$module['instance']->displayName|escape:'html':'UTF-8'}
							</div>
						</div>
					{/if}
				{/foreach}
			{/if}
			</div>

			
			<div id="deo-displaynav" class="deo-container overridehook" data-position="displayNavFullWidth"><div class="pos">HOOK_NAV_FULLWIDTH</div>
			{if isset($hookModules['displayNavFullWidth']) && $hookModules['displayNavFullWidth']['module_count'] > 0}
				{foreach $hookModules['displayNavFullWidth']['modules'] as $position => $module} 
					{if isset($module['instance'])}
						<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
							<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
							<div class="deo-editmodule">
								<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
								{$module['instance']->displayName|escape:'html':'UTF-8'}
							</div>
						</div>
					{/if}
				{/foreach}
			{/if}
			</div>
		</div>
		
		
		<div id="deo-content" class="clearfix deo_top_25"  >
			<div id="deo-left" class="deo-container" data-position="displayLeftColumn"><div class="pos">HOOK_LEFTCOLUMN</div>
				{if isset($hookModules['displayLeftColumn']) && $hookModules['displayLeftColumn']['module_count'] > 0}
					{foreach $hookModules['displayLeftColumn']['modules'] as $position => $module} 
						{if isset($module['instance'])}
							<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
								<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
								<div class="deo-editmodule">
									<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
									{$module['instance']->displayName|escape:'html':'UTF-8'}
								</div>
							</div>
						{/if}
					{/foreach}
				{/if}
			</div>
			
			
			<div  id="deo-center" class="deo-container inner" data-position="displayHome" style="min-height:250px"><div class="pos">HOOK_HOME</div>
				{if isset($hookModules['displayHome']) && $hookModules['displayHome']['module_count'] > 0}
					{foreach $hookModules['displayHome']['modules'] as $position => $module}
						{if isset($module['instance'])}
							<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
								<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
								<div class="deo-editmodule">
									<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
									{$module['instance']->displayName|escape:'html':'UTF-8'}
								</div>
							</div>
						{/if}
					{/foreach}
				{/if}
			</div>
			
			
			<div id="deo-right" class="deo-container" data-position="displayRightColumn"><div class="pos">HOOK_RIGHTCOLUMN</div>
				{if isset($hookModules['displayRightColumn']) && $hookModules['displayRightColumn']['module_count'] > 0}
					{foreach $hookModules['displayRightColumn']['modules'] as $position => $module}
						{if isset($module['instance'])}
							<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
								<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
								<div class="deo-editmodule">
									<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
									{$module['instance']->displayName|escape:'html':'UTF-8'}
								</div>
							</div>
						{/if}
					{/foreach}
				{/if}
			</div>
		</div>
			
			
		<div id="deo-content" class="clearfix"  >
			<div id="deo-left" class="deo-container clearfix" data-position="displayLeftColumnProduct"><div class="pos">HOOK_PRODUCT_LEFT</div>
				{if isset($hookModules['displayLeftColumnProduct']) && $hookModules['displayLeftColumnProduct']['module_count'] > 0}
					{foreach $hookModules['displayLeftColumnProduct']['modules'] as $position => $module} 
						{if isset($module['instance'])}
							<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
								<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
								<div class="deo-editmodule">
									<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
									{$module['instance']->displayName|escape:'html':'UTF-8'}
								</div>
							</div>
						{/if}
					{/foreach}
				{/if}
			</div>
			<div id="deo-center"></div>
			<div id="deo-right" class="deo-container" data-position="displayRightColumnProduct"><div class="pos">HOOK_PRODUCT_RIGHT</div>
				{if isset($hookModules['displayRightColumnProduct']) && $hookModules['displayRightColumnProduct']['module_count'] > 0}
					{foreach $hookModules['displayRightColumnProduct']['modules'] as $position => $module}
						{if isset($module['instance'])}
							<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
								<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
								<div class="deo-editmodule">
									<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
									{$module['instance']->displayName|escape:'html':'UTF-8'}
								</div>
							</div>
						{/if}
					{/foreach}
				{/if}
			</div>
		</div>


		<div id="deo-bottom" class="deo-container overridehook clearfix deo_top_25" data-position="displayFooterBefore">
			<div class="pos">HOOK_FOOTER_BEFORE</div>
			{if isset($hookModules['displayFooterBefore']) && $hookModules['displayFooterBefore']['module_count'] > 0}
				{foreach $hookModules['displayFooterBefore']['modules'] as $position => $module}
					{if isset($module['instance'])}
						<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
							<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
							<div class="deo-editmodule">
								<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
								{$module['instance']->displayName|escape:'html':'UTF-8'}
							</div>
						</div>
					{/if}
				{/foreach}
			{/if}
		</div>

		<div id="deo-bottom" class="deo-container overridehook clearfix" data-position="displayFooter">
			<div class="pos">HOOK_FOOTER</div>
			{if isset($hookModules['displayFooter']) && $hookModules['displayFooter']['module_count'] > 0}
				{foreach $hookModules['displayFooter']['modules'] as $position => $module}
					{if isset($module['instance'])}
						<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
							<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
							<div class="deo-editmodule">
								<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
								{$module['instance']->displayName|escape:'html':'UTF-8'}
							</div>
						</div>
					{/if}
				{/foreach}
			{/if}
		</div>

		
		<div id="deo-bottom" class="deo-container overridehook clearfix" data-position="displayFooterAfter">
			<div class="pos">HOOK_FOOTER_AFTER</div>
			{if isset($hookModules['displayFooterAfter']) && $hookModules['displayFooterAfter']['module_count'] > 0}
				{foreach $hookModules['displayFooterAfter']['modules'] as $position => $module}
					{if isset($module['instance'])}
						<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
							<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
							<div class="deo-editmodule">
								<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
								{$module['instance']->displayName|escape:'html':'UTF-8'}
							</div>
						</div>
					{/if}
				{/foreach}
			{/if}
		</div>
		
		
		<div id="deo-bottom" class="deo-container overridehook clearfix" data-position="displayFooterProduct">
			<div class="pos">HOOK_FOOTER_PRODUCT</div>
			{if isset($hookModules['displayFooterProduct']) && $hookModules['displayFooterProduct']['module_count'] > 0}
				{foreach $hookModules['displayFooterProduct']['modules'] as $position => $module}
					{if isset($module['instance'])}
						<div class="module-pos" id="module-{$module['instance']->id|escape:'html':'UTF-8'}" data-position="{$module['id_hook']|escape:'html':'UTF-8'}">
							<a class="editmod" href="{$moduleEditURL|escape:'html':'UTF-8'}&module_name={$module['name']|escape:'html':'UTF-8'}&configure={$module['name']|escape:'html':'UTF-8'}" data-mod="{$module['name']|escape:'html':'UTF-8'}"></a><div class="edithook"></div>
							<div class="deo-editmodule">
								<img src="{$URI|escape:'html':'UTF-8'}{$module['name']|escape:'html':'UTF-8'}/logo.png"/>
								{$module['instance']->displayName|escape:'html':'UTF-8'}
							</div>
						</div>
					{/if}
				{/foreach}
			{/if}
		</div>
	<div class="clearfix"></div>
</div>
<div id="overidehook" style="display:none">
	<div id="oh-close">Close</div>
	<form action="{$currentURL}&action=overridehook" method="post">
	<p class="clearfix"><label>{l s='Select override hook' mod='deotemplate'}</lable><br>
	<select  name="name_hook">
		<option value="0">{l s='--- Use Self Hook ---'  mod='deotemplate'}</option>

	</select>
	
	
	<input type="hidden" name="hdidmodule" id="hdidmodule" value=""/>
		<input type="hidden" name="deshook" id="deshook" value=""/>
	<input type="submit" value="{l s='Save' mod='deotemplate'}" name="submit" />
	</p>
	</form>
</div>	
<script type="text/javascript">
$("#noposition").css("height",$(".deotheme-layout").height() );
{*
https://www.google.com.vn/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#q=jquery+sortable+and+clone&*
http://jsfiddle.net/v265q/
http://jsfiddle.net/v265q/190/
*}
{*$('#deo-page .deo-container').sortable( {
	connectWith: '#deo-page .deo-container',
	containment: '#deo-page',
	forceHelperSize: true,
	forcePlaceholderSize: true,
	placeholder: 'placeholder',
	handle:".deo-editmodule",
	drop: function(e, ui){
		console.log('abc');
	},
});
*}



{*$('#noposition.deo-container').sortable( {
	connectWith: ".deotheme-layout .deo-container",
	remove: function(event, ui) {
		ui.item.clone().appendTo('.deotheme-layout .deo-container');
		$(this).sortable('cancel');
	}
});

$('.deotheme-layout .deo-container').sortable( {
	connectWith: "#noposition.deo-container, .deotheme-layout .deo-container",
});*}



// 2
$('#noposition.deo-container').sortable( {
	connectWith: ".deotheme-layout .deo-container",
	helper: function (e, li) {
		this.copyHelper = li.clone().insertAfter(li);
		$(this).data('copied', false);
		return li.clone();
	},
	stop: function () {
		var copied = $(this).data('copied');
		if (!copied) {
			this.copyHelper.remove();
		}
		this.copyHelper = null;
	}
});

$('.deotheme-layout .deo-container').sortable( {
	connectWith: "#noposition.deo-container, .deotheme-layout .deo-container",
	receive: function (e, ui) {
		ui.sender.data('copied', true);
	}
});


// 3
{*$('#noposition.deo-container').sortable( {
	connectWith: ".deotheme-layout .deo-container",
	helper: "clone",
}).on('dragstart', function (e, ui) {
	$(ui.helper).css('z-index','999999');
}).on('dragstop', function (e, ui) {
	$(this).after($(ui.helper).clone().draggable());
});

$('.deotheme-layout .deo-container').sortable( {
	connectWith: "#noposition.deo-container",
});

$('.deotheme-layout .deo-container').sortable( {
	connectWith: ".deotheme-layout .deo-container",
});*}



$(document).ready( function(){
	miniLeftMenu();
	//$('#deohook_toolbar').hide();
	$("#desc-deohook-save, #page-header-desc-deohook-save").click( function(){
		//var string = 'rand='+Math.random();
		var string = '';
		var hook = '';
		$(".deotheme-layout .ui-sortable").each( function(){
			if( $(this).attr("data-position") && $(".module-pos",this).length>0)
			{
				string +="&position[]="+$(this).attr("data-position")+"|";
				hook += "&"+$(this).attr("data-position")+"=";
				$(".module-pos",this).each( function(){
					if( $(this).attr("id") != "" ){
							string += $(this).attr("id").replace("module-","")+",";
							hook += $(this).attr("data-position")+",";
					}				
				} );
				string = string.replace(/\,$/,"");
				hook = hook.replace(/\,$/,"");
			}
		});
		var unhook = '';
		var arr_unhook = [];
		$("#noposition .module-pos").each( function(){
			var id_position = $(this).attr("data-position");
			var id_module = $(this).attr("id").replace("module-","");

			if( arr_unhook[ id_module ] )
			{
				// REMOVE MODULE AT MANY HOOK
				arr_unhook[ id_module ] += ',' + id_position;
			}else
			{
				arr_unhook[ id_module ] = id_position;
			}
			for( i=0; i < arr_unhook.length; i++)
			{
				if(arr_unhook[i])
				{
				  unhook += '&unhook['+i+']='+arr_unhook[i];
				}
			}
		});

		$.ajax({
		  type: 'POST',
		  url: $(this).attr("href") + "&updatedeohook=1&ajax=1",
		  data: string+"&"+hook+unhook,
		  dataType: 'json',
		  cache: false,
		  success: function(json){
				if (json && json.hasError == true){
					alert(json.errors);
				}else{
					window.location.reload(true);
				}
			}
		});
		return false; 
	});
	
	$("#overidehook #oh-close").click( function() { $("#overidehook").hide(); } );
	$("#overidehook form").submit( function(){
		var string  =  $("#overidehook form").serialize();
		if( $("#overidehook #hdidmodule").val() ){
			$.ajax({
				type:'POST',
				url:$(this).attr("action") + "&updatedeohook=1&ajax=1",
				data:string,
				dataType: 'json',
				cache: false,
				success: function( json ){
					if (json && json.hasError == true){
						alert(json.errors);
					}else{
						$("#overidehook").hide();
					}
				} 
			 });
		}
		return false; 
	});
} );	

$(".editmod").fancybox({
	'type':'iframe',
	'width':1024,
	'height':500,
	afterLoad:function()
	{
		if( $('body',$('.fancybox-iframe').contents()).find("#main").length  )
		{
			$('body',$('.fancybox-iframe').contents()).find("#header").hide();
			$('body',$('.fancybox-iframe').contents()).find("#footer").hide();
			$('body',$('.fancybox-iframe').contents()).find(".page-head, #nav-sidebar ").hide();
			$('body',$('.fancybox-iframe').contents()).find("#content.bootstrap").css( 'padding',0).css('margin',0);
		}else 
		{ 
			$('body',$('.fancybox-iframe').contents()).find("#psException").html('<div class="alert error"> {$noModuleConfig|escape:'html':'UTF-8'}</div>');
		}
	}
});
</script>
{/if}