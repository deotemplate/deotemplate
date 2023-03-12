<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


include_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoShortCodeBase.php');
include_once(_PS_MODULE_DIR_.'deotemplate/classes/shortcodes/DeoProductList.php');

class DeoTemplateAjaxModuleFrontController extends ModuleFrontController
{
    /**
    * @see FrontController::initContent()
    */

    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();
    }

    public function displayAjax()
    {
        $module = new DeoTemplate();

        if (Tools::getValue('load-ajax') == 1) {
            # process category
            $qty_category = Tools::getValue('qty_category');
            $more_product_img = Tools::getValue('more_product_img');
            $second_img = Tools::getValue('second_img');
            $countdown = Tools::getValue('countdown');
            $color = Tools::getValue('color');

            // add function wishlist compare
            $wishlist = Tools::getValue('wishlist');
            $compare = Tools::getValue('compare');

            $result = array();

            //get number product of compare + wishlist
            if ($wishlist) {
                $total_wishlist = 0;
                if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_WISHLIST') && isset(Context::getContext()->cookie->id_customer)) {
                    $current_user = (int)Context::getContext()->cookie->id_customer;
                    $list_wishlist = Db::getInstance()->executeS("SELECT id_wishlist FROM `"._DB_PREFIX_."deofeature_wishlist` WHERE id_customer = '" . (int)$current_user."'");
                    foreach ($list_wishlist as $list_wishlist_item) {
                        $number_product_wishlist = Db::getInstance()->getValue("SELECT COUNT(id_wishlist_product) FROM `"._DB_PREFIX_."deofeature_wishlist_product` WHERE id_wishlist = ".(int)$list_wishlist_item['id_wishlist']);
                        $total_wishlist += $number_product_wishlist;
                    }
                    // $total_wishlist = Db::getInstance()->getValue("SELECT COUNT(id_wishlist_product) FROM `"._DB_PREFIX_."wishlist_product` WHERE id_wishlist = '$id_wishlist'");
                }
                $result['total_wishlist'] = $total_wishlist;
            }

            if ($compare) {   
                $total_compare = 0;
                if ((int) DeoHelper::getConfig('ENABLE_PRODUCT_COMPARE') && (int) DeoHelper::getConfig('COMPARATOR_MAX_ITEM') > 0 && isset(Context::getContext()->cookie->id_compare)) {
                    $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                        SELECT DISTINCT `id_product`
                        FROM `'._DB_PREFIX_.'deofeature_compare` c
                        LEFT JOIN `'._DB_PREFIX_.'deofeature_compare_product` cp ON (cp.`id_compare` = c.`id_compare`)
                        WHERE cp.`id_compare` = '.(int)(Context::getContext()->cookie->id_compare));
                    $total_compare = count($sql);
                }
                $result['total_compare'] = $total_compare;
            }

            if ($qty_category) {
                $qty_category = explode(',', $qty_category);
                $qty_category = array_filter($qty_category);
                $qty_category = array_unique($qty_category);
                $qty_category = array_map('intval', $qty_category); // fix sql injection
                $qty_category = implode(',', $qty_category);

                $sql = 'SELECT COUNT(cp.`id_product`) AS total, cp.`id_category` FROM `'._DB_PREFIX_.'product` p '.Shop::addSqlAssociation('product', 'p').'
                        LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
        				WHERE cp.`id_category` IN ('.pSQL($qty_category).')
                        AND product_shop.`visibility` IN ("both", "catalog")
                        AND product_shop.`active` = 1
                        GROUP BY cp.`id_category`';
                $category = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                if ($category) {
                    $result['category'] = $category;
                }
            }

            if ($countdown) {
                $countdown = explode(',', $countdown);
                $countdown = array_unique($countdown);
                $countdown = implode(',', $countdown);
                $result['countdown'] = $module->hookProductCdown($countdown);
            }

            if ($more_product_img) {
                $more_product_img = explode(',', $more_product_img);
                $more_product_img = array_unique($more_product_img);
                $more_product_img = implode(',', $more_product_img);

                $result['more_product_img'] = $module->hookProductMoreImg($more_product_img);
            }
            
            if ($second_img) {
                $second_img = explode(',', $second_img);
                $second_img = array_unique($second_img);
                $second_img = implode(',', $second_img);

                $result['second_img'] = $module->hookProductOneImg($second_img);
            }

            if ($result) {
                die(json_encode($result));
            }
        }

        if (Tools::getValue('load-sample-google') == 1) {
            $result = array(
                'success' => false
            );
            $filecontent = Tools::file_get_contents(_PS_ROOT_DIR_.'/'.DeoHelper::getJsDir().'googlewebfont.json');
            $result['success'] = true;
            $result['filecontent'] = $filecontent;

            die(json_encode($result));
        }

        if (Tools::getValue('load-custom-font') == 1) {
            $primary_font = Tools::getValue('primary_font');
            $second_font = Tools::getValue('second_font');

            $result = array(
                'success' => false
            );

            $uri = DeoHelper::getCssDir().'skins/skin-font.css';
            $filecontent = Tools::file_get_contents(_PS_THEME_DIR_.'/'.$uri);
            $filecontent = str_replace(["font-family-base", "font-family-heading"], [$primary_font, $second_font], $filecontent);

            $result['success'] = true;
            $result['filecontent'] = $filecontent;

            die(json_encode($result));
        }

        if (Tools::getValue('load-custom-skin') == 1) {
            $primary_color = Tools::getValue('primary_color');
            $second_color = Tools::getValue('second_color');
            $rgb_primary_color = preg_match('/\((.*?)\)/i', DeoHelper::convertHexToRgb($primary_color), $match_primary_color);
            $rgb_second_color = preg_match('/\((.*?)\)/i', DeoHelper::convertHexToRgb($second_color), $match_second_color);

            $result = array(
                'success' => false
            );

            $uri = DeoHelper::getCssDir().'skins/skin-color.css';
            $filecontent = Tools::file_get_contents(_PS_THEME_DIR_.'/'.$uri);
            $filecontent = str_replace(["#1bbc9b", "#169a7f", "27, 188, 155", "22, 154, 127"], [$primary_color, $second_color, $match_primary_color[1], $match_second_color[1]], $filecontent);

            $result['success'] = true;
            $result['filecontent'] = $filecontent;

            die(json_encode($result));
        }

        if (Tools::getValue('widget') == 'DeoImageGallery') {
            $show_number = Tools::getValue('show_number');
            $assign = Tools::getValue('assign', array());
            $assign = json_decode($assign, true);
            
            $show_number_new = $show_number;
            $form_atts = $assign['formAtts'];

            $limit = (int)$form_atts['limit'] + $show_number;
            $images = array();
            $link = new Link();
            $current_link = $link->getPageLink('', false, Context::getContext()->language->id);
            $path = _PS_ROOT_DIR_.'/'.str_replace($current_link, '', isset($form_atts['path']) ? $form_atts['path'] : '');
            $arr_exten = array('jpg', 'jpge', 'gif', 'png');

                
            $count = 0;
            if ($path && is_dir($path)) {
                if ($handle = scandir($path)) {

                    if (($key = array_search('.', $handle)) !== false) {
                        unset($handle[$key]);
                    }
                    if (($key = array_search('..', $handle)) !== false) {
                        unset($handle[$key]);
                    }
 
                    foreach ($handle as $entry) {
                        if ($entry != '.' && $entry != '..' && is_file($path.'/'.$entry)) {
                            $ext = pathinfo($path.'/'.$entry, PATHINFO_EXTENSION);
                            if (in_array($ext, $arr_exten)) {
                                $url = __PS_BASE_URI__.'/'.str_replace($current_link, '', $form_atts['path']).'/'.$entry;
                                $url = str_replace('//', '/', $url);

                                if($count >= $show_number){
                                    $images[] = $url;
                                    $show_number_new++;
                                }
                                $count++;
                                if($count == $limit){
                                    break;
                                }
                            }
                        }
                    }
                }
            }
                
            $total = count($handle);
            $total_nerver_show = (int)( $total - $count );
            $c = (int)$form_atts['columns'];
            $assign['columns'] = $c > 0 ? $c : 4;
            
            $result = array(
                'images' => array(),
                'show_number' => -1,
                'hasError' => 0,
                'errors' => array(),
            );
            
            $result['images'] = $images;
            if($total_nerver_show > 0){
                $result['show_number'] = $show_number_new;
            }
            die(json_encode($result));
        }

        if (Tools::getValue('widget') == 'DeoProductList') {
            $obj = new DeoProductList();
            $result = $obj->ajaxProcessRender($module);
            die(json_encode($result));
        }

        if (Tools::getValue('widget') == 'DeoProductTabs') {
            $obj = new DeoProductTabs();
            $result = $obj->ajaxRenderProductCarousel($module);
            die(json_encode($result));
        }
    } 
}
