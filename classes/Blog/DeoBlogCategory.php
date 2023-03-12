<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

require_once(_PS_MODULE_DIR_.'deotemplate/libs/HelperBlog.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlog.php');

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class DeoBlogCategory extends ObjectModel
{
    public $id;
    public $id_deoblog_category;
    public $image;
    public $image_link;
    public $use_image_link;
    public $is_root;
    public $rate_image;
    public $id_parent = 1;
    public $level_depth;
    public $active = 1;
    public $position;
    public $class_css;
    public $date_add;
    public $date_upd;
    # Lang
    public $title;
    public $content;
    public $template;
    public $meta_keywords;
    public $meta_description;
    private $shop_url;
    public $link_rewrite;
    private $megaConfig = array();
    private $_editStringCol = '';
    private $_isLiveEdit = true;
    private $_module = null;
    public $id_shop;
    public $select_data = array();
    public $randkey;

    public function setModule($module)
    {
        $this->_module = $module;
    }
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'deoblog_category',
        'primary' => 'id_deoblog_category',
        'multilang' => true,
        'fields' => array(
            'image' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName'),
            'use_image_link' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'image_link' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'rate_image' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 25),
            'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'level_depth' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'is_root' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'position' => array('type' => self::TYPE_INT),
            'template' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 200),
            'class_css' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 25),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            # Lang fields
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'size' => 128),
            'randkey' => array('type' => self::TYPE_STRING, 'lang' => false, 'size' => 255),
        ),
    );

    public static function findByRewrite($parrams)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $id = 0;
        if (isset($parrams['link_rewrite']) && $parrams['link_rewrite']) {
            $sql = 'SELECT cl.id_deoblog_category FROM '._DB_PREFIX_.'deoblog_category_lang cl';
            $sql .= ' INNER JOIN '._DB_PREFIX_.'deoblog_category_shop cs on cl.id_deoblog_category=cs.id_deoblog_category AND id_shop='.$id_shop;
            $sql .= ' INNER JOIN '._DB_PREFIX_.'deoblog_category      cc on cl.id_deoblog_category=cc.id_deoblog_category AND cl.id_deoblog_category != cc.id_parent';  # FIX : PARENT IS NOT THIS CATEGORY
            //$sql .= ' WHERE id_lang = ' . $id_lang ." AND link_rewrite = '".$parrams['link_rewrite']."'";
            $sql .= " AND link_rewrite = '".pSQL($parrams['link_rewrite'])."'";

            if ($row = Db::getInstance()->getRow($sql)) {
                $id = $row['id_deoblog_category'];
            }
        }
        return new DeoBlogCategory($id, $id_lang);
    }

    public function add($autodate = true, $null_values = false)
    {
        $this->position = self::getLastPosition((int)$this->id_parent);
        $this->level_depth = $this->calcLevelDepth();
        $id_shop = DeoHelper::getIDShop();
        $res = parent::add($autodate, $null_values);
        $sql = 'INSERT INTO `'._DB_PREFIX_.'deoblog_category_shop` (`id_shop`, `id_deoblog_category`)
            VALUES('.(int)$id_shop.', '.(int)$this->id.')';
        $res &= Db::getInstance()->execute($sql);
        $this->cleanPositions($this->id_parent);
        return $res;
    }

    public function update($null_values = false)
    {
        $this->level_depth = $this->calcLevelDepth();
        return parent::update($null_values);
    }

    protected function recursiveDelete(&$to_delete, $id_deoblog_category)
    {
        if (!is_array($to_delete) || !$id_deoblog_category) {
            die(Tools::displayError());
        }

        $result = Db::getInstance()->executeS('
        SELECT `id_deoblog_category`
        FROM `'._DB_PREFIX_.'deoblog_category`
        WHERE `id_parent` = '.(int)$id_deoblog_category);
        foreach ($result as $row) {
            $to_delete[] = (int)$row['id_deoblog_category'];
            $this->recursiveDelete($to_delete, (int)$row['id_deoblog_category']);
        }
    }

    public function delete()
    {
        if ($this->id == 1) {
            return false;
        }
        $this->clearCache();

        // Get children categories
        $to_delete = array((int)$this->id);
        $this->recursiveDelete($to_delete, (int)$this->id);
        $to_delete = array_unique($to_delete);

        // Delete CMS Category and its child from database
        $list = count($to_delete) > 1 ? implode(',', array_map('intval', $to_delete)) : (int)$this->id;
        //delete blog
        //get all blog from category ID
        //$where   = '`id_deoblog_category` IN (' . $list . ')';
        $result_blog = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_deoblog` as id FROM `'._DB_PREFIX_.'deoblog` WHERE `id_deoblog_category` IN ('.pSQL($list).')');
        foreach ($result_blog as $value) {
            $blog = new DeoBlog($value['id']);
            $blog->delete();
        }

        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'deoblog_category` WHERE `id_deoblog_category` IN ('.pSQL($list).')');
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'deoblog_category_shop` WHERE `id_deoblog_category` IN ('.pSQL($list).')');
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'deoblog_category_lang` WHERE `id_deoblog_category` IN ('.pSQL($list).')');
        DeoBlogCategory::cleanPositions($this->id_parent);

        $id_shop = (int)Context::getContext()->shop->id;
        require_once(_PS_MODULE_DIR_.'deotemplate/classes/Blog/DeoBlogImage.php');
        DeoBlogImage::removeGenerateImageCategory($this->id, $id_shop);
        
        return true;
    }

    public static function countCats()
    {
        $row = Db::getInstance()->executeS('SELECT COUNT(id_deoblog_category) as total FROM `'._DB_PREFIX_.'deoblog_category` WHERE  id_deoblog_category!=1 AND 1=1');
        return $row[0]['total'];
    }

    public function deleteSelection($menus)
    {
        $return = 1;
        foreach ($menus as $id_deoblog_category) {
            $obj_menu = new DeoBlogCategory($id_deoblog_category);
            $return &= $obj_menu->delete();
        }
        return $return;
    }

    public function calcLevelDepth()
    {
        $parentdeoblog_category = new DeoBlogCategory($this->id_parent);
        if (!$parentdeoblog_category) {
            die('parent Menu does not exist');
        }
        return $parentdeoblog_category->level_depth + 1;
    }

    public function updatePosition($way, $position)
    {
        $sql = 'SELECT cp.`id_deoblog_category`, cp.`position`, cp.`id_parent`
            FROM `'._DB_PREFIX_.'deoblog_category` cp
            WHERE cp.`id_parent` = '.(int)$this->id_parent.'
            ORDER BY cp.`position` ASC';
        !$res = Db::getInstance()->executeS($sql);
        if ($res) {
            return false;
        }
        
        foreach ($res as $menu) {
            if ((int)$menu['id_deoblog_category'] == (int)$this->id) {
                $moved_menu = $menu;
            }
        }

        if (!isset($moved_menu) || !isset($position)) {
            return false;
        }
        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return (Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'deoblog_category`
            SET `position`= `position` '.($way ? '- 1' : '+ 1').'
            WHERE `position`
            '.($way ? '> '.(int)$moved_menu['position'].' AND `position` <= '.(int)$position : '< '.(int)$moved_menu['position'].' AND `position` >= '.(int)$position).'
            AND `id_parent`='.(int)$moved_menu['id_parent']) && Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'deoblog_category`
            SET `position` = '.(int)$position.'
            WHERE `id_parent` = '.(int)$moved_menu['id_parent'].'
            AND `id_deoblog_category`='.(int)$moved_menu['id_deoblog_category']));
    }

    public static function cleanPositions($id_parent)
    {
        $result = Db::getInstance()->executeS('
        SELECT `id_deoblog_category`
        FROM `'._DB_PREFIX_.'deoblog_category`
        WHERE `id_parent` = '.(int)$id_parent.'
        ORDER BY `position`');
        $sizeof = count($result);
        for ($i = 0; $i < $sizeof; ++$i) {
            $sql = '
            UPDATE `'._DB_PREFIX_.'deoblog_category`
            SET `position` = '.(int)$i.'
            WHERE `id_parent` = '.(int)$id_parent.'
            AND `id_deoblog_category` = '.(int)$result[$i]['id_deoblog_category'];
            Db::getInstance()->execute($sql);
        }
        return true;
    }

    public static function getLastPosition($id_parent)
    {
        return (Db::getInstance()->getValue('SELECT MAX(position)+1 FROM `'._DB_PREFIX_.'deoblog_category` WHERE `id_parent` = '.(int)$id_parent));
    }

    public function getInfo($id_deoblog_category, $id_lang = null, $id_shop = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }
        $sql = 'SELECT m.*, md.title, md.description, md.content_text
                FROM '._DB_PREFIX_.'megamenu m
                LEFT JOIN '._DB_PREFIX_.'deoblog_category_lang md ON m.id_deoblog_category = md.id_deoblog_category AND md.id_lang = '.(int)$id_lang
                .' JOIN '._DB_PREFIX_.'deoblog_category_shop bs ON m.id_deoblog_category = bs.id_deoblog_category AND bs.id_shop = '.(int)($id_shop);
        $sql .= ' WHERE m.id_deoblog_category='.(int)$id_deoblog_category;

        return Db::getInstance()->executeS($sql);
    }

    public function getChild($id_deoblog_category = null, $id_lang = null, $id_shop = null, $active = false)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = ' SELECT m.*, md.*
                FROM '._DB_PREFIX_.'deoblog_category m
                LEFT JOIN '._DB_PREFIX_.'deoblog_category_lang md ON m.id_deoblog_category = md.id_deoblog_category AND md.id_lang = '.(int)$id_lang
                .' JOIN '._DB_PREFIX_.'deoblog_category_shop bs ON m.id_deoblog_category = bs.id_deoblog_category AND bs.id_shop = '.(int)($id_shop);
        if ($active) {
            $sql .= ' WHERE m.`active`=1 ';
        }

        if ($id_deoblog_category != null) {
            # validate module
            $sql .= ' WHERE id_parent='.(int)$id_deoblog_category;
        }
        $sql .= ' ORDER BY `position` ';
        return Db::getInstance()->executeS($sql);
    }

    public function getAllChild($id_deoblog_category = null, $id_lang = null, $id_shop = null, $active = false)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = ' SELECT m.*, md.*, m.id_deoblog_category AS id_category
                FROM '._DB_PREFIX_.'deoblog_category m
                LEFT JOIN '._DB_PREFIX_.'deoblog_category_lang md ON m.id_deoblog_category = md.id_deoblog_category AND md.id_lang = '.(int)$id_lang.' 
                LEFT JOIN '._DB_PREFIX_.'deoblog_category_shop bs ON m.id_deoblog_category = bs.id_deoblog_category AND bs.id_shop = '.(int)($id_shop);
        if ($active) {
            $sql .= ' WHERE m.`active`=1 ';
        }

        if ($id_deoblog_category != null) {
            # validate module
            $sql .= ' WHERE id_parent='.(int)$id_deoblog_category;
        }
        $sql .= ' ORDER BY `position` ';

        return Db::getInstance()->executeS($sql);
    }

    public function hasChild($id)
    {
        return isset($this->children[$id]);
    }

    public function getRoot()
    {
        $id_shop = Context::getContext()->shop->id;

        $sql = ' SELECT m.id_deoblog_category AS id_category 
                FROM '._DB_PREFIX_.'deoblog_category m 
                LEFT JOIN '._DB_PREFIX_.'deoblog_category_shop bs ON m.id_deoblog_category = bs.id_deoblog_category AND bs.id_shop = '.(int)($id_shop).'
                WHERE m.`is_root`=1 ';

        $result = Db::getInstance()->executeS($sql);

        if (count($result)){
            return (int) $result[0]['id_category'];
        }else{
            return false;
        }
    }

    public function getNodes($id)
    {
        return $this->children[$id];
    }

    public function getTree($id = null)
    {
        $childs = $this->getChild($id);
        foreach ($childs as $child) {
            # validate module
            $this->children[$child['id_parent']][] = $child;
        }

        // $id_root = $this->getRoot();
        // $parent = ($id_root) ? $id_root : 1;
        // $output = $this->genTree($parent, 1);
        // return $output;
    }

    public function getDropdown($id, $selected = 1)
    {
        $output = array();
        $this->children = array();
        $childs = $this->getChild($id);
        foreach ($childs as $child) {
            # validate module
            $this->children[$child['id_parent']][] = $child;
        }

        $id_root = $this->getRoot();
        // $output = array(array('id' => '1', 'title' => 'Root', 'selected' => ''));
        $output = $this->genOption($id_root, 0, $selected, $output);

        return $output;
    }

    /**
     * @param int $level (default 0 )
     * @param type $output ( default array )
     * @param type $output
     */
    public function genOption($parent, $level, $selected, $output)
    {
        # module validation
        !is_null($level) ? $level : $level = 0;
        is_array($output) ? true : $output = array();
        
        if ($this->hasChild($parent)) {
            $data = $this->getNodes($parent);
            foreach ($data as $menu) {
                //$select = $selected == $menu['id_deoblog_category'] ? 'selected="selected"' : "";
                $output[] = array('id' => $menu['id_deoblog_category'], 'title' => str_repeat('-', $level).' '.$menu['title'].' (ID:'.$menu['id_deoblog_category'].')', 'selected' => $selected);
                if ($menu['id_deoblog_category'] != $parent) {
                    $output = $this->genOption($menu['id_deoblog_category'], $level + 1, $selected, $output);
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
            Context::getContext()->smarty->assign(array(
                'parent' => $parent,
                'level' => $level,
                't' => $t,
                'data' => $data,
                'param_id_deoblog_category' => Tools::getValue('id_deoblog_category'),
                'model_deoblog_category' => $this,
            ));
            return Context::getContext()->smarty->fetch( _PS_MODULE_DIR_ . 'deotemplate/views/templates/admin/deo_blog_categories/genTree.tpl');
        }
        return '';
    }

    public function genTreeForPageBuilder($parent, $level, $select = array())
    {
        if ($this->hasChild($parent)) {
            $data = $this->getNodes($parent);
            Context::getContext()->smarty->assign(array(
                'parent' => $parent,
                'level' => $level,
                'data' => $data,
                'select' => $select,
                'model_deoblog_category' => $this,
            ));
            return Context::getContext()->smarty->fetch( _PS_MODULE_DIR_ . 'deotemplate/views/templates/admin/deo_blog_categories/genTreeForPageBuilder.tpl');
        }
        return '';
    }

    public function getTreeForPageBuilder($select = array(), $id = null)
    {
        $childs = $this->getChild($id);
        foreach ($childs as $child) {
            $this->children[$child['id_parent']][] = $child;
        }

        $id_root = $this->getRoot();
        $parent = ($id_root) ? $id_root : 1;
        $output = $this->genTreeForPageBuilder($parent, 1, $select);
        return $output;
    }

    public function getFrontEndTree($id, $helper)
    {
        $childs = $this->getChild(null);

        foreach ($childs as $child) {
            # validate module
            $this->children[$child['id_parent']][] = $child;
        }

        $parent = $id;
        $output = $this->genFontEndTree($parent, 1, $helper);

        return $output;
    }

    public function genFontEndTree($parent, $level, $helper)
    {
        if ($this->hasChild($parent)) {
            $data = $this->getNodes($parent);
            $t = $level == 1 ? ' tree dhtml' : ' collapse';
            $id_sub = '';
            if ($level != 1) {
                $id_sub = 'sub_'.$parent;
                $output = '<ul id="'.$id_sub.'" class="level'.$level.$t.' ">';
            } else {
                $output = '<ul class="level'.$level.$t.' ">';
            }
            foreach ($data as $menu) {
                if (isset($menu['active']) && $menu['active']) {
                    $params = array(
                        'link_rewrite' => $menu['link_rewrite'],
                        'id' => $menu['id_deoblog_category']
                    );

                    $category_link = $helper->getBlogCatLink($params);

                    $cls = Tools::getValue('id_deoblog_category') == $menu['id_deoblog_category'] ? 'selected ' : '';
                    $cls .= $this->hasChild($menu['id_deoblog_category']) ? 'parent ' : '';
                    $output .= '<li id="list_'.$menu['id_deoblog_category'].'" class="'.$cls.$menu['class_css'].'"><a href="'.$category_link.'" title="'.$menu['title'].'">';
                    $output .= '<span>'.$menu['title'].'</span></a> ';

                    if ($menu['id_deoblog_category'] != $parent) {
                        # validate module
                        if ($this->hasChild($menu['id_deoblog_category'])) {
                            $output .= '<div class="navbar-toggler collapse-icons" data-toggle="collapse" data-target="#sub_'.$menu['id_deoblog_category'].'">
                                <i class="material-icons add">add</i>
                                <i class="material-icons remove">remove</i>
                            </div>';
                        }

                        $output .= $this->genFontEndTree($menu['id_deoblog_category'], $level + 1, $helper);
                    }
                    $output .= '</li>';
                }
            }

            $output .= '</ul>';
            return $output;
        }
        return false;
    }
}
