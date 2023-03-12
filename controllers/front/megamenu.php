<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


class DeoTemplateMegamenuModuleFrontController extends ModuleFrontController
{
    public $php_self;

    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();
    }

    public function displayAjax()
    {
        if (Tools::getValue('getListWidgets')) {
            die(json_encode($this->loadwidget(Tools::getValue('backoffice'))));
        }
    }

    public function loadwidget($backoffice = 1)
    {
        $result = array('success' => false);
        $model = new DeoWidgetModel();

        $result['data'] = $model->loadWidgetsData($backoffice);
        $result['success'] = true;
        
        die(json_encode($result));
    }

    public function GetWidget()
    {
        if (Tools::getIsset('allWidgets')){
            $dataForm = json_decode( Tools::getValue('dataForm'), 1);
            //print_r($dataForm);die;
            foreach ($dataForm as &$widget) {
                // print_r($this->renderwidget($widget['id_shop'], $widget['id_widget']));die;
                $widget['html'] = $this->renderwidget($widget['id_shop'], $widget['id_widget']);
            }
            // $output['dataForm'] = $dataForm;
            // $output['param_widgets_menu'] = $this->param_widgets_menu;
            
            die(json_encode($dataForm));
        }
    }

    public function renderwidget($id_shop, $widgets)
    {
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }
        // $widgets = Tools::getValue('widgets');
        $widgets = explode('|', $widgets);

        $this->context->smarty->assign(array(
            'link' => $this->context->link,
            // 'PS_CATALOG_MODE' => Configuration::get('PS_CATALOG_MODE'),
            // 'priceDisplay' => Product::getTaxCalculationMethod((int) $this->context->cookie->id_customer),
        ));
        if (!empty($widgets)) {
            $output = '';
            $model = new DeoWidgetModel();
            $model->setTheme(Context::getContext()->shop->theme->getName());
            $model->langID = $this->context->language->id;
            $model->loadWidgets($id_shop);
            $model->loadEngines();
            
            foreach ($widgets as $wid) {
                $content = $model->renderContent($wid);
                $html = $this->getWidgetContent($wid, $content['type'], $content['data']);
                $output .= $html;
                // if (!isset($this->param_widgets_menu[$wid])) {
                //     $content['html'] = $html;
                //     $this->param_widgets_menu[$wid] = $content;
                // } 
            }

            return $output;
        }
        return '';
    }
}
