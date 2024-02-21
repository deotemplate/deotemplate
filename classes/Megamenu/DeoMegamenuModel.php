<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }

include_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperMegamenu.php');

class DeoMegamenuModel extends ObjectModel
{
    public $id;
    public $id_deomegamenu;
    public $id_group;
    public $image;
    public $icon_class;
    public $id_parent = 0;
    public $is_group = 0;
    public $width;
    public $submenu_width;
    public $submenu_colum_width;
    public $item;
    public $item_parameter;
    public $colums = 1;
    public $type;
    public $is_content = 0;
    public $show_title = 1;
    public $level_depth;
    public $active = 1;
    public $position;
    public $show_sub;
    public $url;
    public $target;
    public $privacy;
    public $position_type;
    public $menu_class;
    public $content;
    public $submenu_content;
    public $level;
    public $left;
    public $right;
    public $date_add;
    public $date_upd;
    # Lang
    public $title;
    public $text;
    public $description;
    public $content_text;
    public $submenu_catids;
    public $is_cattree = 1;
    private $shop_url;
    private $edit_string = '';
    private $mega_config = array();
    private $edit_string_col = '';
    private $is_live_edit = true;
    private $deo_module = null;
    public $id_shop = '';
    public $groupBox = 'all';       // Default for import datasameple
    public $sub_with;
    public $params_widget;

    public function setModule($module)
    {
        $this->deo_module = $module;
    }
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'deomegamenu',
        'primary' => 'id_deomegamenu',
        'multilang' => true,
        'fields' => array(
            'id_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'image' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName'),
            'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'is_group' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'width' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 255),
            'submenu_width' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 255),
            'submenu_colum_width' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'item' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 255),
            'item_parameter' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'colums' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 255),
            'type' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 255),
            'is_content' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'show_title' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_cattree' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'level_depth' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'position' => array('type' => self::TYPE_INT),
            'show_sub' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'url' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isUrl', 'size' => 255),
            'target' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 25),
            'privacy' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 6),
            'position_type' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 25),
            'menu_class' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 255),
            'icon_class' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 125),
            'content' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'submenu_content' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'level' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'left' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'right' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'sub_with' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 255),
            'groupBox' => array('type' => self::TYPE_STRING, 'size' => 255),
            'params_widget' => array('type' => self::TYPE_HTML, 'validate' => 'isString'),
            # Lang fields
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'text' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => false, 'size' => 255),
            'description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
            'content_text' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'),
            'submenu_catids' => array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isString'),
            
        ),
    );

    public function copyFromPost($post = array())
    {
        /* Classical fields */
        foreach ($post as $key => $value) {
            if (key_exists($key, $this) && $key != 'id_'.$this->table) {
                $this->{$key} = $value;
            }
        }
        /* Multilingual fields */
        if (count($this->fieldsValidateLang)) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                foreach ($this->fieldsValidateLang as $field => $validation) {
                    if (Tools::getIsset($field.'_'.(int)$language['id_lang'])) {
                        $this->{$field}[(int)$language['id_lang']] = Tools::getValue($field.'_'.(int)$language['id_lang']);
                    }

                    # validate module
                    unset($validation);
                }
            }
        }
    }

    public function add($autodate = true, $null_values = false)
    {
        $this->level_depth = $this->calcLevelDepth();
        $id_shop = DeoHelper::getIDShop();
        $res = parent::add($autodate, $null_values);
        $sql = 'INSERT INTO `'._DB_PREFIX_.'deomegamenu_shop` (`id_shop`, `id_deomegamenu`)
            VALUES('.(int)$id_shop.', '.(int)$this->id.')';
        $res &= Db::getInstance()->execute($sql);
        return $res;
    }

    public function update($null_values = false)
    {
        $this->level_depth = $this->calcLevelDepth();
        return parent::update($null_values);
    }

    protected function recursiveDelete(&$to_delete, $id_deomegamenu)
    {
        if (!is_array($to_delete) || !$id_deomegamenu) {
            die(Tools::displayError());
        }

        $result = Db::getInstance()->executeS('
        SELECT `id_deomegamenu`
        FROM `'._DB_PREFIX_.'deomegamenu`
        WHERE `id_parent` = '.(int)$id_deomegamenu);
        foreach ($result as $row) {
            $to_delete[] = (int)$row['id_deomegamenu'];
            $this->recursiveDelete($to_delete, (int)$row['id_deomegamenu']);
        }
    }

    public function delete()
    {
        $this->clearCache();

        // Get children categories
        $to_delete = array((int)$this->id);
        $this->recursiveDelete($to_delete, (int)$this->id);
        $to_delete = array_unique($to_delete);

        // Delete CMS Category and its child from database
        $list = count($to_delete) > 1 ? implode(',', array_map('intval', $to_delete)) : (int)$this->id;
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'deomegamenu` WHERE `id_deomegamenu` IN ('.pSQL($list).')');
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'deomegamenu_shop` WHERE `id_deomegamenu` IN ('.pSQL($list).')');
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'deomegamenu_lang` WHERE `id_deomegamenu` IN ('.pSQL($list).')');
        DeoMegamenuModel::cleanPositions($this->id_parent);
        return true;
    }

    public function deleteSelection($menus)
    {
        $return = 1;
        foreach ($menus as $id_deomegamenu) {
            $obj_menu = new DeoMegamenuModel($id_deomegamenu);
            $return &= $obj_menu->delete();
        }
        return $return;
    }

    public function calcLevelDepth()
    {
        $parent_btmegamenu = new DeoMegamenuModel($this->id_parent);
        if (!$parent_btmegamenu) {
            die('parent Menu does not exist');
        }
        return $parent_btmegamenu->level_depth + 1;
    }

    public function updatePosition($way, $position)
    {
        $sql = '
            SELECT cp.`id_deomegamenu`, cp.`position`, cp.`id_parent`
            FROM `'._DB_PREFIX_.'deomegamenu` cp
            WHERE cp.`id_parent` = '.(int)$this->id_parent.'
            ORDER BY cp.`position` ASC';
        if (!$res = Db::getInstance()->executeS($sql)) {
            return false;
        }
        foreach ($res as $menu) {
            if ((int)$menu['id_deomegamenu'] == (int)$this->id) {
                $moved_menu = $menu;
            }
        }
        if (!isset($moved_menu) || !isset($position)) {
            return false;
        }
        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return (Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'deomegamenu`
            SET `position`= `position` '.pSQL($way ? '- 1' : '+ 1').'
            WHERE `position`
            '.($way ? '> '.(int)$moved_menu['position'].' AND `position` <= '.(int)$position : '< '.(int)$moved_menu['position'].' AND `position` >= '.(int)$position).'
            AND `id_parent`='.(int)$moved_menu['id_parent']) && Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'deomegamenu`
            SET `position` = '.(int)$position.'
            WHERE `id_parent` = '.(int)$moved_menu['id_parent'].'
            AND `id_deomegamenu`='.(int)$moved_menu['id_deomegamenu']));
    }

    public static function cleanPositions($id_parent)
    {
        $result = Db::getInstance()->executeS('
        SELECT `id_deomegamenu`
        FROM `'._DB_PREFIX_.'deomegamenu`
        WHERE `id_parent` = '.(int)$id_parent.'
        ORDER BY `position`');
        $sizeof = count($result);
        for ($i = 0; $i < $sizeof; ++$i) {
            $sql = '
            UPDATE `'._DB_PREFIX_.'deomegamenu`
            SET `position` = '.(int)$i.'
            WHERE `id_parent` = '.(int)$id_parent.'
            AND `id_deomegamenu` = '.(int)$result[$i]['id_deomegamenu'];
            Db::getInstance()->execute($sql);
        }
        return true;
    }

    public static function getLastPosition($id_parent)
    {
        return (Db::getInstance()->getValue('SELECT MAX(position)+1 FROM `'._DB_PREFIX_.'deomegamenu` WHERE `id_parent` = '.(int)$id_parent));
    }

    public function getInfo($id_deomegamenu, $id_lang = null, $id_shop = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }
        $sql = 'SELECT m.*, md.title, md.description, md.content_text , md.url , md.text
                FROM '._DB_PREFIX_.'deomegamenu m
                LEFT JOIN '._DB_PREFIX_.'deomegamenu_lang md ON m.id_deomegamenu = md.id_deomegamenu AND md.id_lang = '.(int)$id_lang
                .' JOIN '._DB_PREFIX_.'deomegamenu_shop bs ON m.id_deomegamenu = bs.id_deomegamenu AND bs.id_shop = '.(int)$id_shop;
        $sql .= ' WHERE m.id_deomegamenu='.(int)$id_deomegamenu;

        return Db::getInstance()->getRow($sql);
    }

    public function getChild($id_deomegamenu = null, $id_group = null, $id_lang = null, $id_shop = null, $active = false)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = ' SELECT m.*, md.title, md.text, md.description, md.content_text, md.url
                FROM '._DB_PREFIX_.'deomegamenu m
                LEFT JOIN '._DB_PREFIX_.'deomegamenu_lang md ON m.id_deomegamenu = md.id_deomegamenu AND md.id_lang = '.(int)$id_lang
                .' JOIN '._DB_PREFIX_.'deomegamenu_shop bs ON m.id_deomegamenu = bs.id_deomegamenu AND bs.id_shop = '.(int)$id_shop;
        if ($id_group != null) {
            $sql .= ' WHERE id_group='.(int)$id_group;
        }
        
        if ($active) {
            $sql .= ' AND m.`active`=1 ';
        }

        if ($id_deomegamenu != null) {
            # validate module
            $sql .= ' AND id_parent='.(int)$id_deomegamenu;
        }
        
        // if ($id_group != null) {
            // if ($id_deomegamenu != null)
                // $sql .= 'AND id_group='.(int)$id_group;
            // else
                // $sql .= ' WHERE id_group='.(int)$id_group;
        // }
        
        $sql .= ' ORDER BY `position` ';
        return Db::getInstance()->executeS($sql);
    }

    public function hasChild($id)
    {
        return isset($this->children[$id]);
    }

    public function getNodes($id)
    {
        return $this->children[$id];
    }

    public function getTree($id = null, $id_group = null)
    {
        $childs = $this->getChild($id, $id_group);

        foreach ($childs as $child) {
            # validate module
            $this->children[$child['id_parent']][] = $child;
        }
        $parent = 0;
        $output = $this->genTree($parent, 1);
        return $output;
    }

    public function getDropdown($id = null, $selected = 0, $id_group = null)
    {
        $this->children = array();
        $childs = $this->getChild($id, $id_group);
        foreach ($childs as $child) {
            # validate module871
            $this->children[$child['id_parent']][] = $child;
        }
        $output = array(array('id' => '0', 'title' => 'Root', 'selected' => ''));
        $output = $this->genOption(0, 1, $selected, $output);

        return $output;
    }

    public function genOption($parent, $level = 0, $selected = null, $output = array())
    {
        if ($this->hasChild($parent)) {
            $data = $this->getNodes($parent);
            foreach ($data as $menu) {
                $selected == $menu['id_deomegamenu'] ? 'selected="selected"' : '';
                $output[] = array('id' => $menu['id_deomegamenu'], 'title' => str_repeat('-', $level).' '.$menu['title'].' (ID:'.$menu['id_deomegamenu'].')', 'selected' => $selected);
                if ($menu['id_deomegamenu'] != $parent) {
                    $output = $this->genOption($menu['id_deomegamenu'], $level + 1, $selected, $output);
                }
            }
        }
        return $output;
    }

    public function genTree($parent, $level)
    {
        if ($this->hasChild($parent)) {
            $data = $this->getNodes($parent);
            $t = $level == 1 ? ' sortable' : '';
            $output = '<ol class="level'.$level.$t.' ">';

            foreach ($data as $menu) {
                $cls = Tools::getValue('id_deomegamenu') == $menu['id_deomegamenu'] ? 'selected' : '';
                // $output .= '<li data-menu-type="'.$menu['type'].'" id="list_'.$menu['id_deomegamenu'].'" data-id-menu="'.$menu['id_deomegamenu'].'" class="nav-item '.$cls.'">
                // <div><span class="disclose"><span></span></span>'.($menu['title'] ? $menu['title'] : '').' (ID:'.$menu['id_deomegamenu'].') <input type="checkbox" name="menubox[]" value="'.$menu['id_deomegamenu'].'" class="quickselect" title="Select to delete"><span title="'. $this->deo_module->l('Edit').'" class="quickedit" rel="id_'.$menu['id_deomegamenu'].'">E</span><span title="'. $this->deo_module->l('Delete').'" class="quickdel" rel="id_'.$menu['id_deomegamenu'].'">D</span><span title="'. $this->deo_module->l('Duplicate').'" class="quickduplicate" rel="id_'.$menu['id_deomegamenu'].'">DUP</span></div>';
                $output .= '<li id="list_'.$menu['id_deomegamenu'].'" data-id-menu="'.$menu['id_deomegamenu'].'" class="nav-item '.$cls.'">
                <div><span class="disclose"><span></span></span>'.($menu['title'] ? $menu['title'] : '').' (ID:'.$menu['id_deomegamenu'].') <input type="checkbox" name="menubox[]" value="'.$menu['id_deomegamenu'].'" class="quickselect" title="Select to delete"><span title="'. $this->deo_module->l('Edit').'" class="quickedit" rel="id_'.$menu['id_deomegamenu'].'">E</span><span title="'. $this->deo_module->l('Delete').'" class="quickdel" rel="id_'.$menu['id_deomegamenu'].'">D</span><span title="'. $this->deo_module->l('Duplicate').'" class="quickduplicate" rel="id_'.$menu['id_deomegamenu'].'">DUP</span></div>';
                if ($menu['id_deomegamenu'] != $parent) {
                    $output .= $this->genTree($menu['id_deomegamenu'], $level + 1);
                }
                $output .= '</li>';
            }

            $output .= '</ol>';
            return $output;
        }
        return '';
    }

    /**
     *
     */
    public function renderAttrs($menu)
    {
        // print_r($menu);
        $t = sprintf($this->edit_string, $menu['id_deomegamenu']);
        if ($this->is_live_edit) {
            if (isset($menu['megaconfig']->subwidth) && $menu['megaconfig']->subwidth) {
                # validate module
                $t .= ' data-subwidth="'.$menu['megaconfig']->subwidth.'" ';
            }
            if (isset($menu['megaconfig']->align) && $menu['megaconfig']->align) {
                # validate module
                $t .= ' data-align="'.$menu['megaconfig']->align.'" ';
            }
            if ($menu['sub_with'] != 'widget') {
                $hasChild = $this->hasChild($menu['id_deomegamenu']);
            } else {
                $hasChild = '';
            }
            $t .= ' id="menu-'.$menu['id_deomegamenu'].'"';
            $t .= ' data-menu-type="'.$menu['type'].'"';
            $t .= ' data-subwith="'.$menu['sub_with'].'"';
            $t .= ' data-id_parent="'.$menu['id_parent'].'"'; 
            $t .= ' data-menu_class="'.$menu['menu_class'].'"'; 
            $t .= ' data-active="'.$menu['active'].'"'; 
            $t .= ' data-icon-image="'.$menu['image'].'"'; 
            $t .= ' data-icon-class="'.htmlspecialchars($menu['icon_class']).'"'; 
            $t .= ' data-show-title="'.$menu['show_title'].'"'; 
            $t .= ' data-sub-title="'.$menu['text'].'"'; 
            $t .= ' data-position="'.$menu['position'].'"';
        }
        return $t;
    }

    /**
     *
     */
    public function parserMegaConfig($params)
    {
        if (!empty($params)) {
            foreach ($params as $key => $param) {
                if ($param) {
                    # validate module
                    // check menu has type subwith widget (not display if have submenu)
                    if ($param->subwith != 'widget' || ($param->subwith == 'widget' && count($param->rows) >0)) {
                        $this->mega_config[$key] = $param;
                    }
                }
            }
        }
    }

    public function hasMegaMenuConfig($menu)
    {
        $id = $menu['id_deomegamenu'];
        return isset($this->mega_config[$id]) ? $this->mega_config[$id] : array();
    }

    public function getFrontTree($parent = 0, $edit = false, $params = array(), $params_group = null, $hook = null)
    {
        
        $this->parserMegaConfig($params);
        
        if ($edit) {
            # validate module
            $this->edit_string = ' data-id="%s" ';
        } else {
            $this->is_live_edit = false;
            $this->model_menu_widget = new DeoWidgetModel();
            // $this->model_menu_widget->setTheme(Context::getContext()->shop->getTheme());
            $this->model_menu_widget->setTheme(Context::getContext()->shop->theme->getName());
            $this->model_menu_widget->langID = Context::getContext()->language->id;
            $this->model_menu_widget->loadWidgets(Context::getContext()->shop->id);
            $this->model_menu_widget->loadEngines();
        }
        $this->edit_string_col = ' data-colwidth="%s" data-class="%s" ';

        if ($edit) {
            $childs = $this->getChild(null, $params_group['id_group'], null, null, false);
        }else{
            $childs = $this->getChild(null, $params_group['id_group'], null, null, true);
        }

        foreach ($childs as $child) {
            $child['megaconfig'] = $this->hasMegaMenuConfig($child);
            $child['megamenu_id'] = $child['id_deomegamenu'];

            if (isset($child['megaconfig']->submenu) && $child['megaconfig']->submenu == 0) {
                # validate module
                $child['menu_class'] = $child['menu_class'];
            }

            $this->children[$child['id_parent']][] = $child;
        }

        $theme_name = Context::getContext()->shop->theme->getName();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $this->image_base_url = DeoHelper::getImgThemeUrl();
        $this->shop_url = $this->image_base_url;
        $output = '';
        $typesub = '';
        $group_type = '';
        $group_params = json_decode(DeoMegamenuHelper::base64Decode($params_group['params']), true);
        $group_type = $group_params['group_type'];
        if ($group_type == 'vertical') {
            $typesub = $group_params['type_sub'];
            
            if ($typesub == 'auto') {
                $theme = Context::getContext()->shop->theme_name;
                $cookie = DeoMegamenuHelper::getCookie();
                if ($hook && $hook == 'rightcolumn') {
                    if (isset($cookie[$theme.'_layout_dir']) && $cookie[$theme.'_layout_dir']) {
                        $layout = $cookie[$theme.'_layout_dir'];
                        if ($layout == 'right-left-main' || $layout == 'right-main-left' || $layout == 'left-right-main') {
                            $typesub = 'right';
                        } elseif ($layout == 'main-left-right') {
                            $typesub = 'left';
                        }
                    }
                } elseif ($hook && $hook == 'leftcolumn') {
                    if (isset($cookie[$theme.'_layout_dir']) && $cookie[$theme.'_layout_dir']) {
                        $layout = $cookie[$theme.'_layout_dir'];
                        if ($layout == 'right-main-left' || $layout == 'main-left-right') {
                            $typesub = 'left';
                        } elseif ($layout == 'left-right-main' || $layout == 'right-left-main') {
                            $typesub = 'right';
                        }
                    }
                } elseif (Context::getContext()->language->is_rtl) {
                    $typesub = 'left';
                } else {
                    $typesub = 'right';
                }
            }
        }
       
        if ($parent == 0){
            if ($group_type == 'vertical') {
                $output = '<ul class="nav navbar-nav megamenu vertical '.$typesub.'">';
            } else {
                $output = '<ul class="nav navbar-nav megamenu horizontal">';
            }
        }

        if ($this->hasChild($parent)) {
            if ($parent != 0){
                $level = 1;
                $output .= '<div class="dropdown-menu level'.$level.'"><div class="dropdown-menu-inner"><div class="row"><div class="col-sp-12 mega-col" data-colwidth="12" data-type="menu" ><div class="inner"><ul>';
            }
            $data = $this->getNodes($parent);
            foreach ($data as $menu) {

                $align = ' ';
                if (isset($menu['megaconfig']->align) && $menu['megaconfig']->align && $menu['sub_with'] == 'widget') {
                    # validate module
                    $align = $menu['megaconfig']->align;
                }

                $menu_class = '';
                if ($this->is_live_edit == true) {
                    $menu_class = (!$menu["active"]) ? $menu_class.' menu-disable' : $menu_class;
                    if ($menu["sub_with"] == 'widget') {
                        $menu_class .= ' enable-widget';
                    }else if($menu["sub_with"] == 'submenu'){
                        $menu_class .= ' enable-submenu';
                    }else{
                        $menu_class .= ' none';
                    }
                }

                $class_menu = '';
                $class_menu .= ($menu['icon_class'] || $menu['image']) ? ' has-icon' : '';
                $class_menu .= ($menu['icon_class']) ? ' icon-class' : '';
                $class_menu .= ($menu['image']) ? ' icon-img' : '';
                $class_menu .= ($menu['show_title']) ? ' show-title' : ' hide-title';
                $class_menu .= ($menu['text']) ? ' show-sub-title' : ' hide-sub-title';

                if ($menu["sub_with"] == 'none') {
                    $menu['image'] = ($menu['image']) ? $this->image_base_url.$menu['image'] : '#';
                    $output .= '<li class="nav-item '.$menu['menu_class'].$menu_class.'" '.$this->renderAttrs($menu).'>';
                    if ($menu['type'] == 'html' && $menu['content_text']) {
                        $output .= '<div href="javascript:void(0)" class="nav-link has-category">';
                            $output .= '<div class="menu-content">'.html_entity_decode($menu['content_text'], ENT_QUOTES, 'UTF-8').'</div>';
                        $output .= '</div>';
                    }else{
                        $output .= '<a href="'.$this->getLink($menu).'" target="'.$menu['target'].'" class="nav-link has-category">';
                            $output .= '<span class="content-menu'.$class_menu.'">';

                                $output .= '<span class="icons">';
                                    $output .= '<span class="menu-icon-class">'.$menu['icon_class'].'</span>';
                                    $output .= '<img class="menu-icon-image" src="'.$menu['image'].'"/>';
                                $output .= '</span>';

                                $output .= '<span class="title">';
                                    $output .= '<span class="menu-title">'.$menu['title'].'</span>';
                                    $output .= '<span class="sub-title">'.$menu['text'].'</span>';
                                $output .= '</span>';

                            $output .= '</span>';
                        $output .= '</a>';
                    }
                    $output .= '</li>';
                } else {
                    $menu_class = ($this->is_live_edit == true && $menu["sub_with"] == 'submenu') ? $menu_class.' active-submenu' : $menu_class;
                    if ($this->hasChild($menu['megamenu_id'])) {
                        if ($menu["sub_with"] != 'widget') {
                            $class_dropdown = ($parent == 0) ? 'dropdown' : 'dropdown-submenu';
                            $menu['image'] = ($menu['image']) ? $this->image_base_url.$menu['image'] : '#';
                            $output .= '<li class="nav-item parent '.$class_dropdown.' '.$menu['menu_class'].$menu_class.$align.'" '.$this->renderAttrs($menu).'>';
                            if ($menu['type'] == 'html' && $menu['content_text']) {
                                $output .= '<div href="javascript:void(0)" class="nav-link has-category">';
                                    $output .= '<div class="menu-content">'.html_entity_decode($menu['content_text'], ENT_QUOTES, 'UTF-8').'</div>';
                                $output .= '</div>';
                            }else{
                                $output .= '<a class="nav-link dropdown-toggle has-category" data-toggle="dropdown" href="'.$this->getLink($menu).'" target="'.$menu['target'].'">';

                                $output .= '<span class="content-menu'.$class_menu.'">';

                                    $output .= '<span class="icons">';
                                        $output .= '<span class="menu-icon-class">'.$menu['icon_class'].'</span>';
                                        $output .= '<img class="menu-icon-image" src="'.$menu['image'].'"/>';
                                    $output .= '</span>';

                                    $output .= '<span class="title">';
                                        $output .= '<span class="menu-title">'.$menu['title'].'</span>';
                                        $output .= '<span class="sub-title">'.$menu['text'].'</span>';
                                    $output .= '</span>';

                                $output .= '</span>';

                                $output .= '<i class="icon icon-arrow"></i>';
                                $output .= '</a><b class="caret"></b>';
                                
                                // if ($this->is_live_edit == false) {
                                if ($parent == 0){
                                    $output .= $this->genFrontTree($menu['megamenu_id'], 1, $menu, $typesub, $group_type);
                                }else{
                                    $output .= $this->genFrontTree($menu['megamenu_id'], $level + 1, $menu, $typesub, $group_type);
                                }
                            }
                            $output .= '</li>';
                        } else {
                            if (isset($menu['megaconfig']) && $menu['megaconfig'] && isset($menu['megaconfig']->rows) && $menu['megaconfig']->rows) {
                                $output .= $this->genMegaMenuByConfig($menu['megamenu_id'], 1, $menu, true, $typesub, $group_type);
                            } else {
                                $menu['image'] = ($menu['image']) ? $this->image_base_url.$menu['image'] : '#';
                                $output .= '<li class="nav-item parent '.$menu['menu_class'].$menu_class.'" '.$this->renderAttrs($menu).'>';
                                if ($menu['type'] == 'html' && $menu['content_text']) {
                                    $output .= '<div href="javascript:void(0)" class="nav-link has-category">';
                                        $output .= '<div class="menu-content">'.html_entity_decode($menu['content_text'], ENT_QUOTES, 'UTF-8').'</div>';
                                    $output .= '</div>';
                                }else{
                                    $output .= '<a class="nav-link dropdown-toggle has-category" data-toggle="dropdown" href="'.$this->getLink($menu).'" target="'.$menu['target'].'">';
                                        $output .= '<span class="content-menu'.$class_menu.'">';

                                            $output .= '<span class="icons">';
                                                $output .= '<span class="menu-icon-class">'.$menu['icon_class'].'</span>';
                                                $output .= '<img class="menu-icon-image" src="'.$menu['image'].'"/>';
                                            $output .= '</span>';

                                            $output .= '<span class="title">';
                                                $output .= '<span class="menu-title">'.$menu['title'].'</span>';
                                                $output .= '<span class="sub-title">'.$menu['text'].'</span>';
                                            $output .= '</span>';
                                        
                                        $output .= '</span>';
                                    $output .= '</a>';
                                }
                                $output .= '</li>';
                            }
                        }
                    } else if (!$this->hasChild($menu['megamenu_id']) && isset($menu['megaconfig']) && $menu['megaconfig'] && isset($menu['megaconfig']->rows) && $menu['megaconfig']->rows) {
                        # validate module
                        $output .= $this->genMegaMenuByConfig($menu['megamenu_id'], 1, $menu, true, $typesub, $group_type);
                    } else {
                        $menu['image'] = ($menu['image']) ? $this->image_base_url.$menu['image'] : '#';
                        $output .= '<li class="nav-item '.$menu['menu_class'].$menu_class.'" '.$this->renderAttrs($menu).'>';
                        if ($menu['type'] == 'html' && $menu['content_text']) {
                            $output .= '<div href="javascript:void(0)" class="nav-link has-category">';
                                $output .= '<div class="menu-content">'.html_entity_decode($menu['content_text'], ENT_QUOTES, 'UTF-8').'</div>';
                            $output .= '</div>';
                        }else{
                            $output .= '<a href="'.$this->getLink($menu).'" target="'.$menu['target'].'" class="nav-link has-category">';
                                $output .= '<span class="content-menu'.$class_menu.'">';

                                    $output .= '<span class="icons">';
                                        $output .= '<span class="menu-icon-class">'.$menu['icon_class'].'</span>';
                                        $output .= '<img class="menu-icon-image" src="'.$menu['image'].'"/>';
                                    $output .= '</span>';

                                    $output .= '<span class="title">';
                                        $output .= '<span class="menu-title">'.$menu['title'].'</span>';
                                        $output .= '<span class="sub-title">'.$menu['text'].'</span>';
                                    $output .= '</span>';
                                
                                $output .= '</span>';
                            $output .= '</a>';
                        }
                        $output .= '</li>';
                    }
                }
            }

            if ($parent != 0){
                $output .= '</ul></div></div></div></div></div>';
            }
        }

        if ($parent == 0){
            $output .= '</ul>';
        }

        $this->deo_module = null;
        return $output;
    }

    public function renderWidgetsInCol($col)
    {
        if (is_object($col) && isset($col->widgets) && !$this->edit_string) {
            $widgets = $col->widgets;
            $widgets = explode('|', $widgets);
            if (!empty($widgets)) {
                // unset($widgets[0]);

                $output = '';
                foreach ($widgets as $wid) {
                    $content = $this->model_menu_widget->renderContent($wid);
                    $output .= $this->model_menu_widget->getWidgetContent($wid, $content['type'], $content['data'], 0);
                }
                return $output;
            }
        }
    }

    /**
     * set data configuration for column
     */
    public function getColumnDataConfig($col)
    {
        $output = '';
        if (is_object($col)) {
            $vars = get_object_vars($col);
            if ($this->is_live_edit){
                foreach ($vars as $key => &$var) {
                    // add data column
                    $var = ($key == 'widgets') ? str_replace("wid-", "", $var) : $var;
                    $output .= ' data-'.$key.'="'.$var.'" ';
                }
                //set default col if have not configuration
                $output .= (!isset($vars['xxl'])) ? ' data-xxl="12" ' : '';
                $output .= (!isset($vars['xl'])) ? ' data-xl="12" ' : '';
                $output .= (!isset($vars['lg'])) ? ' data-lg="12" ' : '';
                $output .= (!isset($vars['md'])) ? ' data-md="12" ' : '';
                $output .= (!isset($vars['sm'])) ? ' data-sm="12" ' : '';
                $output .= (!isset($vars['xs'])) ? ' data-xs="12" ' : '';
                $output .= (!isset($vars['sp'])) ? ' data-sp="12" ' : '';
            }else{
                foreach ($vars as $key => &$var) {
                    // add data column
                    if ($key == 'widgets'){
                        $var = str_replace("wid-", "", $var);
                        $output .= ' data-'.$key.'="'.$var.'" ';
                        break;
                    }
                }
            }
        }
        
        return $output;
    }

    /**
     * set data width configuration for column
     */
    public function getColumnWidthConfig($col)
    {
        $output = '';
        if (is_object($col)) {
            $vars = get_object_vars($col);
            $output .= (isset($vars['xxl'])) ? ' col-xxl-'.$vars['xxl'] : ' col-xxl-12';
            $output .= (isset($vars['xl'])) ? ' col-xl-'.$vars['xl'] : ' col-xl-12';
            $output .= (isset($vars['lg'])) ? ' col-lg-'.$vars['lg'] : ' col-lg-12';
            $output .= (isset($vars['md'])) ? ' col-md-'.$vars['md'] : ' col-md-12';
            $output .= (isset($vars['sm'])) ? ' col-sm-'.$vars['sm'] : ' col-sm-12';
            $output .= (isset($vars['xs'])) ? ' col-xs-'.$vars['xs'] : ' col-xs-12';
            $output .= (isset($vars['sp'])) ? ' col-sp-'.$vars['sp'] : ' col-sp-12';
        }
        return $output;
    }

    /**
     * display mega content based on user configuration
     */
    public function genMegaMenuByConfig($parent_id, $level, $menu, $hascat = false, $typesub = '', $group_type = '')
    {
        $attrw = '';
        $align = ' ';
        $menu_class = '';
        if ($this->is_live_edit == true) {
            $menu_class = (!$menu["active"]) ? $menu_class.' menu-disable' : $menu_class;
            if ($menu["sub_with"] == 'widget') {
                $menu_class .= ' enable-widget';
            }else if($menu["sub_with"] == 'submenu'){
                $menu_class .= ' enable-submenu';
            }else{
                $menu_class .= ' none';
            }
        }
        $class_menu = '';
        $class_menu .= ($menu['icon_class'] || $menu['image']) ? ' has-icon' : '';
        $class_menu .= ($menu['icon_class']) ? ' icon-class' : '';
        $class_menu .= ($menu['image']) ? ' icon-img' : '';
        $class_menu .= ($menu['show_title']) ? ' show-title' : ' hide-title';
        $class_menu .= ($menu['text']) ? ' show-sub-title' : ' hide-sub-title';
        $menu['image'] = ($menu['image']) ? $this->image_base_url.$menu['image'] : '#';

        $class = $level > 1 ? 'dropdown-submenu' : 'dropdown';
        if (isset($menu['megaconfig']->align) && $menu['megaconfig']->align && $menu['sub_with'] == 'widget') {
            # validate module
            $align .= $menu['megaconfig']->align;
        }
        $output = '<li class="nav-item '.$menu['menu_class'].$menu_class.' parent '.$class.$align.'" '.$this->renderAttrs($menu).'>';
        if ($menu['type'] == 'html' && $menu['content_text']) {
            $output .= '<div href="javascript:void(0)" class="nav-link has-category">';
                $output .= '<div class="menu-content">'.html_entity_decode($menu['content_text'], ENT_QUOTES, 'UTF-8').'</div>';
            $output .= '</div>';
        }else{
            if ($hascat) {
                $output .= '<a href="'.$this->getLink($menu).'" class="nav-link dropdown-toggle has-category" data-toggle="dropdown" target="'.$menu['target'].'">';
            } else {
                $output .= '<a href="'.$this->getLink($menu).'" class="nav-link dropdown-toggle" data-toggle="dropdown" target="'.$menu['target'].'">';
            }
            
            $output .= '<span class="content-menu'.$class_menu.'">';

                $output .= '<span class="icons">';
                    $output .= '<span class="menu-icon-class">'.$menu['icon_class'].'</span>';
                    $output .= '<img class="menu-icon-image" src="'.$menu['image'].'"/>';
                $output .= '</span>';

                $output .= '<span class="title">';
                    $output .= '<span class="menu-title">'.$menu['title'].'</span>';
                    $output .= '<span class="sub-title">'.$menu['text'].'</span>';
                $output .= '</span>';

            $output .= '</span>';
            
            $output .= '<i class="icon icon-arrow"></i>';
            $output .= '</a><b class="caret"></b>';
            
            if ($menu['sub_with'] == 'widget') {
                if (isset($menu['megaconfig']->subwidth) && $menu['megaconfig']->subwidth) {
                    $attrw .= ((isset($menu['megaconfig']->align) && $menu['megaconfig']->align != 'aligned-fullwidth') || $group_type == 'vertical') ? ' style="width:'.$menu['megaconfig']->subwidth.'px;"' :  '';

                    # validate module
                    // if ($group_type == 'horizontal') {
                    //     $attrw .= ' style="width:'.$menu['megaconfig']->subwidth.'px;"';
                    // } else {
                    //     if (in_array(Context::getContext()->controller->controller_type, array('front', 'modulefront'))) {
                    //         if ($typesub == 'left') {
                    //             $attrw .= ' style="width:'.$menu['megaconfig']->subwidth.'px;"';
                    //         } else if ($typesub == 'right' || $typesub == 'auto') {
                    //             $attrw .= ' style="width:'.$menu['megaconfig']->subwidth.'px;"';
                    //         }
                    //     } else if (isset($typesub) && $typesub == 'left') {
                    //         $attrw .= ' style="width:'.$menu['megaconfig']->subwidth.'px;"';
                    //     } else {
                    //         $attrw .= ' style="width:'.$menu['megaconfig']->subwidth.'px;"';
                    //     }
                    // }
                }

                if ($this->is_live_edit) {
                    $class = 'dropdown-widget dropdown-menu';
                }else{
                    $class = 'dropdown-widget dropdown-menu loading';
                }
                
                $output .= '<div class="'.$class.'" '.$attrw.' ><div class="dropdown-menu-inner">';

                foreach ($menu['megaconfig']->rows as $row) {
                    $output .= '<div class="row">';
                    foreach ($row->cols as $col) {
                        $colclass = (isset($col->colclass) && !empty($col->colclass)) ? ($col->colclass) : '';
                        $output .= '<div class="mega-col '.$colclass.' '.$this->getColumnWidthConfig($col). '" '.$this->getColumnDataConfig($col).'> <div class="mega-col-inner">';
                        // $output .= $this->renderWidgetsInCol($col);
                        $output .= '</div></div>';
                    }
                    $output .= '</div>';
                }
                $output .= '</div></div>';
            }
        }
        
        $output .= '</li>';
        unset($parent_id); # validate module

        return $output;
    }

    /**
     *
     */
    public function getSelect($menu)
    {
        $page_name = Dispatcher::getInstance()->getController();
        $value = (int)$menu['item'];
        $result = '';
        switch ($menu['type']) {
            case 'product':
                if ($value == Tools::getValue('id_product') && $page_name == 'product') {
                    $result = ' active';
                }
                break;
            case 'category':
                if ($value == Tools::getValue('id_category') && $page_name == 'category') {
                    $result = ' active';
                }
                break;
            case 'cms':
                if ($value == Tools::getValue('id_cms') && $page_name == 'cms') {
                    $result = ' active';
                }
                break;
            case 'manufacturer':
                if ($value == Tools::getValue('id_manufacturer') && $page_name == 'manufacturer') {
                    $result = ' active';
                }
                break;
            case 'supplier':
                if ($value == Tools::getValue('id_supplier') && $page_name == 'supplier') {
                    $result = ' active';
                }
                break;
            case 'url':
                $value = $menu['url'];
                if (Tools::strpos($value, 'http') !== false) {
                    # validate module
                    $result = '';
                } else {
                    if ($value == $page_name) {
                        # validate module
                        $result = ' active';
                    } elseif (($value == 'index' || $value == 'index.php') && $page_name == 'index') {
                        # validate module
                        $result = ' active';
                    }
                }
                break;
            default:
                $result = '';
                break;
        }
        return $result;
    }

    public function genFrontTree($parent_id, $level, $parent, $typesub = '', $group_type = '')
    {
        $attrw = '';
        $class = 'dropdown-mega-menu dropdown-menu';
        if (isset($parent['megaconfig']->subwidth) && $parent['megaconfig']->subwidth && $parent['sub_with'] == 'widget') {
            # validate module
            if ($group_type == 'horizontal') {
                $attrw .= ' style="width:'.$parent['megaconfig']->subwidth.'px"';
            } else {
                if (isset($typesub) && $typesub == 'left') {
                    $attrw .= ' style="width:'.$parent['megaconfig']->subwidth.'px;"';
                } else {
                    $attrw .= ' style="width:'.$parent['megaconfig']->subwidth.'px;"';
                }
            }
        }

        if ($this->hasChild($parent_id)) {
            $data = $this->getNodes($parent_id);
            if ($parent['sub_with'] == 'submenu') {
                $output = '<div class="'.$class.' level'.$level.'" '.$attrw.'><div class="dropdown-menu-inner">';
                $row = '<div class="row"><div class="col-sp-12 mega-col" data-colwidth="12" data-type="menu" ><div class="inner"><ul>';
                foreach ($data as $menu) {
                    # validate module
                    $row .= $this->renderMenuContent($menu, $level + 1, $typesub, $group_type);
                }
                $row .= '</ul></div></div></div>';
                $row .= '</div></div>';
                $output .= $row;
                return $output;
            }
            if (!empty($parent['megaconfig']->rows)) {
                $output = '<div class="'.$class.' level'.$level.'" '.$attrw.'><div class="dropdown-menu-inner">';
                foreach ($parent['megaconfig']->rows as $rows) {
                    foreach ($rows as $rowcols) {
                        $output .= '<div class="row">';
                        foreach ($rowcols as $col) {
                            $colclass = (isset($col->colclass) && !empty($col->colclass)) ? ($col->colclass) : '';
                            if (isset($col->type) && $col->type == 'menu') {
                                $scol = '<div class="mega-col '.$colclass.' col-md-'.$col->colwidth.'" data-type="menu" '.$this->getColumnDataConfig($col).'><div class="mega-col-inner">';
                                $scol .= '<ul>';
                                foreach ($data as $menu) {
                                    # validate module
                                    $scol .= $this->renderMenuContent($menu, $level + 1, $typesub, $group_type);
                                }
                                $scol .= '</ul>';
                            } else {
                                $scol = '<div class="mega-col '.$colclass.' col-md-'.$col->colwidth.'"  '.$this->getColumnDataConfig($col).'><div class="mega-col-inner">';
                                $scol .= $this->renderWidgetsInCol($col);
                            }
                            $scol .= '</div></div>';
                            $output .= $scol;
                        }
                        $output .= '</div>';
                    }
                }$output .= '</div></div>';
            } else {
                $output = '<div class="'.$class.' level'.$level.'" '.$attrw.'><div class="dropdown-menu-inner">';
                $row = '<div class="row"><div class="col-sp-12 mega-col" data-colwidth="12" data-type="menu" ><div class="inner"><ul>';
                foreach ($data as $menu) {
                    # validate module
                    $row .= $this->renderMenuContent($menu, $level + 1, $typesub, $group_type);
                }
                $row .= '</ul></div></div></div>';
                $row .= '</div></div>';
                $output .= $row;
            }

            return $output;
        }

        return '';
    }

    // public function genCatNoTree($context, $categories)
    // {
        // $html = '<ul class="dropdown-menu level1">';
        // foreach ($categories as $val) {
            // $html .= '<li><a href='.$context->link->getCategoryLink($val['id_category'], $val['link_rewrite']).' title='.$val['name'].'><span class="menu-title">'.$val['name'].'</span></a></li>';
        // }
        // $html .= '</ul>';

        // return $html;
    // }

    public function getCategorie($submenu_catids, $context)
    {
        $groups = implode(', ', array_map('intval', Customer::getGroupsStatic((int)$context->customer->id)));
        $submenu_catids =  implode(', ', array_map('intval', explode(',', $submenu_catids)));
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT DISTINCT c.id_parent, c.id_category, c.level_depth , cl.name, cl.link_rewrite
            FROM `'._DB_PREFIX_.'category` c
            INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$context->language->id.Shop::addSqlRestrictionOnLang('cl').')
            INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = '.(int)$context->shop->id.')
            WHERE (c.`active` = 1 OR c.`id_category` = '.(int)Configuration::get('PS_HOME_CATEGORY').')
            AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
            AND c.id_category IN (SELECT id_category FROM `'._DB_PREFIX_.'category_group` WHERE `id_group` IN ('.$groups.') AND id_category IN ('.$submenu_catids.'))
            ORDER BY `level_depth` ASC, cs.`position`');
        return $result;
    }


    /**
     *
     */
    public function renderMenuContent($menu, $level, $typesub = '', $group_type = '')
    {
        $output = '';
        $menu_class = '';
        $class = '';
        if ($this->is_live_edit == true) {
            $menu_class = (!$menu["active"]) ? $menu_class.' menu-disable' : $menu_class;
            if ($menu["sub_with"] == 'widget') {
                $menu_class .= ' enable-widget';
            }else if($menu["sub_with"] == 'submenu'){
                $menu_class .= ' enable-submenu';
            }else{
                $menu_class .= ' none';
            }
        }

        $menu_class = $menu_class.$class;

        $class_menu = '';
        $class_menu .= ($menu['icon_class'] || $menu['image']) ? ' has-icon' : '';
        $class_menu .= ($menu['icon_class']) ? ' icon-class' : '';
        $class_menu .= ($menu['image']) ? ' icon-img' : '';
        $class_menu .= ($menu['show_title']) ? ' show-title' : ' hide-title';
        $class_menu .= ($menu['text']) ? ' show-sub-title' : ' hide-sub-title';
        $menu['image'] = ($menu['image']) ? $this->image_base_url.$menu['image'] : '#';

        if ($this->hasChild($menu['megamenu_id'])) {
            // $output .= '<li data-menu-type="'.$menu['type'].'" class="nav-item parent dropdown-submenu'.$menu['menu_class'].$menu_class.'" '.$this->renderAttrs($menu).'>';
            $output .= '<li class="nav-item parent dropdown-submenu'.$menu['menu_class'].$menu_class.'" '.$this->renderAttrs($menu).'>';
            if ($menu['type'] == 'html' && $menu['content_text']) {
                $output .= '<div href="javascript:void(0)" class="nav-link has-category">';
                    $output .= '<div class="menu-content">'.html_entity_decode($menu['content_text'], ENT_QUOTES, 'UTF-8').'</div>';
                $output .= '</div>';
            } else if($menu['show_title']) {
                $output .= '<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="'.$this->getLink($menu).'">';
                //$t = '%s';    # validate module
                    $output .= '<span class="content-menu'.$class_menu.'">';

                        $output .= '<span class="icons">';
                            $output .= '<span class="menu-icon-class">'.$menu['icon_class'].'</span>';
                            $output .= '<img class="menu-icon-image" src="'.$menu['image'].'"/>';
                        $output .= '</span>';

                        $output .= '<span class="title">';
                            $output .= '<span class="menu-title">'.$menu['title'].'</span>';
                            $output .= '<span class="sub-title">'.$menu['text'].'</span>';
                        $output .= '</span>';

                    $output .= '</span>';

                    $output .= '<i class="icon icon-arrow"></i>';
                $output .= '</a>';
                $output .= '<b class="caret"></b>';
            }
            
            $output .= $this->genFrontTree($menu['megamenu_id'], $level, $menu, $typesub, $group_type);
            $output .= '</li>';
        } else if ($menu['megaconfig'] && $menu['megaconfig']->rows) {
            # validate module
            $output .= $this->genMegaMenuByConfig($menu['megamenu_id'], $level, $menu, false, $typesub, $group_type);
        } else {
            // $output .= '<li data-menu-type="'.$menu['type'].'" class="nav-item '.$menu['menu_class'].$menu_class.'" '.$this->renderAttrs($menu).'>';
            $output .= '<li class="nav-item '.$menu['menu_class'].$menu_class.'" '.$this->renderAttrs($menu).'>';
            if ($menu['type'] == 'html' && $menu['content_text']) {
                $output .= '<div href="javascript:void(0)" class="nav-link has-category">';
                    $output .= '<div class="menu-content">'.html_entity_decode($menu['content_text'], ENT_QUOTES, 'UTF-8').'</div>';
                $output .= '</div>';
            }else if($menu['show_title']) {
                $output .= '<a class="nav-link" href="'.$this->getLink($menu).'" target="'.$menu['target'].'">';
                    $output .= '<span class="content-menu'.$class_menu.'">';

                        $output .= '<span class="icons">';
                            $output .= '<span class="menu-icon-class">'.$menu['icon_class'].'</span>';
                            $output .= '<img class="menu-icon-image" src="'.$menu['image'].'"/>';
                        $output .= '</span>';

                        $output .= '<span class="title">';
                            $output .= '<span class="menu-title">'.$menu['title'].'</span>';
                            $output .= '<span class="sub-title">'.$menu['text'].'</span>';
                        $output .= '</span>';

                    $output .= '</span>';

                $output .= '</a>';
            }
            $output .= '</li>';
        }

        return $output;
    }

    public function getLink($menu)
    {
        if ($this->edit_string) {
            # validate module
            return '#';
        }
        $value = (int)$menu['item'];
        $result = '';
        $link = new Link();
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        switch ($menu['type']) {
            case 'product':
                if (Validate::isLoadedObject($obj_pro = new Product($value, true, $id_lang))) {
                    # validate module
                    $result = $link->getProductLink((int)$obj_pro->id, $obj_pro->link_rewrite, null, null, $id_lang, null, (int)Product::getDefaultAttribute((int)$obj_pro->id), false, false, true);
                }
                break;
            case 'category':
                if (Validate::isLoadedObject($obj_cate = new Category($value, $id_lang))) {
                    # validate module
                    $result = $link->getCategoryLink((int)$obj_cate->id, $obj_cate->link_rewrite, $id_lang);
                }
                break;
            case 'cms':
                if (Validate::isLoadedObject($obj_cms = new CMS($value, $id_lang))) {
                    # validate module
                    $result = $link->getCMSLink((int)$obj_cms->id, $obj_cms->link_rewrite, $id_lang);
                }
                break;
            case 'cms_category':
                if (Validate::isLoadedObject($obj_cate = new CMSCategory($value, $id_lang))) {
                    # validate module
                    $result = $link->getCMSCategoryLink((int)$obj_cate->id, $obj_cate->link_rewrite);
                }
                break;
            case 'url':
                // MENU TYPE : URL
                if (preg_match('/http:\/\//', $menu['url']) || preg_match('/https:\/\//', $menu['url'])) {
                    // ABSOLUTE LINK : default
                } else {
                    // RELATIVE LINK : auto insert host
                    $host_name = DeoMegamenuHelper::getBaseLink().DeoMegamenuHelper::getLangLink();
                    $menu['url'] = $host_name.$menu['url'];
                }

                $value = $menu['url'];
                $regex = '((https?|ftp)\:\/\/)?'; // SCHEME
                $regex .= '([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?'; // User and Pass
                $regex .= '([a-z0-9-.]*)\.([a-z]{2,3})'; // Host or IP
                $regex .= '(\:[0-9]{2,5})?'; // Port
                $regex .= '(\/([a-z0-9+\$_-]\.?)+)*\/?'; // Path
                $regex .= '(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?'; // GET Query
                $regex .= '(#[a-z_.-][a-z0-9+\$_.-]*)?'; // Anchor
                if ($value == 'index' || $value == 'index.php') {
                    $result = $link->getPageLink('index.php', false, $id_lang);
                    break;
                } elseif ($value == '#' || preg_match("/^$regex$/", $value)) {
                    $result = $value;
                    break;
                } else {
                    $result = $value;
                }
                break;
            case 'manufacture':
                if (Validate::isLoadedObject($obj_manu = new Manufacturer($value, $id_lang))) {
                    # validate module
                    $result = $link->getManufacturerLink((int)$obj_manu->id, $obj_manu->link_rewrite, $id_lang);
                }
                break;
            case 'supplier':
                if (Validate::isLoadedObject($obj_supp = new Supplier($value, $id_lang))) {
                    # validate module
                    $result = $link->getSupplierLink((int)$obj_supp->id, $obj_supp->link_rewrite, $id_lang);
                }
                break;
            case 'controller':
                //getPageLink('history', true, Context::getContext()->language->id, null, false, $id_shop);
                $result = $link->getPageLink($menu['item'], null, $id_lang, null, false, $id_shop);
                if ($menu['item_parameter'] != '') {
                    $result .= $menu['item_parameter'];
                }
                break;
            default:
                $result = '#';
                break;
        }
        return $result;
    }

    /**
     *
     */
    public function getColWidth($menu, $cols)
    {
        $output = array();

        $split = preg_split('#\s+#', $menu['submenu_colum_width']);
        if (!empty($split) && !empty($menu['submenu_colum_width'])) {
            foreach ($split as $sp) {
                $tmp = explode('=', $sp);
                if (count($tmp) > 1) {
                    # validate module
                    $output[trim(preg_replace('#col#', '', $tmp[0]))] = (int)$tmp[1];
                }
            }
        }
        $tmp = array_sum($output);
        $spans = array();
        $t = 0;
        for ($i = 1; $i <= $cols; $i++) {
            if (array_key_exists($i, $output)) {
                # validate module
                $spans[$i] = 'col-sm-'.$output[$i];
            } else {
                if ((12 - $tmp) % ($cols - count($output)) == 0) {
                    # validate module
                    $spans[$i] = 'col-sm-'.((12 - $tmp) / ($cols - count($output)));
                } else {
                    if ($t == 0) {
                        # validate module
                        $spans[$i] = 'col-sm-'.( ((11 - $tmp) / ($cols - count($output))) + 1 );
                    } else {
                        # validate module
                        $spans[$i] = 'col-sm-'.( ((11 - $tmp) / ($cols - count($output))) + 0 );
                    }
                    $t++;
                }
            }
        }
        return $spans;
    }
    
    public function validateFields($die = true, $error_return = false)
    {
        $type = Tools::getValue('type');

        if ($type == 'url') {
            foreach (Language::getIDs(false) as $id_lang) {
                $temp = Tools::getValue('url_'.(int)$id_lang);
                $temp = $this->url[$id_lang];
                if (empty($temp)) {
                    $message = 'URL is required';
                    if ($die) {
                        throw new PrestaShopException($message);
                    }
                    return $error_return ? $message : false;
                }
            }
        }
        return parent::validateFields($die, $error_return);
    }
    
    // reset params widget by group
    public function resetParamsWidget($id_group)
    {
        $sql = '
                UPDATE `'._DB_PREFIX_.'deomegamenu`
                SET `params_widget`= ""
                WHERE `id_group` = '.(int)$id_group.' AND `id_parent` = 0';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
    }
    
    // get all menu root of group
    public static function getMenusRoot($id_group)
    {
        $sql = '
                SELECT `id_deomegamenu` FROM `'._DB_PREFIX_.'deomegamenu`
                WHERE `id_group` = '.(int)$id_group.' AND `id_parent` = 0';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    // get menu data by id
    public function getMenuById($id)
    {
        $sql = '
                SELECT * FROM `'._DB_PREFIX_.'deomegamenu`
                WHERE `id_deomegamenu` = '.(int)$id;
        return Db::getInstance()->getRow($sql);
    }
    
    // get params widget by group
    public function getParamsWidget()
    {
        $sql = 'SELECT `params_widget` FROM `'._DB_PREFIX_.'deomegamenu`
                WHERE `id_deomegamenu` = '.(int)$this->id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
    
    // get params widget by group
    public function updateParamsWidget($params)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'deomegamenu`
                SET `params_widget`= "'.pSQL($params).'"
                WHERE `id_deomegamenu` = '.(int)$this->id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
    }

    public function updateSubWith($params)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'deomegamenu`
                SET `sub_with`= "'.pSQL($params).'"
                WHERE `id_deomegamenu` = '.(int)$this->id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
    }


    // get all menu root of group
    public static function getMegamenuRoot($id_group)
    {
        $sql = '
                SELECT m.`id_deomegamenu`, m.`params_widget` FROM `'._DB_PREFIX_.'deomegamenu` m
                LEFT JOIN `'._DB_PREFIX_.'deomegamenu_group` g ON g.`id_deomegamenu_group` = m.`id_group`
                WHERE m.`id_group` = '.(int)$id_group.' AND m.`id_parent` = 0';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }


    /**
     *  Start functions move from AdminDeoMegamenuController
     */
    public function GetWidget()
    {
        if(Tools::getIsset('allWidgets')){
            $dataForm = json_decode( Tools::getValue('dataForm'), 1);
            foreach ($dataForm as &$widget) {
                $widget['html'] = $this->renderwidget($widget['id_shop'], $widget['id_widget']);
            }
            die(json_encode($dataForm));
        }
    }

    public function renderwidget($id_shop, $widgets)
    {
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }
        $widgets = explode('|', $widgets);

        Context::getContext()->smarty->assign(array(
            'link' => Context::getContext()->link,
        ));
        if (!empty($widgets)) {
            $output = '';
            $model = new DeoWidgetModel();
            $model->setTheme(Context::getContext()->shop->theme->getName());
            $model->langID = Context::getContext()->language->id;
            $model->loadWidgets($id_shop);
            $model->loadEngines();
            
            foreach ($widgets as $wid) {
                $content = $model->renderContent($wid);
                $html = $this->getWidgetContent($wid, $content['type'], $content['data']);
                $output .= $html;
            }

            return $output;
        }
        return '';
    }


    public function getThemeMediaDir($media = null)
    {
        $media_dir = '';

        if (version_compare(_PS_VERSION_, '1.7.4.0', '>=') || version_compare(Configuration::get('PS_VERSION_DB'), '1.7.4.0', '>=')) {
            if ($media == 'img') {
                $media_dir = 'assets/img/modules/deotemplate/';
            }
            
            if ($media == 'css') {
                $media_dir = 'assets/css/modules/deotemplate/views/';
            }
        } else {
            $media_dir = 'modules/deotemplate/';
        }
        return $media_dir;
    }
}
