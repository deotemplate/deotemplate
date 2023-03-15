<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


if (!class_exists('DeoWidgetModel')) {
    class DeoWidgetModel extends ObjectModel
    {
        public $name;
        public $type;
        public $params;
        public $key_widget;
        public $id_shop;
        private $widgets = array();
        public $modName = 'deotemplate';
        public $theme = '';
        public $langID = 1;
        public $engines = array();
        public $engineTypes = array();

        public function setTheme($theme)
        {
            $this->theme = $theme;
            return $this;
        }
        public static $definition = array(
            'table' => 'deomegamenu_widgets',
            'primary' => 'id_deomegamenu_widgets',
            'fields' => array(
                'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true),
                'type' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
                'params' => array('type' => self::TYPE_HTML, 'validate' => 'isString'),
                'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
                'key_widget' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'size' => 11)
            )
        );

        /**
         * Get translation for a given module text
         *
         * Note: $specific parameter is mandatory for library files.
         * Otherwise, translation key will not match for Module library
         * when module is loaded with eval() Module::getModulesOnDisk()
         *
         * @param string $string String to translate
         * @param boolean|string $specific filename to use in translation key
         * @return string Translation
         */
        public function l($string, $specific = false)
        {
            return Translate::getModuleTranslation($this->modName, $string, ($specific) ? $specific : $this->modName);
        }
        
        public function update($null_values = false)
        {
            // validate module
            unset($null_values);
            return Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'deomegamenu_widgets SET `name`= "'.pSQL($this->name).'", `type`= "'.pSQL($this->type).'", `params`= "'.pSQL($this->params).'", `id_shop` = '.(int)$this->id_shop.', `key_widget` = '.(int)$this->key_widget.' WHERE `id_deomegamenu_widgets` = '.(int)$this->id.' AND `id_shop` = '.(int)Context::getContext()->shop->id);
        }

        public function delete()
        {
            return parent::delete();
        }

        public function loadEngines()
        {
            $this->id_shop = Context::getContext()->shop->id;
            $this->langID = Context::getContext()->language->id;
            if (!$this->engines) {
                $wds = glob(dirname(__FILE__).'/widget/*.php');
                foreach ($wds as $w) {
                    if (basename($w) == 'index.php') {
                        continue;
                    }
                    require_once($w);
                    $f = str_replace('.php', '', basename($w));
                    //validate module
                    $validate_class = str_replace('_', '', $f);
                    $class = 'DeoWidget'.Tools::ucfirst($validate_class);

                    if (class_exists($class)) {
                        $this->engines[$f] = new $class;
                        $this->engines[$f]->id_shop = Context::getContext()->shop->id;
                        $this->engines[$f]->langID = Context::getContext()->language->id;
                        $this->engineTypes[$f] = $this->engines[$f]->getWidgetInfo();
                        $this->engineTypes[$f]['type'] = $f;
                        $this->engineTypes[$f]['for'] = $this->engines[$f]->for_module;
                    }
                }
            }
        }

        /**
         * get list of supported widget types.
         */
        public function getTypes()
        {
            return $this->engineTypes;
        }

        /**
         * get list of widget rows.
         */
        public function getWidgets()
        {
            $sql = ' SELECT * FROM '._DB_PREFIX_.'deomegamenu_widgets WHERE `id_shop` = '.(int)Context::getContext()->shop->id;
            return Db::getInstance()->executeS($sql);
        }

        /**
         * get widget data row by id
         */
        public function getWidetById($id, $id_shop)
        {
            $output = array(
                'id' => '',
                'id_deomegamenu_widgets' => '',
                'name' => '',
                'params' => '',
                'type' => '',
            );
            if (!$id) {
                # validate module
                return $output;
            }
            $sql = ' SELECT * FROM '._DB_PREFIX_.'deomegamenu_widgets WHERE id_deomegamenu_widgets='.(int)$id.' AND id_shop='.(int)$id_shop;

            $row = Db::getInstance()->getRow($sql);

            if ($row) {
                $output = array_merge($output, $row);
                $output['params'] = json_decode(call_user_func('base64'.'_decode', $output['params']), true);
                $output['id'] = $output['id_deomegamenu_widgets'];
            }
            return $output;
        }

        /**
         * get widget data row by id
         */
        public function getWidetByKey($key, $id_shop)
        {
            $output = array(
                'id' => '',
                'id_deomegamenu_widgets' => '',
                'name' => '',
                'params' => '',
                'type' => '',
                'key_widget' => '',
            );
            if (!$key) {
                # validate module
                return $output;
            }
            $sql = ' SELECT * FROM '._DB_PREFIX_.'deomegamenu_widgets WHERE key_widget='.(int)$key.' AND id_shop='.(int)$id_shop;
            $row = Db::getInstance()->getRow($sql);
            if ($row) {
                $output = array_merge($output, $row);
                $output['params'] = json_decode(call_user_func('base64'.'_decode', $output['params']), true);
                $output['id'] = $output['id_deomegamenu_widgets'];
            }
            return $output;
        }

        /**
         * render widget Links Form.
         */
        public function getWidgetInformationForm($args, $data)
        {
            $fields = array(
                'html' => array('type' => 'textarea', 'value' => '', 'lang' => 1, 'values' => array(), 'attrs' => 'cols="40" rows="6"')
            );
            unset($args);
            return $this->_renderFormByFields($fields, $data);
        }

        public function renderWidgetSubcategoriesContent($args, $setting)
        {
            # validate module
            unset($args);
            $t = array(
                'category_id' => '',
                'limit' => '12'
            );
            $setting = array_merge($t, $setting);
//            $nb = (int)$setting['limit'];

            $category = new Category($setting['category_id'], $this->langID);
            $subCategories = $category->getSubCategories($this->langID);
            $setting['title'] = $category->name;


            $setting['subcategories'] = $subCategories;
            $output = array('type' => 'sub_categories', 'data' => $setting);

            return $output;
        }

        /**
         * general function to render FORM
         *
         * @param String $type is form type.
         * @param Array default data values for inputs.
         *
         * @return Text.
         */
        public function getForm($type, $data = array())
        {
            if (isset($this->engines[$type])) {
                $args = array();
                $this->engines[$type]->types = $this->getTypes();

                return $this->engines[$type]->renderForm($args, $data);
            }
            return $this->l('Sorry, Form Setting is not avairiable for this type');
        }

        /**
         *
         */
        public function getWidgetContent($type, $data, $key_widget = null)
        {
            // $method = 'renderWidget'.Tools::ucfirst($type).'Content';
            $args = array();
            $data = json_decode(call_user_func('base64'.'_decode', $data), true);
            $data['widget_heading'] = isset($data['widget_title_'.$this->langID]) ? Tools::stripslashes($data['widget_title_'.$this->langID]) : '';
            $data['link_title'] = isset($data['link_title_'.$this->langID]) ? Tools::stripslashes($data['link_title_'.$this->langID]) : '';
            $data['key_widget'] = $key_widget;
            $data['name'] = $type;

            if (isset($data['icon_use_image_link']) && $data['icon_use_image_link']){
                // $data['icon_image'] = isset($data['icon_image_link_'.$this->langID]) ? Tools::stripslashes($data['icon_image_link_'.$this->langID]) : '';
                $data['icon_image'] = isset($data['icon_image_link']) ? Tools::stripslashes($data['icon_image_link']) : '';
            }else{
                // $image = isset($data['icon_image_'.$this->langID]) ? Tools::stripslashes($data['icon_image_'.$this->langID]) : '';
                if (isset($data['icon_image']) && $data['icon_image']){
                    $image = DeoHelper::getImgThemeUrl().Tools::stripslashes($data['icon_image']);
                    $data['icon_image'] = $image;
                }else{
                    $data['icon_image'] = '';
                }
            }
            if (DeoHelper::getLazyload()){
                // $data['icon_rate_image'] = isset($data['icon_rate_image_'.$this->langID]) ? Tools::stripslashes($data['icon_rate_image_'.$this->langID]) : '';
                $data['icon_rate_image'] = isset($data['icon_rate_image']) ? Tools::stripslashes($data['icon_rate_image']) : '';
                if (isset($data['icon_rate_image']) && $data['icon_rate_image']){
                    $data['icon_rate_image'] = $data['icon_rate_image'].'%';
                }
            }else{
                $data['icon_lazyload'] = 0;
            }

            $data['path_widget_base'] = _PS_MODULE_DIR_.'deotemplate/views/templates/hook/megamenu/';


            //echo $method;
            if (isset($this->engines[$type])) {
                $args = array();
                return $this->engines[$type]->renderContent($args, $data);
            }
            return false;
        }

        /**
         *
         */
        public function renderContent($id)
        {
            $output = array('id' => $id, 'type' => '', 'data' => '');
            if (isset($this->widgets[$id])) {
                # validate module
                $output = $this->getWidgetContent($this->widgets[$id]['type'], $this->widgets[$id]['params'], $id);
            }

            return $output;
        }

        /**
         *
         */
        public function loadWidgets()
        {
            if (empty($this->widgets)) {
                $widgets = $this->getWidgets();
                foreach ($widgets as $widget) {
                    $widget['id'] = $widget['id_deomegamenu_widgets'];
                    $this->widgets[$widget['key_widget']] = $widget;
                }
            }
        }

        /**
         * Load widget (data + html)
         */
        public function loadWidgetsData($backoffice = 0)
        {
            $new_array = array();
            $widgets = $this->getWidgets();
            $this->loadEngines();
            
            foreach ($widgets as &$wid) {
                if ($backoffice && $wid['type'] == 'product_list' && Shop::getTotalShops() > 1){
                    $data = json_decode(call_user_func('base64'.'_decode', $wid['params']), true);
                    $data['widget_heading'] = isset($data['widget_title_'.Context::getContext()->language->id]) ? Tools::stripslashes($data['widget_title_'.Context::getContext()->language->id]) : '';
                    $data['link_title'] = isset($data['link_title_'.Context::getContext()->language->id]) ? Tools::stripslashes($data['link_title_'.Context::getContext()->language->id]) : '';
                    $data['key_widget'] = $wid['key_widget'];
                    $data['name'] = $wid['type'];

                    if (isset($data['icon_use_image_link']) && $data['icon_use_image_link']){
                        // $data['icon_image'] = isset($data['icon_image_link_'.Context::getContext()->language->id]) ? Tools::stripslashes($data['icon_image_link_'.Context::getContext()->language->id]) : '';
                        $data['icon_image'] = isset($data['icon_image_link']) ? Tools::stripslashes($data['icon_image_link']) : '';
                    }else{
                        // $image = isset($data['icon_image_'.Context::getContext()->language->id]) ? Tools::stripslashes($data['icon_image_'.Context::getContext()->language->id]) : '';
                        if (isset($data['icon_image']) && $data['icon_image']){
                            $image = DeoHelper::getImgThemeUrl().Tools::stripslashes($data['icon_image']);
                            $data['icon_image'] = $image;
                        }else{
                            $data['icon_image'] = '';
                        }
                    }
                    if (DeoHelper::getLazyload()){
                        // $data['icon_rate_image'] = isset($data['icon_rate_image_'.Context::getContext()->language->id]) ? Tools::stripslashes($data['icon_rate_image_'.Context::getContext()->language->id]) : '';
                        $data['icon_rate_image'] = isset($data['icon_rate_image']) ? Tools::stripslashes($data['icon_rate_image']) : '';
                        if (isset($data['icon_rate_image']) && $data['icon_rate_image']){
                            $data['icon_rate_image'] = $data['icon_rate_image'].'%';
                        }
                    }else{
                        $data['icon_lazyload'] = 0;
                    }

                    $data['path_widget_base'] = _PS_MODULE_DIR_.'deotemplate/views/templates/hook/megamenu/';
                    $data['backoffice'] = $backoffice;

                    Context::getContext()->smarty->assign($data);
                    Context::getContext()->smarty->assign('id_widget', $wid['key_widget']);

                    // $output = $this->display(__FILE__, 'views/templates/hook/megamenu/widgets/widget_'.$type.'.tpl');
                    $wid['html'] = Context::getContext()->smarty->fetch(_PS_MODULE_DIR_.'deotemplate/views/templates/hook/megamenu/widgets/widget_product_list_backoffice.tpl');
                }else{
                    $content = $this->getWidgetContent($wid['type'],$wid['params'],$wid['key_widget']);
                    $content['data'] = array_merge($content['data'], array('backoffice' => $backoffice));
                    $wid['html'] = $this->getContentWidget($wid['key_widget'], $content['type'], $content['data']);
                }

                $new_array[$wid['key_widget']] = $wid;
            }

            return $new_array;
        }


        /**
         * gen content to html
         */
        public function getContentWidget($id, $type, $data, $show_widget_id = 1)
        {
            # validate module
            unset($show_widget_id);
            // $widgets = $this->getWidgets();
            // print_r($widgets);
            // $type_menu = array('carousel', 'categoriestabs', 'manucarousel', 'map', 'producttabs', 'tab', 'accordion', 'specialcarousel');
            // foreach ($widgets as $key => $widget) {
            //     if (in_array($widget['type'], $type_menu)) {
            //         unset($widgets[$key]);
            //     }
            // }
            Context::getContext()->smarty->assign($data);
            Context::getContext()->smarty->assign('id_widget', $id);
            // Context::getContext()->smarty->assign('widgets',$widgets);

            // $output = $this->display(__FILE__, 'views/templates/hook/megamenu/widgets/widget_'.$type.'.tpl');
            $output = Context::getContext()->smarty->fetch(_PS_MODULE_DIR_.'deotemplate/views/templates/hook/megamenu/widgets/widget_'.$type.'.tpl');
            
            return $output;
        }
    }
}
