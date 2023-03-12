{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

{assign var="path_widget_base" value="`$path_widget_base`widget.tpl"}
{extends file=$path_widget_base}

{block name='widget-content'}
    {if isset($tabhtmls)}
        <div class="block_content">
            <div id="tabhtmls{$id}" class="{if isset($vertical) && $vertical}nav-vertical clearfix{/if}">
                <ul class="nav nav-tabs">
                    {foreach $tabhtmls as $key => $ac}  
                        <li class="nav-item">
                            <a href="#tabhtml{$id}{$key}" class="nav-link{if $key==0} active{/if} nav-tab-link">{$ac.title}</a>
                        </li>
                    {/foreach}
                </ul>
                <div class="tab-content">
                    {foreach $tabhtmls as $key => $ac}
                        <div class="tab-pane{if $key==0} active{/if} " id="tabhtml{$id}{$key}">{$ac.content nofilter}</div>
                    {/foreach}
                </div>
            </div>
            <script type="text/javascript">
                $('#tabhtmls{$id} a').on('click', function (e) {
                    e.preventDefault();
                    $(this).tab('show');
                })
            </script>
        </div>
    {/if}
{/block}