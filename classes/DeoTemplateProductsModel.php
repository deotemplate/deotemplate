<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoShortCodeBase.php');
require_once(_PS_MODULE_DIR_.'deotemplate/classes/DeoSetting.php');

class DeoTemplateProductsModel extends ObjectModel
{
    public $name;
    public $params;
    public $demo;
    public $active;
    public $plist_key;
    public $class;
    public $responsive;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'deotemplate_products',
        'primary' => 'id_deotemplate_products',
        'multilang' => false,
        'multishop' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'plist_key' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'demo' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'params' => array('type' => self::TYPE_HTML),
            'class' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'responsive' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
        )
    );

    public function getAllProductProfileByShop()
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $where = ' WHERE id_shop='.(int)$id_shop;
        $sql = 'SELECT p.*, ps.*
                 FROM '._DB_PREFIX_.'deotemplate_products p
                 INNER JOIN '._DB_PREFIX_.'deotemplate_products_shop ps ON (ps.id_deotemplate_products = p.id_deotemplate_products)'
                .$where;
        return Db::getInstance()->executes($sql);
    }

    public function __construct($id = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
        // validate module
        unset($context);
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = false)
    {
        $id_shop = DeoHelper::getIDShop();
        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'deotemplate_products_shop` (`id_shop`, `id_deotemplate_products`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')');
        if (Db::getInstance()->getValue('SELECT COUNT(p.`id_deotemplate_products`) AS total FROM `'
                        ._DB_PREFIX_.'deotemplate_products` p INNER JOIN `'
                        ._DB_PREFIX_.'deotemplate_products_shop` ps ON(p.id_deotemplate_products = ps.id_deotemplate_products) WHERE id_shop='
                        .(int)$id_shop) <= 1) {
            $this->deActiveAll();
        } else if ($this->active) {
            $this->deActiveAll();
        }
        return $res;
    }

    public function addShop()
    {
        $id_shop = DeoHelper::getIDShop();
        $res = Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'deotemplate_products_shop` (`id_shop`, `id_deotemplate_products`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')');
        if (Db::getInstance()->getValue('SELECT COUNT(p.`id_deotemplate_products`) AS total FROM `'
                        ._DB_PREFIX_.'deotemplate_products` p INNER JOIN `'
                        ._DB_PREFIX_.'deotemplate_products_shop` ps ON(p.id_deotemplate_products = ps.id_deotemplate_products) WHERE id_shop='
                        .(int)$id_shop) <= 1) {
            $this->deActiveAll();
        } else if ($this->active) {
            $this->deActiveAll();
        }
        return $res;
    }

    public function toggleStatus()
    {
        $this->deActiveAll();
        return true;
    }

    public function deActiveAll()
    {
        $id_shop = DeoHelper::getIDShop();
        $sql = 'UPDATE '._DB_PREFIX_.'deotemplate_products_shop SET active=0 where id_shop='.(int)$id_shop;
        Db::getInstance()->execute($sql);
        $where = ' WHERE ps.id_shop='.(int)$id_shop." AND ps.id_deotemplate_products = '".(int)$this->id."'";
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'deotemplate_products_shop` ps set ps.active = 1 '.$where);
    }

    public static function getActive()
    {
        $id_shop = (int)Context::getContext()->shop->id;
        if (Tools::getIsset('plist_key') && Tools::getValue('plist_key')) {
            // validate module
            $where = " p.plist_key='".pSQL(Tools::getValue('plist_key'))."' and ps.id_shop=".(int)$id_shop;
        } else {
            // validate module
            $where = ' ps.active=1 and ps.id_shop='.(int)$id_shop;
        }

        $sql = 'SELECT * FROM '._DB_PREFIX_.'deotemplate_products p
                INNER JOIN '._DB_PREFIX_.'deotemplate_products_shop ps on(p.id_deotemplate_products = ps.id_deotemplate_products) WHERE '.$where;
        return Db::getInstance()->getRow($sql);
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
                    (int)$this->id.' AND id_shop IN ('.pSQL(implode(', ', $id_shop_list)).')');
            }
        }
        
        return $result;
    }
}
