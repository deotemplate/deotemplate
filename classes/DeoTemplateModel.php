<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoShortCodeBase.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoSetting.php');

class DeoTemplateModel extends ObjectModel
{
    public $hook_name;
    public $params;
    public $id_deotemplate_positions;
    public $id_deotemplate_shortcode;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'deotemplate',
        'primary' => 'id_deotemplate',
        'multilang' => true,
        'multishop' => true,
        'fields' => array(
            'hook_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'id_deotemplate_positions' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'params' => array('type' => self::TYPE_HTML, 'lang' => true),
            'id_deotemplate_shortcode' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
        # validate module
        unset($context);
        parent::__construct($id, $id_lang, $id_shop);
    }
    
    // get id by id shortcode
    public static function getIdByIdShortCode($id_shortcode)
    {
        if (!$id_shortcode) {
            return false;
        }
        $sql = 'SELECT id_deotemplate FROM '._DB_PREFIX_.'deotemplate WHERE id_deotemplate_shortcode = ' . (int)$id_shortcode;
        $result = Db::getInstance()->getRow($sql);
        
        if (!$result) {
            return false;
        }
        
        return $result['id_deotemplate'];
    }

    public function getIdbyHookName($hook_name, $position_id)
    {
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;
        $sql = 'SELECT p.id_deotemplate
                FROM '._DB_PREFIX_.'deotemplate p
                    LEFT JOIN '._DB_PREFIX_.'deotemplate_shop ps ON (ps.id_deotemplate = p.id_deotemplate)
                    WHERE ps.id_shop = '.(int)$id_shop.'
                        AND hook_name=\''.pSql($hook_name).'\'
                        AND p.id_deotemplate_positions='.(int)$position_id.' ORDER BY p.id_deotemplate';
        $result = Db::getInstance()->executeS($sql);
        if (!$result) {
            return false;
        }

        foreach ($result as $value) {
            $sql = 'DELETE FROM '._DB_PREFIX_.'deotemplate_shop
                    WHERE id_deotemplate IN(SELECT id_deotemplate
                        FROM '._DB_PREFIX_.'deotemplate
                        WHERE hook_name=\''.pSql($hook_name).'\'
                        AND id_deotemplate_positions='.(int)$position_id.'
                        AND id_deotemplate != '.(int)$value['id_deotemplate'].')';
            Db::getInstance()->execute($sql);

            $sql = 'DELETE FROM '._DB_PREFIX_.'deotemplate
                    WHERE hook_name=\''.pSql($hook_name).'\'
                        AND id_deotemplate_positions='.(int)$position_id.'
                        AND id_deotemplate != '.(int)$value['id_deotemplate'];
            Db::getInstance()->execute($sql);

            return $value['id_deotemplate'];
        }
    }

    public function getIdbyHookNameAndProfile($hook_name, $profile, $id_lang)
    {
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;

        //$id_lang = (int)$id_lang;
        if (!$profile->mobile && !$profile->header && !$profile->content && !$profile->footer && !$profile->product) {
            return array();
        }

        $arr = array($profile->mobile, $profile->header, $profile->content, $profile->footer, $profile->product);

        $sql = 'SELECT p.id_deotemplate, pl.params
                FROM '._DB_PREFIX_.'deotemplate p
                    LEFT JOIN '._DB_PREFIX_.'deotemplate_shop ps ON (ps.id_deotemplate = p.id_deotemplate AND id_shop='.(int)$id_shop.')
                    LEFT JOIN '._DB_PREFIX_.'deotemplate_lang pl ON (p.id_deotemplate = pl.id_deotemplate AND pl.id_lang='.(int)$id_lang.')
                WHERE p.`hook_name`=\''.$hook_name.'\'
                    AND ps.id_shop='.(int)$id_shop.'
                    AND pl.id_lang='.(int)$id_lang.'
                    AND p.id_deotemplate_positions IN ('. pSQL(implode(',', array_map('intval', $arr))).')
                    ORDER BY p.id_deotemplate';
        return Db::getInstance()->getRow($sql);
    }

    /**
     * getListPositisionByType
     * @param type $type = {all, mobile, header, content, footer, product}
     * @return type
     */
    public function getListPositisionByType($type = 'all', $id_shop = null)
    {
        $str = Tools::strtolower($type);
        $sql = 'SELECT p.* FROM `'._DB_PREFIX_.'deotemplate_positions` p'
                .' INNER JOIN `'._DB_PREFIX_.'deotemplate_positions_shop` ps ON (p.id_deotemplate_positions = ps.id_deotemplate_positions)';
        if ($type != 'all') {
            $sql .= ' WHERE p.position=\''.pSQL($str).'\' AND ps.id_shop='.(int)$id_shop;
        }

        return Db::getInstance()->executeS($sql, 1);
    }

    public function add($autodate = true, $null_values = false)
    {
        $id_shop = DeoHelper::getIDShop();
        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'deotemplate_shop` (`id_shop`, `id_deotemplate`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')');
        return $res;
    }

    public function save($null_values = false, $autodate = true)
    {
        # validate module
        unset($null_values);
        unset($autodate);
        $context = Context::getContext();
        $this->id_shop = $context->shop->id;
        return parent::save();
    }

    public function parseData($hook_name, $data, $profile_param)
    {
        DeoShortCodesBuilder::$is_front_office = 1;
        DeoShortCodesBuilder::$is_gen_html = 1;
        DeoShortCodesBuilder::$profile_param = $profile_param;
        $shortcode_builder = new DeoShortCodesBuilder();
        DeoShortCodesBuilder::$hook_name = $hook_name;
        $result = $shortcode_builder->parse($data);
  
        return $result;
    }

    public function parseJsonToHtml($data)
    {
        DeoShortCodesBuilder::$is_front_office = 1;
        DeoShortCodesBuilder::$is_gen_html = 1;
        $shortcode_builder = new DeoShortCodesBuilder();
        DeoShortCodesBuilder::$hook_name = $hook_name;

        $result = $shortcode_builder->parseJsonToHtml($data);
  
        return $result;
    }

    /**
     * Get all item by position include information: hooks postion and data
     * @param type $pos
     * @param type $id_position
     * @param type $id_profile
     * @param type $is_font
     * @param type $id_lang
     * @return type
     */
    public function getAllItemsByPosition($pos, $id_position, $id_profile = 0, $is_font = 0, $id_lang = 0)
    {
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;
        $id_position = (int)$id_position;
        $id_profile = (int)$id_profile;
        $where = ' WHERE ps.id_shop='.(int)$id_shop.' AND pp.id_deotemplate_positions='.(int)$id_position;
        if ($id_profile) {
            $where .= ' AND ppr.id_deotemplate_profiles='.(int)$id_profile;
        }
        if ($id_lang) {
            $where .= ' AND pl.id_lang = '.(int)$id_lang;
        } else {
            // $id_lang = $context->language->id;  // default in admin account
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');      // default at frontend
        }
        $sql = 'SELECT p.*, pl.params, pl.id_lang
                FROM `'._DB_PREFIX_.'deotemplate` p
                    LEFT JOIN `'._DB_PREFIX_.'deotemplate_shop` ps ON (ps.id_deotemplate = p.id_deotemplate)
                    LEFT JOIN `'._DB_PREFIX_.'deotemplate_lang` pl ON (pl.id_deotemplate = p.id_deotemplate)
                    LEFT JOIN `'._DB_PREFIX_.'deotemplate_positions` pp ON (p.id_deotemplate_positions=pp.id_deotemplate_positions)
                    LEFT JOIN `'._DB_PREFIX_.'deotemplate_profiles` ppr ON (ppr.`'.bqSQL($pos).'`=pp.id_deotemplate_positions)
                '.pSql($where).' ORDER BY p.id_deotemplate';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        
        # FIX - Only get language data valid
        // $id_langs = Language::getLanguages(true, false, true);
        // foreach ($result as $key => $val) {
        //    if (isset($val['id_lang']) && !in_array($val['id_lang'], $id_langs)) {
        //        unset($result[$key]);
        //    }
        // }
        
        $data_lang = array();
        if ($is_font) {
            foreach ($result as $row) {
                $data_lang[$row['hook_name']] = $row['params'];
            }
            return $data_lang;
        }
        $deo_helper = new DeoShortCodesBuilder();
        DeoShortCodesBuilder::$is_front_office = $is_font;
        DeoShortCodesBuilder::$is_gen_html = 1;
        foreach ($result as $row) {
            if (isset($data_lang[$row['id_deotemplate']])) {
                $data_lang[$row['id_deotemplate']]['params'][$row['id_lang']] = $row['params'];
            } else {
                $data_lang[$row['id_deotemplate']] = array(
                    'id' => $row['id_deotemplate'],
                    'hook_name' => $row['hook_name'],
                );
                $data_lang[$row['id_deotemplate']]['params'][$row['id_lang']] = $row['params'];
            }
        }
        
        $hook_config = DeoHelper::getConfig('LIST_' . Tools::strtoupper($pos).'_HOOK');
        $hook_config = ($hook_config != '' && $hook_config) ? explode(',', $hook_config) : DeoSetting::getHook($pos);
 
        $data_hook = array_flip($hook_config);
        foreach ($data_lang as $row) {
            //process params
            foreach ($row['params'] as $key => $value) {
                DeoShortCodesBuilder::$lang_id = $key;
                if ($key == $id_lang) {
                    DeoShortCodesBuilder::$is_gen_html = 1;
                    $row['content'] = $deo_helper->parse($value);
                } else {
                    DeoShortCodesBuilder::$is_gen_html = 0;
                    $deo_helper->parse($value);
                }
            }
            $data_hook[$row['hook_name']] = $row;
        }
        return array('content' => $data_hook, 'dataForm' => DeoShortCodesBuilder::$data_form);
    }

    /**
     * Get all items - datas of all hooks by shop Id, lang Id for front-end or back-end
     * @param type $list_pos_id array
     */
    public function getAllItemsByPositionId($list_pos_id)
    {
        if ($list_pos_id) {
            $sql = 'SELECT DISTINCT(id_deotemplate) as id FROM `'._DB_PREFIX_.'deotemplate` p
                    WHERE id_deotemplate_positions IN('. pSQL(implode(',', array_map('intval', $list_pos_id))).')';
            return Db::getInstance()->executes($sql);
        }
        return array();
    }

    /**
     * Get all items - datas of all hooks by shop Id, lang Id for front-end or back-end
     * @param type $id_profiles
     * @param type $is_font (=0: for back-end; =1: for front-end)
     * @param type $id_lang
     * @return type
     */
    public function getAllItems($profile, $is_font = 0, $id_lang = 0, $data_template = array())
    {
        //print_r("Input: $id_profiles - $is_font - $id_lang"); 2-1-1
        $context = Context::getContext();
        // $id_profiles = (int)$profile['id_deotemplate_profiles'];
        $id_shop = (int)$context->shop->id;
        $id_lang = $id_lang ? (int)$id_lang : (int)$context->language->id;
        if (!$profile['mobile'] && !$profile['header'] && !$profile['content'] && !$profile['footer'] && !$profile['product']) {
            return array();
        }


        $data_template = array();
        if (count($data_template)){
            $data_template = array_merge($data_template[$profile['mobile']], $data_template[$profile['header']], $data_template[$profile['content']], $data_template[$profile['footer']], $data_template[$profile['product']]);
        }else{
            // $positions = DeoSetting::getPositionsName();
            // // echo '<pre>';
            // foreach ($positions as $position) {
            //     $hooks = DeoSetting::getHook($position);
            //     foreach ($hooks as $hook) {
            //         $name_hook_params = DeoHelper::getConfigName($hook.'_'.$profile[$position].'_'.Language::getIsoById($id_lang));
            //         $data_template[] = json_decode(Tools::htmlentitiesDecodeUTF8(DeoHelper::get($name_hook_params)), true);
            //         // print_r($name_hook_params);
            //     }
            // }
            // // print_r($data_template);
            // // echo "</pre>";
            // // die();


            
            // echo '<pre>';
            $arr = array($profile['mobile'], $profile['header'], $profile['content'], $profile['footer'], $profile['product']);
            $sql = 'SELECT p.*, pl.params, pl.id_lang
                    FROM '._DB_PREFIX_.'deotemplate p
                        LEFT JOIN '._DB_PREFIX_.'deotemplate_shop ps ON (ps.id_deotemplate = p.id_deotemplate AND id_shop='.(int)$id_shop.')
                        LEFT JOIN '._DB_PREFIX_.'deotemplate_lang pl ON (pl.id_deotemplate = p.id_deotemplate) 
                        WHERE pl.id_lang='.(int)$id_lang.' 
                        AND ps.id_shop='.(int)$id_shop.' 
                        AND p.id_deotemplate_positions IN ('. pSQL(implode(',', array_map('intval', $arr))).')';

            $data_template = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            // print_r($data_template);
            // echo "</pre>";
            // die();
        }

        $data_lang = array();
        if ($is_font) {
            foreach ($data_template as $row) {
                $data_lang[$row['hook_name']] = $row['params'];
            }
            return $data_lang;
        }
        $deo_helper = new DeoShortCodesBuilder();
        DeoShortCodesBuilder::$is_front_office = $is_font;
        DeoShortCodesBuilder::$is_gen_html = 1;
        foreach ($data_template as $row) {
            if (isset($data_lang[$row['id_deotemplate']])) {
                $data_lang[$row['id_deotemplate']]['params'][$row['id_lang']] = $row['params'];
            } else {
                $data_lang[$row['id_deotemplate']] = array(
                    'id' => $row['id_deotemplate'],
                    'hook_name' => $row['hook_name'],
                );
                $data_lang[$row['id_deotemplate']]['params'][$row['id_lang']] = $row['params'];
            }
        }
        $data_hook = array_flip(DeoSetting::getHookHome());
        foreach ($data_lang as $row) {
            //process params
            foreach ($row['params'] as $key => $value) {
                DeoShortCodesBuilder::$lang_id = $key;
                if ($key == $id_lang) {
                    DeoShortCodesBuilder::$is_gen_html = 1;
                    $row['content'] = $deo_helper->parse($value);
                } else {
                    DeoShortCodesBuilder::$is_gen_html = 0;
                    $deo_helper->parse($value);
                }
            }
            $data_hook[$row['hook_name']] = $row;
        }

        return array('content' => $data_hook, 'dataForm' => DeoShortCodesBuilder::$data_form);
    }

    public function getAllStoreByShop()
    {
        return Store::getStores((int)Context::getContext()->language->id);
//        $context = Context::getContext();
//        $id_shop = (int)$context->shop->id;
//        $id_lang = (int)$context->language->id;
//        //$where = ' WHERE id_shop="'.$id_shop.'"';
//        $sql = '
//            SELECT  a.*, cl.name country, st.name state
//            FROM '._DB_PREFIX_.'store a
//                LEFT JOIN '._DB_PREFIX_.'country_lang cl
//                ON (cl.id_country = a.id_country
//                AND cl.id_lang = '.(int)$id_lang.')
//                LEFT JOIN '._DB_PREFIX_.'state st
//                ON (st.id_state = a.id_state)
//            WHERE a.id_store IN (
//                SELECT sa.id_store
//                FROM '._DB_PREFIX_.'store_shop sa
//                WHERE sa.id_shop = '.(int)$id_shop.'
//            )';
//        return Db::getInstance()->executes($sql);
    }

    public function findOtherProfileUsePosition($id_position, $id_profile)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'deotemplate_profiles dp
                WHERE (dp.`mobile`='.(int)$id_position.' OR dp.`header`='.(int)$id_position.' OR dp.`content`='.(int)$id_position.'
                    OR dp.`footer`='.(int)$id_position.' OR dp.`product`='.(int)$id_position.')
                    AND dp.`id_deotemplate_profiles`<>'.(int)$id_profile;
        return Db::getInstance()->executes($sql);
    }

    public function updateDeotemplateLang($id, $id_lang, $params)
    {
        //can not use psql, because pramram is import function
        $data = array('params' => $params);
        Db::getInstance()->update('deotemplate_lang', $data, 'id_deotemplate='.(int)$id.' AND id_lang='.(int)$id_lang);
    }
    
    public function delete()
    {
        $result = parent::delete();
        
        if ($result) {
            if (isset($this->def['multishop']) && $this->def['multishop'] == true) {
                # DELETE RECORD FORM TABLE _SHOP
                $id_shop_list = Shop::getContextListShopID();
                if (count($this->id_shop_list)) {
                    $id_shop_list = $this->id_shop_list;
                }

                $id_shop_list = array_map('intval', $id_shop_list);

                Db::getInstance()->delete($this->def['table'].'_shop', '`'.$this->def['primary'].'`='.
                    (int)$this->id.' AND id_shop IN ('. pSQL(implode(', ', $id_shop_list)).')');
            }
        }
        
        return $result;
    }
}
