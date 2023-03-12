{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{$id_default = 0}
{if isset($list_profile) && $list_profile}
	<ul class="source-profile hidden">
		{$nameProfile = ''}
		{foreach $list_profile as $item}
			<li class="{if $item['active'] == 1}active{/if}">={$item['id_deotemplate_profiles']|escape:'html':'UTF-8'}</li>
			{if $item['active'] == 1}
			{$id_default = $item['id_deotemplate_profiles']}
			{$nameProfile = $item['name']}
			{/if}
		{/foreach}
	</ul>
	<div id="deo_loading" class="deo-loading" style="display: none;">
        <div class="spinner">
            <div class="item-1"></div>
            <div class="item-2"></div>
            <div class="item-3"></div>
        </div>
    </div>
	<script language="javascript" type="text/javascript">
		{addJsDef enableFriendlyUrl=$enable_friendly_url}
		{addJsDef urlPreview=$url_preview}
		{addJsDef urlEditProfile=$url_edit_profile}
		{addJsDef urlProfileDetail=$url_profile_detail}
		{addJsDef urlEditProfileToken=$url_edit_profile_token}
		{addJsDef idProfile=$id_default}
		$(function() {
			// Add button preview, tooltip for row
			totalTr = $(".deotemplate_profiles tbody tr").length;
			$(".deotemplate_profiles tbody tr").each(function() {
				// Add button preview
				let idProfile_row,friendlyUrl_row, url = '';
				if (totalTr <= 1){
					idProfile_row = $.trim($(this).find("td:nth-child(1)").text());
					friendlyUrl_row = $.trim($(this).find("td:nth-child(4)").text());
				}else{
					idProfile_row = $.trim($(this).find("td:nth-child(2)").text());
					friendlyUrl_row = $.trim($(this).find("td:nth-child(5)").text());
				}

				$(this).find(".pull-right ul li.divider").before("<li><a title=\"{l s='Customize Color' mod='deotemplate'}\" target='_blank' href='" + urlProfileDetail + "&id_deotemplate_profiles="+ idProfile_row +"&updatedeotemplate_profiles&tab_open=tab_customize'><i class='icon-asterisk'></i> {l s='Customize Color' mod='deotemplate'}</a></li>");
	                        
	            {if (int) DeoHelper::getConfig('DEBUG_MODE')}              
		            url = urlProfileDetail + "&submitBulkinsertLangdeotemplate_profiles&id=" + idProfile_row;
					$(this).find(".pull-right ul li.divider").before("<li><a title=\"{l s='Copy data from default language to other' mod='deotemplate'}\" href='" + url + "'><i class='icon-paste'></i> {l s='Copy to Other Language' mod='deotemplate'}</a></li>");   
				{/if}     
	       
				url = (enableFriendlyUrl && (friendlyUrl_row != '--' || friendlyUrl_row != '')) ? urlPreview + friendlyUrl_row + ".html" : urlPreview + "?id_deotemplate_profiles=" + idProfile_row;
				$(this).find(".pull-right ul").prepend("<li><a title=\"{l s='Preview' mod='deotemplate'}\" target='_blank' href='" + url + "'><i class='icon-search-plus'></i> {l s='Preview' mod='deotemplate'}</a></li>");
			});
			$(".deotemplate_profiles tbody tr").tooltip();
			var d = new Date();
			if($('.table-responsive-row .row-selector').length){
				var listTd = ".deotemplate_profiles tr td:nth-child(2)," + 
					".deotemplate_profiles tr td:nth-child(3), .deotemplate_profiles tr td:nth-child(4)";
			}else{
				var listTd = ".deotemplate_profiles tr td:nth-child(1)," + 
					".deotemplate_profiles tr td:nth-child(2), .deotemplate_profiles tr td:nth-child(3)";
			}
			$("#btn-preview").click(function() {
				window.open(urlPreview + "?id_deotemplate_profiles=" + idProfile, "_blank");
			});
			$("#btn-design-layout").click(function() {
				window.open(urlEditProfile + "&id_deotemplate_profiles=" + idProfile, "_blank");
			});
		});
	</script>
{else}
	<hr/>
	<center><p><a href="{$profile_link|escape:'html':'UTF-8'}" class="btn btn btn-primary"><i class="icon-file-text"></i> {l s='Create first Profile >>' mod='deotemplate'}</a>
	</p></center>
	<script type="text/javascript">
		$(function() {
			$(".deotemplate_profiles td:first-child").attr("colspan", $(".deotemplate_profiles th").length);
		});
	</script>
{/if}