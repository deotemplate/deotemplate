{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{block name='page_content_container'}
  <section class="page-content" id="content">
    {block name='page_content'}
      {block name='product_cover_thumbnails'}
        {include file='catalog/_partials/product-cover-thumbnails.tpl' col_thumbnail="col-thumbnail" size="home_default" lazyload=true gallery=false}
      {/block}
    {/block}
  </section>
{/block}

{block name='product_images_modal'}
  {include file='catalog/_partials/product-images-modal.tpl'}
{/block}