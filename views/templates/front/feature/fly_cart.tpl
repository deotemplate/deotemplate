{* 
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
*}

<div data-type="{$type_fly_cart}" style="position: fixed; {$position_vertical_flycart}:{$position_vertical_value_flycart}; {$position_horizontal_flycart}:{$position_horizontal_value_flycart}" class="deo-cart-solo solo {($type_fly_cart == 'dropup' || $type_fly_cart == 'dropdown') ? ' enable-dropdown' : ''}{($type_fly_cart == 'slidebar_top' || $type_fly_cart == 'slidebar_bottom' || $type_fly_cart == 'slidebar_right' || $type_fly_cart == 'slidebar_left') ? ' enable-slidebar' : ''}">
	<div class="icon-cart-sidebar-wrapper">
		<a href="javascript:void(0)" class="icon-cart-sidebar" data-type="{$type_fly_cart}"></a>
		<span class="icon-cart-total">0</span>
	</div>
	<div class="deo-icon-cart-loading"></div>
</div>