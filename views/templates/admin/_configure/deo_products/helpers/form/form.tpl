{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'product_list_builder'}
        <div class="row {$input.type}">
            <div class="col-xl-5 col-lg-5 col-md-6 col-sm-12 list-builder">
                <div class="panel panel-sm layout-container">
                    <div class="panel-heading desc-box text-center">{l s='Product Layout' mod='deotemplate'}</div>
                    {foreach $input.blockList key=kBlock item=vblock}
                        <div class="{$vblock.class} panel panel-sm">
                            <div class="panel-heading">{$vblock.title}</div>
                            <div class="content {$kBlock}-block-content">
                                {assign var=blockElement value=$input.params[$kBlock]}
                                {foreach $blockElement item=gridElement}
                                    {if $gridElement.name == 'code'}
                                        {include file='./code.tpl' eItem=$gridElement}
                                    {elseif $gridElement.name == 'box'}
                                        {include file='./box.tpl' eItem=$gridElement}
                                    {else}
                                        {include file='./element.tpl' eItem=$gridElement}
                                    {/if}
                                {/foreach}
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
            <div class="col-xl-7 col-lg-7 col-md-6 col-sm-12 element-list display-grid">
                {foreach from=$input.elements item=eItems}
                    <div class="grid-item col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="panel panel-sm clearfix">
                            {foreach from=$eItems.group key=eKey item=eItem}
                                {if isset($eItem.type) and $eItem.type=="sperator"}
                                    <h4 class="title-group panel-heading">
                                        <i class="{(isset($eItem.icon)) ? $eItem.icon : 'icon-ticket'}"></i> {$eItem.name}
                                    </h4>
                                {elseif isset($eItem.type) and $eItem.type=="desc"}
                                    <p class="desc-group help-block">{$eItem.text}</p>
                                {else}
                                    {include file='./element.tpl' eItem=$eItem defaultItem=1}
                                {/if}
                            {/foreach}
                        </div>
                    </div>
                {/foreach}
                <div class="grid-item col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="panel panel-sm clearfix">
                        <h4 class="title-group panel-heading">
                            <i class="icon-beaker"></i> {l s='Advance Element' mod='deotemplate'}
                        </h4>
                        {include file='./code.tpl' defaultItem=1}
                        {include file='./box.tpl' defaultItem=1}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_form"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modal-lg">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='deotemplate'}</button>
                        <button type="button" class="btn btn-primary btn-savewidget">{l s='Save changes' mod='deotemplate'}</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="list-temp-element" style="display:none">
            <div id="product_thumbnail">
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Effect When Hover' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="effecthover" name="effecthover">
                            {foreach $input.effecthover key=key item=value}
                                <option value="{$key}"{($value == "disable") ? " selected" : ""}>{$value}</option>
                            {/foreach}              
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Size Image' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="size" name="size">
                            {foreach $input.imageType item=value}
                                <option value="{$value.name}"{($value.name == "home_default") ? " selected" : ""}>{$value.name} ({$value.width}px x {$value.height}px)</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Label and Flag' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="labelflag" name="labelflag">
                            {foreach $input.labelflag key=key item=value}
                                <option value="{$key}"{($value == "showall") ? " selected" : ""}>{$value}</option>
                            {/foreach}              
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Second Image' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="second_image" name="second_image">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0" selected="selected">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                        <p class="help-block">{l s='Effect show second image when hover product list. It always is No: when your product list have Product Thumbnail'}</p>
                    </div>
                </div>
            </div>
            <div id="reviews">
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Show Count' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="show_count" name="show_count">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0" selected="selected">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                        <p class="help-block">{l s='Show count number review' mod='deotemplate'}</p>
                    </div>
                </div>
                <div class="form-group count-text-input">
                    <label class="control-label col-lg-5">{l s='Show Count Text' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="show_text_count" name="show_text_count">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0" selected="selected">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                        <p class="help-block">{l s='Show text count number review' mod='deotemplate'}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Show Review Zero' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="show_zero_review" name="show_zero_review">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0" selected="selected">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                        <p class="help-block">{l s='Show review even count review = 0' mod='deotemplate'}</p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-offset-5 col-lg-7">
                        <p class="alert alert-warning">{l s='You have to enable Review at Deo Template > Theme Configurations > Review to use this function' mod='deotemplate'}</p>
                    </div>
                </div>
            </div>
            <div id="quantity">
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Show Label Quantity' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="show_label_quantity" name="show_label_quantity">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0" selected="selected">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                    </div>
                </div>
            </div>
            <div id="more_image">
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Size Image' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="size" name="size">
                            {foreach $input.imageType item=value}
                                <option value="{$value.name}"{($value.name == "home_default") ? " selected" : ""}>{$value.name} ({$value.width}px x {$value.height}px)</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Type' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="type" name="type">
                            {foreach $input.type_more_image key=key item=value}
                                <option value="{$key}">{$value}</option>
                            {/foreach}                     
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Show Dots' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="dots" name="dots">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0" selected="selected">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Center Mode' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="centermode" name="centermode">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0" selected="selected">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Lazyload' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="lazyload" name="lazyload">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Scroll Mouse Wheel' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="mousewheel" name="mousewheel">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0" selected="selected">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Effect Fade' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="use-fade" name="fade">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0" selected="selected">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Slides To Show' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <input type="text" name="slidestoshow" class="slidestoshow" value="4"> 
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Display responsive for other screen' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <input type="text" name="responsive" class="responsive" value=""> 
                        <p class="help-block">{l s='(Advance User) Example: [[1200, 5],[992, 4],[768, 3], [576, 2]]. The format is [x,y] whereby x=browser width and y=number of slides displayed' mod='deotemplate'}</p>
                    </div>
                </div>
            </div>
            <div id="attribute">
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Show name attribute' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="show_name_attribute" name="show_name_attribute">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Display value attribute type text' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="show_value_text" name="show_value_text">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                        <p class="help-block">{l s='Choose No to display attribute by radio' mod='deotemplate'}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-5">{l s='Display attribute color as view' mod='deotemplate'}</label>
                    <div class="col-lg-5">
                        <select class="show_color" name="show_color">
                            <option value="1">{l s='Yes' mod='deotemplate'}</option>
                            <option value="0">{l s='No' mod='deotemplate'}</option>                       
                        </select>
                        <p class="help-block">{l s='Choose No to display attribute color by text' mod='deotemplate'}</p>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var title_modal = {
                product_thumbnail : "{l s='Configure Primary Image' mod='deotemplate'}",
                more_image : "{l s='Configure Thumbnail Image' mod='deotemplate'}",
            };

            $(document).ready(function() {  
                $(".display-grid").DeoMasonry({
                    minWidth: '280px',
                });
            });
        </script>
    {/if}
    {$smarty.block.parent}
{/block}