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

include_once(_PS_MODULE_DIR_.'deotemplate/classes/Megamenu/DeoMegamenuGroupModel.php');
include_once(_PS_MODULE_DIR_.'deotemplate/classes/Megamenu/DeoMegamenuModel.php');

class DeoMegamenuTabs extends DeoShortCodeBase
{
    public $name = 'DeoMegamenuTabs';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Deo Megamenu Tabs', 
            'position' => 3, 
            'desc' => $this->l('Show group megamenu type Tab. You can only one Deo Megamenu Tabs'),
            'image' => 'menubar.png',
            'tag' => 'content',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {

        $id_shop = Context::getContext()->shop->id;
        $list = DeoMegamenuGroupModel::getGroups(null, $id_shop, true);
        $url = Context::getContext()->link->getAdminLink('AdminDeoMegamenu');
        if ($list && count($list) > 0) {
            $inputs = array(
                array(
                    'type' => 'DeoClass',
                    'name' => 'class',
                    'label' => $this->l('Class'),
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Step 1: Select Group Megamenu Tab').'</div>',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Group Megamenu Tab'),
                    'name' => 'megamenu_groups[]',
                    'multiple' => true,
                    'options' => array(
                        'query' => $this->getListGroup($list),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Press "Ctrl" and "Mouse Left Click" to choose many items'),
                    'class' => 'fixed-width-xxl',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-info">'.$this->l('Step 2: Select Group Megamenu Tab Active').'</div>',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Group Megamenu Tab Active'),
                    'name' => 'megamenu_group_active',
                    'options' => array(
                        'query' => $this->getListGroup($list),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'class' => 'fixed-width-xxl',
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="">'.$this->l('No exist group megamenu type Tab ').'<a class="" href="'.$url.'" target="_blank">'.$this->l('Megamenu Manager').'</a></div><br/>'.'<div class="alert alert-warning">'.$this->l('To use Megamenu in style Tab you have to add at least one group Megamenu enabled setting "Tab Style" in Homepage Builder').'</div>'
                ),
            );
        } else {
            // Go to page setting of the module
            $inputs = array(
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-warning">'.$this->l('No exist group Megamenu enabled setting "Tab Style".').'</div><br/><div><a class="btn btn-primary" href="'.$url.'" target="_blank">'.$this->l('Create first megamenu here').'</a></div>'
                )
            );
        }
       
        return $inputs;
    }

    public function getListGroup($list)
    {
        $result = array();
        foreach ($list as $item) {
            $status = ' (ID: '.$item['id_deomegamenu_group'].' - '.($item['active'] ? $this->l('Active') : $this->l('Deactive')).')';
            $result[] = array('id' => $item['randkey'], 'name' => $item['title'].$status);
        }
        return $result;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }

    public function prepareFontContent($assign, $module = null)
    {
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        $groups = array();
        $parent = '0';
        $params = array('params' => array());
        $get_params_widget = array();
        $megamenu_groups = explode(',', $assign['formAtts']['megamenu_groups']);

        foreach ($megamenu_groups as $randkey_group) {
            $sql = 'SELECT id_deomegamenu_group FROM `'._DB_PREFIX_.'deomegamenu_group`';
            $sql .= ' WHERE tab_style = 1 AND randkey = "'.$randkey_group.'" AND id_shop = ' . (int)$id_shop;
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

            // get data group menu
            $group_id = $result['id_deomegamenu_group'];
            $groups[] = $this->getMegamenuGroupById($group_id);

        }

        if (is_array($groups) && empty($groups)) {
            $assign['formAtts']['has_error'] = true;
            $assign['formAtts']['msg_error'] = $this->l('Group megamenu tabs not exist.');
        }

        $assign['groups'] = $groups;

        return $assign;
    }

    public function getMegamenuGroupById($id)
    {
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        $id_lang = Context::getContext()->language->id;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT gr.`active`, grl.`title_fo`, gr.`tab_style`, gr.`randkey`
            FROM '._DB_PREFIX_.'deomegamenu_group gr
            LEFT JOIN '._DB_PREFIX_.'deomegamenu_group_lang grl ON gr.id_deomegamenu_group = grl.id_deomegamenu_group AND grl.id_lang = '.(int)$id_lang.'
            WHERE gr.id_shop = '.(int)$id_shop.'
            AND gr.id_deomegamenu_group = '.(int)$id);
    }
}
