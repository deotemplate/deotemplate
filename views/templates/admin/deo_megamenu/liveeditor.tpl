{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div id="setting-menu" class="clearfix">
    <div id="sidebar-configure">
        {$generate_form nofilter}
    </div>
    <div id="live-editor">   
        <div id="menu-form" style="display: none; left: 340px; top: 15px; max-width:600px" class="popover top out form-setting">
            <div class="arrow"></div>
            <div class="popover-title clearfix">
                <i class="icon-gears"></i>
    			{l s='Setting Sub Menu' mod='deotemplate'}
                <span class="pull-right close"><i class="icon-remove-sign"></i></span>
            </div>
            <div class="popover-content clearfix"> 
                <form  method="post" action="{$liveedit_action}" enctype="multipart/form-data" onsubmit="return false;">
                    <div class="col-lg-12">	
                        <table class="table table-hover">
                            <tr class="type-submenu">
                                <td>{l s='Sub Menu Widget' mod='deotemplate'}</td>
                                <td>
                                    <select name="menu_subwith" class="menu_subwith">
                                        <option value="none" selected>{l s='None' mod='deotemplate'}</option>
                                        <option value="submenu">{l s='Submenu' mod='deotemplate'}</option>
                                        <option value="widget">{l s='Widget' mod='deotemplate'}</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="aligned-submenu">
                                <td>{l s='Align Sub Menu' mod='deotemplate'}</td>
                                <td>
                                    <div class="btn-group button-alignments">
                                        <button type="button" class="btn btn-default" data-option="aligned-left"><span class="icon icon-align-left"></span></button>
                                        <button type="button" class="btn btn-default" data-option="aligned-center"><span class="icon icon-align-center"></span></button>
                                        <button type="button" class="btn btn-default" data-option="aligned-right"><span class="icon icon-align-right"></span></button>
                                        <button type="button" class="btn btn-default" data-option="aligned-fullwidth"><span class="icon icon-align-justify"></span></button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="width_submenu">
                                <td>{l s='Width Sub Menu (px)' mod='deotemplate'}</td>
                                <td>
                                    <input type="text" name="menu_subwidth" class="menu_subwidth"> 
                                </td>
                            </tr>
                            <tr class="button-submit" style="display: none;">
                                <td colspan="2" class="text-right">
                                    <button type="submit" class="apply btn btn-info btn-sm"><span>{l s='Apply' mod='deotemplate'}</span></button>
                                </td>
                            </tr>
                        </table>
                        <span class="text-alert-change" style="display: none;">{l s='*Click the Apply button to make changes.' mod='deotemplate'}</span>
                        <input type="hidden" name="menu_id">
                    </div>
                </form>
            </div>
        </div>

        <div id="column-form" class="modal fade column-setting form-setting" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{l s='Column Setting' mod='deotemplate'}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body"> 
                        <form method="post" action="{$liveedit_action}"  enctype="multipart/form-data" >
                            <table class="table table-hover">
                                <tr>
                                    <td>{l s='Addition Class' mod='deotemplate'}</td>
                                    <td>
                                        <input type="text" name="colclass"> 
                                    </td>
                                </tr>
                                <tr>
                                    <td>{l s='Large Desktop (width screen ≥ 1500px)' mod='deotemplate'}</td>
                                    <td>
                                        <select class="colwidth" name="xxl"> 
                                            {foreach from=$widthList item=itemWidth}
                                                <option value="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</option>
                                            {/foreach}
                                        </select> 
                                    </td>
                                </tr>
                                <tr>
                                    <td>{l s='Desktop (width screen < 1500px)' mod='deotemplate'}</td>
                                    <td>
                                        <select class="colwidth" name="xl"> 
                                            {foreach from=$widthList item=itemWidth}
                                                <option value="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</option>
                                            {/foreach}
                                        </select> 
                                    </td>
                                </tr>
                                <tr>
                                    <td>{l s='Small Desktop (width screen < 1200px)' mod='deotemplate'}</td>
                                    <td>
                                        <select class="colwidth" name="lg"> 
                                            {foreach from=$widthList item=itemWidth}
                                                <option value="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</option>
                                            {/foreach}
                                        </select> 
                                    </td>
                                </tr>
                                <tr>
                                    <td>{l s='Tablet (width screen < 992px)' mod='deotemplate'}</td>
                                    <td>
                                        <select class="colwidth" name="md"> 
                                            {foreach from=$widthList item=itemWidth}
                                                <option value="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{l s='Small Tablet (width screen < 768px)' mod='deotemplate'}</td>
                                    <td>
                                        <select class="colwidth" name="sm"> 
                                            {foreach from=$widthList item=itemWidth}
                                                <option value="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</option>
                                            {/foreach}
                                        </select> 
                                    </td>
                                </tr>
                                <tr>
                                    <td>{l s='Mobile (width screen < 576px)' mod='deotemplate'}</td>
                                    <td>
                                        <select class="colwidth" name="xs"> 
                                            {foreach from=$widthList item=itemWidth}
                                                <option value="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</option>
                                            {/foreach}
                                        </select> 
                                    </td>
                                </tr>
                                <tr>
                                    <td>{l s='Small Mobile (width screen < 480px)' mod='deotemplate'}</td>
                                    <td>
                                        <select class="colwidth" name="sp"> 
                                            {foreach from=$widthList item=itemWidth}
                                                <option value="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}">{$itemWidth|escape:'html':'UTF-8'}/12 - ({(($itemWidth/12)*100)|string_format:"%.2f"}%)</option>
                                            {/foreach}
                                        </select> 
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{l s='Close' mod='deotemplate'}</button>
                        <button type="button" class="btn btn-default pull-right btn-primary save">{l s='Save' mod='deotemplate'}</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="widget-setting" class="modal fade widget-setting form-setting" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{l s='Widget Setting' mod='deotemplate'}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <select name="key_widget" class="key_widget">
                            {* <option value="">{l s='Choose widget...' mod='deotemplate'}</option> *}
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button"  id="btn-close-widget" class="btn btn-default pull-left" data-dismiss="modal">{l s='Close' mod='deotemplate'}</button>
                        <button type="button" id="btn-inject-widget" class="btn btn-primary pull-right hide">{l s='Insert' mod='deotemplate'}</button>
                        <button type="button" id="btn-create-widget" class="btn btn-warning pull-right">{l s='Create New Widget' mod='deotemplate'}</button>
                    </div>
                </div> 
            </div> 
        </div>

        <div id="list-widgets" class="modal fade list-widgets form-setting" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{l s='Choose Type Widget' mod='deotemplate'}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        {$list_widgets}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-right close-btn" data-dismiss="modal">{l s='Close' mod='deotemplate'}</button>
                    </div>
                </div> 
            </div> 
        </div>

        <div id="form-widget" class="modal fade form-widget form-setting" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    
                </div> 
            </div> 
        </div>

        <div id="form-submenu" class="modal fade form-submenu form-setting" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    
                </div> 
            </div> 
        </div>

        <div id="modal_select_image" class="modal fade form-setting" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                        <span class="sr-only">{l s='Close' mod='deotemplate'}</span></button>
                        <h4 class="modal-title2">{l s='Image manager' mod='deotemplate'}</h4>
                    </div>
                    <div class="modal-body"></div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='deotemplate'}</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="deo_loading" class="deo-loading" style="display: none;">
            <div class="spinner">
                <div class="item-1"></div>
                <div class="item-2"></div>
                <div class="item-3"></div>
            </div>
        </div>

        <div id="content-s">
            <div id="pav-megamenu-liveedit">
                <div class="megamenu-wrap">
                    <label class="alert alert-info" style="display: none;">{l s='Group does not exist. Please create group menu first.' mod='deotemplate'}</label>
                    <div class="progress" id="progress-menu">
                        <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                            <span class="percentage">0%</span>
                        </div>
                    </div>
                    <div id="megamenu-content" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div> 
</div>

<script type="text/javascript">
    var live_editor = true;
    var list_tab_live_editor = [];
    $("#megamenu-content").DeoMegamenuEditor({
		"action": "{$ajxgenmenu|replace:'&amp;':'&'}", 
		"action_save": "{$ajxsavemenu|replace:'&amp;':'&'}", 
		"action_widget": "{$action_widget|replace:'&amp;':'&'}", 
        "action_submenu": "{$action_submenu|replace:'&amp;':'&'}", 
        "action_loadwidget" : "{$action_loadwidget|replace:'&amp;':'&'}",
        "action_gensubmenu" : "{$ajxgensubmenu|replace:'&amp;':'&'}",
        "action_changeposition" : "{$ajxchangeposition|replace:'&amp;':'&'}",
        "base_url_widget" : "{$base_url_widget|replace:'&amp;':'&'}",
		"id_shop": "{$id_shop}",
        "message_apply" : "{l s='Click button save to finish changes.' mod='deotemplate'}",
	});
</script>
