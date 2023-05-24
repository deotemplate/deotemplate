{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<fieldset>
	<div id="groupLayer" class="panel col-md-12">
		<h3>{l s='Group List' mod='deotemplate'}</h3>
		
		<div class="group-header col-md-12 col-xs-12">
			<ol>
				<li>
					<div class="col-md-1 col-xs-1 text-center">
						<span class="title_box ">
							{l s='ID' mod='deotemplate'}
						</span>
					</div>
					<div class="col-md-3 col-xs-3">
						<span class="title_box ">
							{l s='Group Name' mod='deotemplate'}
						</span>
					</div>
					<div class="col-md-1 col-xs-1 text-center">
						<span class="title_box">
							{l s='Class CSS' mod='deotemplate'}
						</span>
					</div>
					<div class="col-md-1 col-xs-1 text-center">
						<span class="title_box">
							{l s='Tab Type' mod='deotemplate'}
						</span>
					</div>
					<div class="col-md-1 col-xs-1 text-center">
						<span class="title_box">
							{l s='Menu Mobile' mod='deotemplate'}
						</span>
					</div>
					<div class="col-md-2 col-xs-2 text-center">
						<span class="title_box">
							{l s='Type' mod='deotemplate'}
						</span>
					</div>
					<div class="col-md-1 col-xs-1 text-center">
						<span class="title_box ">{l s='Active' mod='deotemplate'}</span>
					</div>
					<div class="col-md-2 col-xs-2 text-right">
						<a href="{$link->getAdminLink('AdminDeoMegamenu')|escape:'html':'UTF-8'}&configuregroup=1&addNewGroup=1" class="btn btn-default">
							<i class="icon-plus"></i> {l s='Add new Group' mod='deotemplate'}
						</a>
					</div>
				</li>
			</ol>
		</div>
		<div class="group-wrapper col-md-12 col-xs-12">
			<div class="row">
				<ol class="tree-group disable-sort-position">
					{foreach from=$groups item=group}
						<li id="list_group_{$group.id_deomegamenu_group}" class="nav-item">
							<div class="col-md-1 col-xs-1 text-center"><strong>#{$group.id_deomegamenu_group|intval}</strong></div>
							<div class="col-md-3 col-xs-3" class="pointer">
								{$group.title|escape:'html':'UTF-8'}
							</div>
							<div class="col-md-1 col-xs-1 text-center">
								{$group.group_class}
							</div>
							<div class="col-md-1 col-xs-1 text-center">
								{if isset($group.tab_style) && $group.tab_style}
									{l s='Yes' mod='deotemplate'}
								{else}
									{l s='No' mod='deotemplate'}
								{/if}
							</div>
							<div class="col-md-1 col-xs-1 text-center">
								{if $group.show_cavas}
									{l s='Yes' mod='deotemplate'}
								{else}
									{l s='No' mod='deotemplate'}
								{/if}
							</div>
							<div class="col-md-2 col-xs-2 text-center">
								{if $group.group_type == 'vertical'}
									{l s='Vertical' mod='deotemplate'} <span>({$group.type_sub})</span>
								{else}
									{l s='Horizontal' mod='deotemplate'}
								{/if}
							</div>
							<div class="col-md-1 col-xs-1 text-center">
								{$group.status}
							</div>
							<div class="col-md-2 col-xs-2">
								<div class="btn-group-action">
									<div class="btn-group pull-right">
										<a href="{$link->getAdminLink('AdminDeoMegamenu')|escape:'html':'UTF-8'}&configuregroup=1&liveeditor=1&editgroup=1&id_group={$group.id_deomegamenu_group|escape:'html':'UTF-8'}" title="{l s='Configure Group' mod='deotemplate'}" class="edit btn btn-default">
											<i class="icon-search-plus"></i> {l s='View' mod='deotemplate'}
										</a>
										<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											<span class="caret"></span>&nbsp;
										</button>
										<ul class="dropdown-menu">
											<li>
												<a href="{$link->getAdminLink('AdminDeoMegamenu')|escape:'html':'UTF-8'}&deletegroup=1&id_group={$group.id_deomegamenu_group|intval}" onclick="if (confirm(text_confirm_delete_group)) {
														return true;
													} else {
														event.stopPropagation();
														event.preventDefault();
													}
													;" title="{l s='Delete' mod='deotemplate'}" class="delete">
													<i class="icon-trash"></i> {l s='Delete' mod='deotemplate'}
												</a>
											</li>
											<li>
												<a href="{$link->getAdminLink('AdminDeoMegamenu')|escape:'html':'UTF-8'}&duplicategroup=1&id_group={$group.id_deomegamenu_group|intval}" onclick="if (confirm('{l s='Duplicate Selected Group?' mod='deotemplate'}')) {
														return true;
													} else {
														event.stopPropagation();
														event.preventDefault();
													}
													;" title="{l s='Duplicate' mod='deotemplate'}" class="duplicate">
													<i class="icon-copy"></i> {l s='Duplicate' mod='deotemplate'}
												</a>															
											</li>
											<li>
												<a href="{$link->getAdminLink('AdminDeoMegamenu')|escape:'html':'UTF-8'}&exportgroup=1&id_group={$group.id_deomegamenu_group|intval}&widgets=1" title="{l s='Export' mod='deotemplate'}" class="export">
													<i class="icon-external-link"></i> {l s='Export' mod='deotemplate'}
												</a>																
											</li>
										</ul>
									</div>
								</div>				
							</div>
						</li> 
					{/foreach}
				</ol>
			</div>
		</div>

		<div id="import_group_menu" class="modal fade form-setting" role="dialog" aria-hidden="true">
			<form method="post" enctype="multipart/form-data" action="{$link->getAdminLink('AdminDeoMegamenu')|escape:'html':'UTF-8'}&importgroup=1">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title">{l s='Import Group Menu' mod='deotemplate'}</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="form-group">							
									<input type="file" class="hide" name="import_file" id="import_file">
									<div class="dummyfile input-group">
										<span class="input-group-addon"><i class="icon-file"></i></span>
										<input type="text" readonly="" name="filename" class="disabled" id="import_file-name">
										<span class="input-group-btn">
											<button class="btn btn-default" name="submitAddAttachments" type="button" id="import_file-selectbutton">
												<i class="icon-folder-open"></i> {l s='Choose a file' mod='deotemplate'}
											</button>
										</span>
									</div>
									<p class="help-block color_danger">{l s='Please upload *.txt only' mod='deotemplate'}</p>
								</div>            
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-primary pull-right" name="importGroup" id="import_file_submit_btn" type="submit">
								{l s='Import Group' mod='deotemplate'}
							</button>
						</div>
					</div> 
				</div> 
			</form>
		</div>
	</div>
</fieldset>
<script type="text/javascript">
	var text_confirm_delete_group = "{l s='Delete Selected Group?' mod='deotemplate'}";
	var update_group_position_link = "{$update_group_position_link}";
	$(document).ready(function() {
		//import/export group menu
		$('#import_file-selectbutton').click(function(e){
			$('#import_file').trigger('click');
		});
		$('#import_file').change(function(e){
			var val = $(this).val();
			var file = val.split(/[\\/]/);
			$('#import_file-name').val(file[file.length-1]);
		});
		$('#import_file_submit_btn').click(function(e){
			if($("#import_file-name").val().indexOf(".txt") != -1){
				if($("#override_group_on").is(":checked")) return confirm("{l s='Are you sure to override group?' mod='deotemplate'}");
				if($("#override_widget_on").is(":checked")) return confirm("{l s='Are you sure to override widgets?' mod='deotemplate'}");
				return true;
			}else{
				alert("{l s='Please upload txt file' mod='deotemplate'}");
				$('#import_file').val("");
				$('#import_file-name').val("");
				return false;
			}
		});

	
		
		$(".group-preview").click(function() {
			eleDiv = $(this).parent().parent().parent();
			if ($(eleDiv).hasClass("open"))
				eleDiv.removeClass("open");

			var url = $(this).attr("href") + "&content_only=1";
			$('#dialog').remove();
			$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe name="iframename2" src="' + url + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
			$('#dialog').dialog({
				title: 'Preview Management',
				close: function(event, ui) {

				},
				bgiframe: true,
				width: 1024,
				height: 780,
				resizable: false,
				draggable:false,
				modal: true
			});
			return false;
		});

		$("#page-header-desc-deomegamenu-import_groups").click(function(e) {
			e.preventDefault();
			$('#import_group_menu').modal('show');
		});
		
	});
</script>
