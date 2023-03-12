<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


class DeoMegamenuGroupModel extends ObjectModel
{
    public $title;
    public $title_fo;
    public $active;
    public $hook;
    public $position;
    public $id_shop;
    public $params;
    
     // check call via deotemplate
    public $active_ap;
    public $randkey;
    public $data = array();
    public $form_id;
    
    const GROUP_STATUS_DISABLE = '0';
    const GROUP_STATUS_ENABLE = '1';
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'deomegamenu_group',
        'primary' => 'id_deomegamenu_group',
        'multilang' => true,
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'tab_style' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            //'hook' => array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 64),
            'hook' => array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml', 'size' => 64),
            'position' => array('type' => self::TYPE_INT),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'params' => array('type' => self::TYPE_HTML, 'lang' => false),
            
            'active_ap' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'randkey' => array('type' => self::TYPE_STRING, 'lang' => false, 'size' => 255),
            'form_id' => array('type' => self::TYPE_STRING, 'lang' => false, 'size' => 255),
            # Lang fields
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'title_fo' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
        )
    );

    public function add($autodate = true, $null_values = false)
    {
        $res = parent::add($autodate, $null_values);

        return $res;
    }

    public static function groupExists($id_group, $id_shop = null)
    {
        $req = 'SELECT gr.`id_deomegamenu_group` as id_group
                FROM `'._DB_PREFIX_.'deomegamenu_group` gr
                WHERE gr.`id_deomegamenu_group` = '.(int)$id_group;
        if ($id_shop != null) {
            $req .= ' AND gr.`id_shop` = '.(int)$id_shop;
        }
        
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);
        return ($row);
    }

    public static function getGroups($active, $id_shop = null, $tab_style = null)
    {
        if (!isset($id_shop)) {
            $id_shop = DeoHelper::getIDShop();
        }
        $id_lang = Context::getContext()->language->id;
        $req = 'SELECT *
                FROM `'._DB_PREFIX_.'deomegamenu_group` gr
                LEFT JOIN '._DB_PREFIX_.'deomegamenu_group_lang grl ON gr.id_deomegamenu_group = grl.id_deomegamenu_group AND grl.id_lang = '.(int)$id_lang.'
                WHERE (`id_shop` = '.(int)$id_shop.')'.
                ($active ? ' AND gr.`active` = 1' : ' ').($tab_style ? ' AND gr.`tab_style` = 1' : ' ').' ORDER BY gr.position';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
    }


    public function delete()
    {
        $res = true;

        $sql = 'DELETE FROM `'._DB_PREFIX_.'deomegamenu_group` '
                .'WHERE `id_deomegamenu_group` = '.(int)$this->id;
        $res &= Db::getInstance()->execute($sql);
        $sql = 'DELETE FROM `'._DB_PREFIX_.'deomegamenu_group_lang` '
                .'WHERE `id_deomegamenu_group` = '.(int)$this->id;
        $res &= Db::getInstance()->execute($sql);
        $sql = 'SELECT bt.`id_deomegamenu` as id
                FROM `'._DB_PREFIX_.'deomegamenu` bt
                WHERE bt.`id_group` = '.(int)$this->id;
        $btmegamenu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($btmegamenu) {
            $where = '';
            foreach ($btmegamenu as $bt) {
                # module validation
                $where .= $where ? ','.(int)$bt['id'] : (int)$bt['id'];
            }
            $sql = 'DELETE FROM `'._DB_PREFIX_.'deomegamenu` '
                    .'WHERE `id_deomegamenu` IN ('.$where.')';
            Db::getInstance()->execute($sql);   # module validation
            $sql = 'DELETE FROM `'._DB_PREFIX_.'deomegamenu_lang` '
                    .'WHERE `id_deomegamenu` IN ('.$where.')';
            Db::getInstance()->execute($sql);   # module validation
            $sql = 'DELETE FROM `'._DB_PREFIX_.'deomegamenu_shop` '
                    .'WHERE `id_deomegamenu` IN ('.$where.')';
            Db::getInstance()->execute($sql);   # module validation
        }
        
        $res &= parent::delete();
        return $res;
    }

    /**
     * Get and validate StartWithSlide field.
     */
    public static function showStartWithSlide($start_with_slide = 0, $slider = array())
    {
        $result = 1;
        if (is_array($slider)) {
            $start_with_slide = (int)$start_with_slide;
            $slider_num = count($slider);
            // 1 <= $start_with_slide <= $slider_num
            if (1 <= $start_with_slide && $start_with_slide <= $slider_num) {
                $result = $start_with_slide;
            }
        }

        $result--; // index begin from 0
        return $result;
    }

    public function getDelay()
    {
        $temp_result = json_decode(LeoSlideshowSlide::base64Decode($this->params), true);
        $result = $temp_result['delay'];

        return $result;
    }

    /**
     * Get group to frontend
     */
    public static function getActiveGroupByHook($hook_name = '', $active = 1)
    {
        $id_shop = DeoHelper::getIDShop();
        $id_lang = Context::getContext()->language->id;
        $sql = '
                SELECT *
                FROM '._DB_PREFIX_.'deomegamenu_group gr
                LEFT JOIN '._DB_PREFIX_.'deomegamenu_group_lang grl ON gr.id_deomegamenu_group = grl.id_deomegamenu_group AND grl.id_lang = '.(int)$id_lang.'
                WHERE gr.id_shop = '.(int)$id_shop.'
                AND gr.hook = "'.pSQL($hook_name).'"'.
                ($active ? ' AND gr.`active` = 1' : ' ').'
                ORDER BY gr.position';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Get group to preview
     */
    public static function getGroupByID($id_group)
    {
        $sql = '
            SELECT *
            FROM '._DB_PREFIX_.'deomegamenu_group gr
            WHERE gr.id_deomegamenu_group = '.(int)$id_group;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }
    
    public function count()
    {
        $sql = 'SELECT id_deomegamenu_group FROM '._DB_PREFIX_.'deomegamenu_group';
        $groups = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $number_groups = count($groups);
        return $number_groups;
    }
    
    // get last position of group
    public static function getLastPosition($id_shop)
    {
        return (Db::getInstance()->getValue('SELECT MAX(position)+1 FROM `'._DB_PREFIX_.'deomegamenu_group` WHERE `id_shop` = '.(int)$id_shop));
    }
    
    // get all menu of group
    public static function getMenuByGroup($id_group)
    {
        $sql = 'SELECT `id_deomegamenu`,`id_parent` FROM `'._DB_PREFIX_.'deomegamenu`
                WHERE `id_group` = '.(int)$id_group.'
                ORDER BY `id_parent` ASC';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
    
    // get all menu parent of group
    public static function getMenuParentByGroup($id_group)
    {
        $sql = 'SELECT `id_deomegamenu`,`id_parent` FROM `'._DB_PREFIX_.'deomegamenu`
                WHERE `id_group` = '.(int)$id_group.' AND `id_parent` = 0';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
    
    // set data for group when import
    public static function setDataForGroup($group, $content, $override)
    {
        $languages = Language::getLanguages();
            
        $lang_list = array();
        foreach ($languages as $lang) {
            # module validation
            $lang_list[$lang['iso_code']] = $lang['id_lang'];
        }
        if (is_array($content['title'])) {
            foreach ($content['title'] as $key => $title_item) {
                if (isset($lang_list[$key])) {
                    $group->title[$lang_list[$key]] = $title_item;
                }
            }
        } else {
            foreach ($languages as $lang) {
                $group->title[$lang['id_lang']] = $content['title'];
            }
        }
        if (is_array($content['title_fo'])) {
            foreach ($content['title_fo'] as $key => $title_item) {
                if (isset($lang_list[$key])) {
                    $group->title_fo[$lang_list[$key]] = $title_item;
                }
            }
        } else {
            $group_title_fo = '';
            foreach ($languages as $lang) {
                if ($lang['iso_code'] == 'en') {
                    $group_title_fo = 'Categories';
                }
                if ($lang['iso_code'] == 'es') {
                    $group_title_fo = 'Categorías';
                }
                if ($lang['iso_code'] == 'fr') {
                    $group_title_fo = 'Catégories';
                }
                if ($lang['iso_code'] == 'de') {
                    $group_title_fo = 'Kategorien';
                }
                if ($lang['iso_code'] == 'it') {
                    $group_title_fo = 'Categorie';
                }
                if ($lang['iso_code'] == 'ar') {
                    $group_title_fo = 'ال�?ئات';
                }
                $group->title_fo[$lang['id_lang']] = $group_title_fo;
            }
        }
        
        // $group->title = $content['title'];
        $group->id_shop = DeoHelper::getIDShop();
        $group->hook = $content['hook'];
        if (!$override) {
            $group->position = self::getLastPosition(Context::getContext()->shop->id);
            include_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperMegamenu.php');
            $group->randkey = DeoMegamenuHelper::genKey();
        }
        $group->tab_style = $content['tab_style'];
        $group->active = $content['active'];
        $group->params = $content['params'];
        // $group->params_widget = $content['params_widget'];
        $group->active_ap = $content['active_ap'];
        return $group;
    }
    
    public static function autoCreateKey()
    {
        $sql = 'SELECT '.self::$definition['primary'].' FROM '._DB_PREFIX_.bqSQL(self::$definition['table']).
                ' WHERE randkey IS NULL OR randkey = ""';
        
        $rows = Db::getInstance()->executes($sql);
        foreach ($rows as $row) {
            $mod_group = new DeoMegamenuGroupModel((int)$row[self::$definition['primary']]);
            include_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperMegamenu.php');
            $mod_group->randkey = DeoMegamenuHelper::genKey();
            $mod_group->update();
        }
    }
}
