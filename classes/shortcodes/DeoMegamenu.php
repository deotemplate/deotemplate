<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }

include_once(_PS_MODULE_DIR_.'deotemplate/classes/Megamenu/DeoMegamenuGroupModel.php');
include_once(_PS_MODULE_DIR_.'deotemplate/classes/Megamenu/DeoMegamenuModel.php');

class DeoMegamenu extends DeoShortCodeBase
{
    public $name = 'DeoMegamenu';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array(
            'label' => 'Megamenu', 
            'position' => 3, 
            'desc' => $this->l('Show group megamenu'),
            'image' => 'menu.png',
            'tag' => 'content',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function getConfigList()
    {

        $list = DeoMegamenuGroupModel::getGroups(null);
        $url = Context::getContext()->link->getAdminLink('AdminDeoMegamenu');
        if ($list && count($list) > 0) {
            $inputs = array(
                array(
                    'type' => 'DeoClass',
                    'name' => 'class',
                    'label' => $this->l('Class'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Select Group Megamenu'),
                    'name' => 'megamenu_group',
                    'options' => array(
                        'query' => $this->getListGroup($list),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'class' => 'fixed-width-xxl',
                    'form_group_class' => 'value_by_categories',
                    'default' => 'all'
                ),
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class=""><a class="" href="'.$url.'" target="_blank">'.
                    $this->l('Megamenu Manager').'</a></div>'
                )
            );
        } else {
            // Go to page setting of the module
            $inputs = array(
                array(
                    'type' => 'html',
                    'name' => 'default_html',
                    'html_content' => '<div class="alert alert-warning">'.
                    $this->l('There is no group exist.').
                    '</div><br/><div><center><a class="btn btn-primary" href="'.$url.'" target="_blank">'.
                    $this->l('Create first megamenu here').'</a></center></div>'
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
        $result = array('success' => false);
        $parent = '0';
        $params = array('params' => array());
        $get_params_widget = array();
        $randkey_group = $assign['formAtts']['megamenu_group'];

        $where = ' WHERE randkey = "'.$randkey_group.'" AND id_shop = ' . (int)$id_shop;
        $sql = 'SELECT id_deomegamenu_group FROM `'._DB_PREFIX_.'deomegamenu_group` '.$where;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        if (is_array($result) && empty($result)) {
            $assign['formAtts']['has_error'] = true;
            $assign['formAtts']['msg_error'] = $this->l('Group megamenu not exist.');
        }

        // get data group menu
        $group_id = $result['id_deomegamenu_group'];
        $group = $this->getMegamenuGroupById($group_id);
        if (isset($group['tab_style']) && $group['tab_style']){
            $assign['group'] = $group;
        }

        if ($group['active'] != 1) {
            $assign['formAtts']['has_error'] = true;
            $assign['formAtts']['msg_error'] = $this->l('Group megamenu not active, please active it to display megamenu');
        }

        $group_params = array(
            'id_group' => $group_id,
            'params' => $group['params']
        );
        
        $list_root_menu = DeoMegamenuModel::getMegamenuRoot($group_id);

        // get array id menu => param widget
        if (count($list_root_menu) > 0) {
            foreach ($list_root_menu as $list_root_menu_item) {
                if ($list_root_menu_item['params_widget'] != '') {
                    $get_params_widget[$list_root_menu_item['id_deomegamenu']] = json_decode(DeoMegamenuHelper::base64Decode($list_root_menu_item['params_widget']));
                }
            }
        }

        $params['params'] = $get_params_widget;
        $obj = new DeoMegamenuModel($parent);
        $obj->setModule($this);
        
        $megamenu = $obj->getFrontTree(0, false, $params['params'], $group_params);
        $params_group = json_decode(DeoMegamenuHelper::base64Decode($group['params']), true);
        $show_cavas = $params_group['show_cavas'];
        $type_sub = $params_group['type_sub'];
        $group_type = $params_group['group_type'];
        $group_class = $params_group['group_class'];

        $this->context->smarty->assign('megamenu', $megamenu);
        $this->context->smarty->assign('show_cavas', $show_cavas);
        $this->context->smarty->assign('type_sub', $type_sub);
        $this->context->smarty->assign('group_type', $group_type);
        $this->context->smarty->assign('group_class', $group_class);
        $this->context->smarty->assign('group_title', $group['title_fo']);
        $this->context->smarty->assign('megamenu_id', $assign['formAtts']['form_id']);
        $assign['content_megamenu'] = $this->context->smarty->fetch('module:deotemplate/views/templates/hook/megamenu/megamenu.tpl');

        return $assign;
    }

    public function getMegamenuGroupById($id)
    {
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;
        $id_lang = Context::getContext()->language->id;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT gr.`params` , gr.`active`, grl.`title_fo`, gr.`tab_style`, gr.`randkey`
            FROM '._DB_PREFIX_.'deomegamenu_group gr
            LEFT JOIN '._DB_PREFIX_.'deomegamenu_group_lang grl ON gr.id_deomegamenu_group = grl.id_deomegamenu_group AND grl.id_lang = '.(int)$id_lang.'
            WHERE gr.id_shop = '.(int)$id_shop.'
            AND gr.id_deomegamenu_group = '.(int)$id);
    }
}
