<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


if (!class_exists("DeoFrameworkHelper")) {

    /**
     * DeoFrameworkHelper Class
     */
    class DeoFrameworkHelper
    {
        /**
         * @var Array $overrideHooks;
         *
         * @access protected
         */
        protected $overrideHooks = array();
        /**
         * @var String $activedTheme
         *
         * @access protected
         */
        protected $activedTheme = '';
        /**
         * @var boolean $isLangRTL
         *
         * @access protected
         */
        protected $isLangRTL = false;
        protected $cparams = array();
        protected $fonts = array();

        /**
         * get instance of current object
         */
        public static function getInstance()
        {
            static $_instance;
            if (!$_instance) {
                $_instance = new DeoFrameworkHelper();
            }
            return $_instance;
        }

        public function __construct()
        {
            
        }

        // public static function getHookPositions()
        // {

        //    $hookspos = array(
        //        'displayNav',
        //        'displayTop',
        //        'displayHeaderRight',
        //        'displaySlideshow',
        //        'topNavigation',
        //        'displayTopColumn',
        //        'displayRightColumn',
        //        'displayLeftColumn',
        //        'displayHome',
        //        'displayFooter',
        //        'displayBottom',
        //        'displayContentBottom',
        //        'displayFootNav',
        //        'displayFooterTop',
        //        'displayFooterBottom'
        //    );
        //    return $hookspos;
        // }

        /**
         * Set actived theme and language direction
         */
        public function setActivedTheme($theme, $isRTL = false)
        {
            $this->activedTheme = $theme;
            $this->isLangRTL = $isRTL;
            return $this;
        }


        /**
         * save data into framework
         */
        public static function writeToCache($folder, $file, $value, $e = 'css')
        {
            $file = $folder.preg_replace('/[^A-Z0-9\._-]/i', '', $file).'.'.$e;
            $handle = fopen($file, 'w+');
            fwrite($handle, ($value));
            fclose($handle);
        }


        /**
         *  auto load all css file local folder
         */
        public function loadLocalCss()
        {
            return $this->getFileList(_PS_ALL_THEMES_DIR_.$this->activedTheme.'/css/local/', '.css');
        }

        /**
         *  auto load all js file local folder
         */
        public function loadLocalJs()
        {
            return $this->getFileList(_PS_ALL_THEMES_DIR_.$this->activedTheme.'/js/local/', '.js');
        }

        public static function getThemeInfo()
        {
            $xml = DeoHelper::getThemeDir().'/config.xml';

            $output = array();

            if (file_exists($xml)) {
                $output = simplexml_load_file($xml);
            }

            return $output;
        }

        public function getParam($key, $value = "")
        {
            return $this->cparams[$this->activedTheme."_".$key];
        }

    
        /**
         * get URI with http or https
         */
        public function getURI()
        {

            $useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
            $protocol_content = ($useSSL) ? 'https://' : 'http://';

            return $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
        }


        /**
         * get list of filename inside folder
         */
        public static function getFileList($path, $e = null, $nameOnly = false)
        {
            $output = array();
            $directories = glob($path.'*'.$e);
            if ($directories)
                foreach ($directories as $dir) {
                    $dir = basename($dir);
                    if ($nameOnly) {
                        $dir = str_replace($e, '', $dir);
                    }

                    $output[$dir] = $dir;
                }

            return $output;
        }

        public static function getUserProfiles()
        {

            $folder = DeoHelper::getThemeDir().'/css/customize/*.css';
            $dirs = glob($folder);
            $output = array();
            if ($dirs)
                foreach ($dirs as $dir) {
                    $file = str_replace(".css", "", basename($dir));
                    $output[] = array("skin" => $file, "name" => (Tools::ucfirst($file)));
                }

            return $output;
        }

        public static function getLayoutDirections()
        {
            $folder = DeoHelper::getThemeDir().'/layout/*';
            $dirs = glob($folder, GLOB_ONLYDIR);
            $output = array();
            foreach ($dirs as $dir) {
                $file = str_replace(".scss", "", basename($dir));
                $output[] = array("id" => $file, "name" => (Tools::ucfirst($file)));
            }

            return $output;
        }

        public static function getSkins()
        {
            $folders = array();
            $folders[] = DeoHelper::getThemeDir().DeoHelper::getCssDir().'skins/*';
            $output = array();
            foreach ($folders as $folder) {
                $dirs = glob($folder, GLOB_ONLYDIR);
                $output = array();
                if ($dirs) {
                    $i = 0;
                    foreach ($dirs as $dir) {
                        $output[$i]['id'] = basename($dir);
                        $output[$i]['name'] = Tools::ucfirst(basename($dir));
                        $skinFileUrl = DeoHelper::getUriFromPath($dir).'/';

                        if (file_exists($dir.'/icon.png')) {
                            $output[$i]['icon'] = $skinFileUrl.'icon.png';
                        }
                        $output[$i]['css'] = $skinFileUrl;
                        
                        $i++;
                    }
                }
                if (!empty($output)) {
                    break;
                }
            }
            return $output;
        }

        public static function getBlogStyles()
        {
            $folders = array();
            $folders[] = _PS_MODULE_DIR_.'deotemplate/views/templates/front/blog/*';
            $output = array();
            foreach ($folders as $folder) {
                $dirs = glob($folder, GLOB_ONLYDIR);
                $output = array();
                if ($dirs) {
                    foreach ($dirs as $dir) {
                        // $output[]['id'] = basename($dir);
                        // $output[]['name'] = Tools::ucfirst(basename($dir));
                        $output[] = basename($dir);
                    }
                }
                if (!empty($output)) {
                    break;
                }
            }

            return $output;
        }

        public static function getCustomize($data)
        {
            $result = array();
            foreach ($data as $key => $type) {
                $uri_file = DeoHelper::getThemeDir().DeoHelper::getJsDir().'customize/'.$key.$type.'.json';
                // echo $uri_file;
                if (file_exists($uri_file)) {
                    $inputs = $default = array();
                    $settings = json_decode(Tools::file_get_contents($uri_file));
                    if ($settings) {
                        $name_group = '';
                        switch ($key) {
                            case 'content':
                                $name_group = 'General';
                                break;
                            case 'header':
                                $name_group = 'Header';
                                break;
                            case 'footer':
                                $name_group = 'Footer';
                                break;
                        }

                        foreach ($settings as $input) {
                            $inputs[] = array(
                                'required'   => isset($input->required) ? $input->required : false,
                                'name'       => $input->id,
                                'desc'       => isset($input->desc) ? $input->desc : '',
                                'type'       => isset($input->type) ? $input->type : '',
                                'label'      => isset($input->label) ? $input->label : '',
                                'value'      => isset($input->value) ? $input->value : '',
                                'responsive' => (isset($input->responsive) && $input->responsive) ? json_encode($input->responsive) : '',
                                'selector'   => isset($input->selector) ? $input->selector : '',
                                'default'    => isset($input->default) ? $input->default : '',
                                'special'    => isset($input->special) ? $input->special : '',
                                'media'      => isset($input->media) ? $input->media : '',
                            );
                        }

                        $result[] = array(
                            'title' => $name_group,
                            'key' => $key.$type,
                            'inputs' => $inputs,
                        );
                    }
                }
            }


            return $result;
        }
        


        /**
         * Execute modules for specified hook
         *
         * @param string $hook_name Hook Name
         * @param array $hook_args Parameters for the functions
         * @param int $id_module Execute hook for this module only
         * @return string modules output
         */
        public function exec($hook_name, $hook_args = array(), $id_module = null)
        {

            // Check arguments validity
            if (($id_module && !is_numeric($id_module)) || !Validate::isHookName($hook_name)) {
                throw new PrestaShopException('Invalid id_module or hook_name');
            }

            // If no modules associated to hook_name or recompatible hook name, we stop the function

            if (!$module_list = Hook::getHookModuleExecList($hook_name)) {
                return '';
            }

            // Check if hook exists
            if (!$id_hook = Hook::getIdByName($hook_name)) {
                return false;
            }

            // Store list of executed hooks on this page
            Hook::$executed_hooks[$id_hook] = $hook_name;

            $live_edit = false;
            $context = Context::getContext();
            if (!isset($hook_args['cookie']) || !$hook_args['cookie']) {
                $hook_args['cookie'] = $context->cookie;
            }
            if (!isset($hook_args['cart']) || !$hook_args['cart']) {
                $hook_args['cart'] = $context->cart;
            }

            $retro_hook_name = Hook::getRetroHookName($hook_name);

            // Look on modules list
            $altern = 0;
            $output = '';
            foreach ($module_list as $array) {

                // Check errors
                if ($id_module && $id_module != $array['id_module'])
                    continue;
                if (!($moduleInstance = Module::getInstanceByName($array['module'])))
                    continue;


                // Check permissions
                $exceptions = $moduleInstance->getExceptions($array['id_hook']);
                if (in_array(Dispatcher::getInstance()->getController(), $exceptions)) {
                    continue;
                }
                if (Validate::isLoadedObject($context->employee) && !$moduleInstance->getPermission('view', $context->employee)) {
                    continue;
                }

                // Check which / if method is callable

                $hook_callable = is_callable(array($moduleInstance, 'hook'.$hook_name));
                $orhook = "";
                if (array_key_exists($moduleInstance->id, $this->overrideHooks)) {
                    $orhook = ($this->overrideHooks[$moduleInstance->id]);
                    $hook_callable = is_callable(array($moduleInstance, 'hook'.$orhook));
                }

                if (($hook_callable)) {
                    $hook_args['altern'] = ++$altern;
                    if (array_key_exists($moduleInstance->id, $this->overrideHooks)) {
                        if ($hook_callable) {
                            $display = $moduleInstance->{'hook'.$orhook}($hook_args);
                        }
                    } else {
                        // Call hook method
                        if ($hook_callable) {
                            $display = $moduleInstance->{'hook'.$hook_name}($hook_args);
                        }
                    }
                    // Live edit
                    if (isset($array['live_edit']) && $array['live_edit'] && Tools::isSubmit('live_edit') && Tools::getValue('ad') && Tools::getValue('liveToken') == Tools::getAdminToken('AdminModulesPositions'.(int)Tab::getIdFromClassName('AdminModulesPositions').(int)Tools::getValue('id_employee'))) {
                        $live_edit = true;
                        $output .= self::wrapLiveEdit($display, $moduleInstance, $array['id_hook']);
                    } else {
                        $output .= $display;
                    }
                }
            }

            // Return html string
            return ($live_edit ? '<script type="text/javascript">hooks_list.push(\''.$hook_name.'\'); </script>
						<div id="'.$hook_name.'" class="dndHook" style="min-height:50px">' : '').$output.($live_edit ? '</div>' : '');
        }

        /**
         * wrap html Live Edit
         */
        public static function wrapLiveEdit($display, $moduleInstance, $id_hook)
        {
            return '';
        }

        /**
         * get array languages
         * @param : id_lang, name, active, iso_code, language_code, date_format_lite, date_format_full, is_rtl, id_shop, shops (array)
         * return array (
         * 		1 => en,
         * 		2 => vn,
         * )
         */
        public static function getLangAtt($attribute = 'iso_code')
        {
            $languages = array();
            foreach (Language::getLanguages(false, false, false) as $lang) {
                $languages[] = $lang[$attribute];
            }
            return $languages;
        }

        public static function getCookie()
        {
            $data = $_COOKIE;
            return $data;
        }

        /**
         * @param
         * 0 no multi_lang
         * 1 multi_lang follow id_lang
         * 2 multi_lnag follow code_lang
         * @return array
         */
        public static function getPost($keys = array(), $multi_lang = 0)
        {
            $post = array();
            if ($multi_lang == 0) {
                foreach ($keys as $key) {
                    // get value from $_POST
                    $post[$key] = Tools::getValue($key);
                }
            } elseif ($multi_lang == 1) {

                foreach ($keys as $key) {
                    // get value multi language from $_POST
                    if (method_exists('Language', 'getIDs')) {
                        foreach (Language::getIDs(false) as $id_lang)
                            $post[$key.'_'.(int)$id_lang] = Tools::getValue($key.'_'.(int)$id_lang);
                    }
                }
            } elseif ($multi_lang == 2) {
                $languages = self::getLangAtt();
                foreach ($keys as $key) {
                    // get value multi language from $_POST
                    foreach ($languages as $id_code)
                        $post[$key.'_'.$id_code] = Tools::getValue($key.'_'.$id_code);
                }
            }

            return $post;
        }

        public static function deoExitsDb($type='', $table_name='', $col_name='')
        {
            if ($type == 'table') {
                # EXITS TABLE
                $sql = 'SELECT COUNT(*) FROM information_schema.tables
                            WHERE table_schema = "'._DB_NAME_.'"
                            AND table_name = "'._DB_PREFIX_.pSQL($table_name).'"';
                $table = Db::getInstance()->getValue($sql);
                if (empty($table)) {
                    return false;
                }
                return true;
                
            } else if ($type == 'column') {
                # EXITS COLUMN
                $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.pSQL($table_name) .'` LIKE "'.pSQL($col_name).'"';
                $column = Db::getInstance()->executeS($sql);
                if (empty($column)) {
                    return false;
                }
                return true;
            }
            
            return false;
        }

        public static function DeoCreateColumn($table_name, $col_name, $data_type)
        {
            $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.pSQL($table_name) .'` LIKE "'.pSQL($col_name).'"';
            $column = Db::getInstance()->executeS($sql);

            if (empty($column)) {
                $sql = 'ALTER TABLE `'._DB_PREFIX_.pSQL($table_name).'` ADD COLUMN `'.pSQL($col_name).'` '.pSQL($data_type);
                $res = Db::getInstance()->execute($sql);
            }
        }
        
        public static function DeoEditColumn($table_name, $col_name, $data_type)
        {
            $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.pSQL($table_name) .'` LIKE "'.pSQL($col_name).'"';
            $column = Db::getInstance()->executeS($sql);

            if (!empty($column)) {
                $sql = 'ALTER TABLE `'._DB_PREFIX_.pSQL($table_name).'` MODIFY `'.pSQL($col_name).'` '.pSQL($data_type);
                $res = Db::getInstance()->execute($sql);
            }
        }

        public static function DeoRemoveColumn($table_name, $col_name)
        {
            $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.pSQL($table_name) .'` LIKE "'.pSQL($col_name).'"';
            $column = Db::getInstance()->executeS($sql);

            if (!empty($column)) {
                $sql = 'ALTER TABLE `'._DB_PREFIX_.pSQL($table_name).'` DROP `'.pSQL($col_name).'`';
                $res = Db::getInstance()->execute($sql);
            }
        }

        public static function DeoRenameColumn($table_name, $col_name, $col_new, $data_type)
        {
            $sql = 'SHOW FIELDS FROM `'._DB_PREFIX_.pSQL($table_name) .'` LIKE "'.pSQL($col_name).'"';
            $column = Db::getInstance()->executeS($sql);

            if (!empty($column)) {
                $sql = 'ALTER TABLE `'._DB_PREFIX_.pSQL($table_name).'` CHANGE  `'.pSQL($col_name).'` `'.pSQL($col_new).'` '.pSQL($data_type);
                $res = Db::getInstance()->execute($sql);
            }
        }
    }
}
