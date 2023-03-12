{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

{assign var="path_widget_base" value="`$path_widget_base`widget.tpl"}
{extends file=$path_widget_base}

{function name=category level=0}
{if isset($disable_html_tree_structure) && $disable_html_tree_structure}
    {foreach $data as $category}
        <li class="cate-item{if isset($category.deo_count) && $category.deo_count >= $limit} hidden-cate-item{/if}" {if isset($category.deo_count) && $category.deo_count >= $limit}style="display: none;"{/if} data-id="{$category.id_category|intval}">
            {if isset($category.image)}
                <div class="cover-img">
                    <a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)}"  title="{$category.name}">
                        {if isset($category.rate_image) && isset($lazyload) && $lazyload && !$backoffice}
                            <span class="lazyload-wrapper" style="padding-bottom: {$category.rate_image};">
                                <span class="lazyload-icon"></span>
                            </span>
                            <img class="img-fluid lazyload" data-src='{$category["image"]}' src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt='{$category["name"]}'/>
                        {else}
                            <img class="img-fluid" src='{$category["image"]}' alt='{$category["name"]}'/>
                        {/if}
                    </a>
                </div>
            {/if}
            <div class="cate-meta">
                <div class="box-cate">
                    <h4 class="cate-name"><a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)}" title="{$category.name}">{$category["name"]}</a></h4>
                    {if isset($quantity) && $quantity}
                        <span data-id="{$category.id_category}" class="deo-qty-category deo-cat-{$category.id_category}" data-str="{l s=' items' mod='deotemplate'}"></span>
                    {/if}
                    {if isset($description) && $description}
                        <div class="description">{$category.description|truncate:120 nofilter}</div>
                    {/if}
                </div>
            </div>
        </li>
    {/foreach}
{else}
    <ul class="level{$level|intval} cate-items">
        {foreach $data as $category}
            <li class="cate-item{if isset($category.deo_count) && $category.deo_count >= $limit} hidden-cate-item{/if}" {if isset($category.deo_count) && $category.deo_count >= $limit}style="display: none;"{/if} data-id="{$category.id_category|intval}">
                <div class="cate_content">
                    {if isset($category.children) && is_array($category.children)}
                        {if isset($category.image)}
                            <div class="cover-img">
                                <a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)}" title="{$category.name}">
                                    {if isset($category.rate_image) && isset($lazyload) && $lazyload && !$backoffice}
                                        <span class="lazyload-wrapper" style="padding-bottom: {$category.rate_image};">
                                            <span class="lazyload-icon"></span>
                                        </span>
                                        <img class="img-fluid lazyload" data-src='{$category["image"]}' src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt='{$category["name"]}'/>
                                    {else}
                                        <img class="img-fluid" src='{$category["image"]}' alt='{$category["name"]}'/>
                                    {/if}
                                </a>
                            </div>
                        {/if}
                        <div class="cate-meta">
                            <div class="box-cate">
                                <h4 class="cate-name"><a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)}" title="{$category.name}">{$category.name}</a></h4>
                                {if isset($quantity) && $quantity}
                                    <span data-id="{$category.id_category}" class="deo-qty-category deo-cat-{$category.id_category}" data-str="{l s=' items' mod='deotemplate'}"></span>
                                {/if}
                                {if isset($description) && $description}
                                    <div class="description">{$category.description|truncate:120 nofilter}</div>
                                {/if}
                            </div>
                            {category data=$category.children level=$level+1}
                        </div>
                    {else}
                        {if isset($category.image)}
                            <div class="cover-img">
                                <a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)}"  title="{$category.name}">
                                    {if isset($category.rate_image) && isset($lazyload) && $lazyload && !$backoffice}
                                        <span class="lazyload-wrapper" style="padding-bottom: {$category.rate_image};">
                                            <span class="lazyload-icon"></span>
                                        </span>
                                        <img class="img-fluid lazyload" data-src='{$category["image"]}' src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt='{$category["name"]}'/>
                                    {else}
                                        <img class="img-fluid" src='{$category["image"]}' alt='{$category["name"]}'/>
                                    {/if}
                                </a>
                            </div>
                        {/if}
                        <div class="cate-meta">
                            <div class="box-cate">
                                <h4 class="cate-name"><a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)}" title="{$category.name}">{$category.name}</a></h4>
                                {if isset($quantity) && $quantity}
                                    <span data-id="{$category.id_category}" class="deo-qty-category deo-cat-{$category.id_category}" data-str="{l s=' items' mod='deotemplate'}"></span>
                                {/if}
                                {if isset($description) && $description}
                                    <div class="description">{$category.description|truncate:120 nofilter}</div>
                                {/if}
                            </div>
                        </div>
                    {/if}
                </div>
            </li>
        {/foreach}
    </ul>
{/if}
{/function}

{block name='widget-content'}
    {if isset($categories)}
        <div class="widget-category_image block {if isset($class)}{$class}{/if} {if isset($sub_title) && $sub_title}has-sub-title{/if}{if isset($description) && $description} show-description{/if}{if isset($disable_html_tree_structure) && $disable_html_tree_structure} disable-html-tree-structure{/if}" 
            data-limit="{$limit|intval}" 
            data-cate_depth="{$cate_depth|intval}" 
            data-viewall="{(isset($viewall) && $viewall) ? $viewall : 0}" 
            data-link_viewall="{(isset($link_viewall) && $link_viewall) ? $link_viewall : 0}"  
        >
            <div class="block_content">
                {if isset($carousel_type) && $carousel_type == 'slickcarousel'}
                    {if !empty($categories)}
                        {include file=$deo_helper->getTplTemplate('DeoCategoryImageSlickCarousel.tpl', $data['override_folder'])}
                    {else}
                        <p class="alert alert-info">{l s='No slide at this time.' mod='deotemplate'}</p>
                    {/if}
                {else}     
                    {if isset($disable_html_tree_structure) && $disable_html_tree_structure}  
                         <ul class="cate-items">
                            {foreach from=$categories key=key item=cate}
                                {category data=$cate}
                            {/foreach}
                        </ul>
                    {else}
                        {foreach from=$categories key=key item=cate}
                            {category data=$cate}
                        {/foreach}
                    {/if}
                    {if $limit > 1 && isset($viewall) && $viewall}
                        <div class="view_all_wapper" {if $total <= $limit}style="display: none;"{/if}>
                            <div class="view_all">
                                <a class="btn{if isset($link_viewall) && $link_viewall}{else} active-js-view-all{/if}" href="{if isset($link_viewall) && $link_viewall}{$link_viewall}{else}javascript:void(0){/if}" data-hide-less="{l s='Hide Less' mod='deotemplate'}" data-view-more="{if isset($text_link_viewall) && $text_link_viewall}{$text_link_viewall}{else}{l s='View more' mod='deotemplate'}{/if}">{if isset($text_link_viewall) && $text_link_viewall}{$text_link_viewall}{else}{l s='View more' mod='deotemplate'}{/if}</a>
                            </div>
                        </div> 
                    {/if}
                {/if}
            </div>
        </div>
    {/if}
{/block}