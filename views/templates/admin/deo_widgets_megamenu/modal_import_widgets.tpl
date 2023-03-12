{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}


<div id="import_widgets_menu" class="modal fade form-setting" role="dialog" aria-hidden="true">
    <form method="post" enctype="multipart/form-data" action="{$link->getAdminLink('AdminDeoWidgetsMegamenu')|escape:'html':'UTF-8'}&importwidgets=1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">{l s='Import Widgets Menu' mod='deotemplate'}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">                            
                            <input type="file" class="hide" name="import_widgets_file" id="import_widgets_file">
                            <div class="dummyfile input-group">
                                <span class="input-group-addon"><i class="icon-file"></i></span>
                                <input type="text" readonly="" name="filename" class="disabled" id="import_widgets_file-name">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" name="submitAddAttachments" type="button" id="import_widgets_file-selectbutton">
                                        <i class="icon-folder-open"></i> {l s='Choose a file' mod='deotemplate'}
                                    </button>
                                </span>
                            </div>
                            <p class="help-block color_danger">{l s='Please upload *.txt only. Import will override widgets have same key' mod='deotemplate'}</p>
                        </div>                                                                                               
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" name="importWidgets" id="import_widgets_file_submit_btn" type="submit">
                        {l s='Import Widgets' mod='deotemplate'}
                    </button>
                </div>
            </div> 
        </div> 
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        //import/expor widgets menu
        $('#import_widgets_file-selectbutton').click(function(e){
            $('#import_widgets_file').trigger('click');
        });
        $('#import_widgets_file').change(function(e){
            var val = $(this).val();
            var file = val.split(/[\\/]/);
            $('#import_widgets_file-name').val(file[file.length-1]);
        });
        $('#import_widgets_file_submit_btn').click(function(e){
            if($("#import_widgets_file-name").val().indexOf(".txt") != -1){
                if ($("#override_import_widgets_on").is(":checked")) 
                    return confirm("{l s='Are you sure to override widgets?' mod='deotemplate'}");
                return true;
            }else{
                alert("{l s='Please upload txt file' mod='deotemplate'}");
                $('#import_widgets_file').val("");
                $('#import_widgets_file-name').val("");
                return false;
            }
        });     

        $("#page-header-desc-deomegamenu_widgets-import_widgets").click(function(e) {
            e.preventDefault();
            $('#import_widgets_menu').modal('show');
        });
    });
</script>