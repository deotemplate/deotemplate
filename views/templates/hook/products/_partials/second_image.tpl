{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}
{if isset($second_img) && $second_img && count($product.images) >= 2}
  {foreach from=$product.images item=image}
    {if $image.id_image != $product.default_image.id_image}
      <span class="deo-second-img pro">
        <img
          class="img-fluid {if isset($formAtts.carousel_type) && $formAtts.carousel_type == "slickcarousel"}{else}lazyload{/if}"
          {if (isset($formAtts.carousel_type) && $formAtts.carousel_type == "slickcarousel")}data-lazy{else}data-src{/if}="{$image.bySize[$deo_size].url}"
          src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
          alt = "{if !empty($image.legend)}{$image.legend}{else}{$product.name}{/if}"
        >
      </span>
      {break}
    {/if}
  {/foreach}
{/if}