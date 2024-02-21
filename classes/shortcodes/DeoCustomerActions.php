<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) { exit; }

use PrestaShop\PrestaShop\Adapter\ObjectPresenter;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class DeoCustomerActions extends DeoShortCodeBase
{
    public $name = 'DeoCustomerActions';

    public function getInfo()
    {
        return array(
            'label' => 'Customer actions',
            'position' => 5,
            'desc' => $this->l('Multiple block: Language Selector, Currency Selector, Customer Signin'),
            'image' => 'customer.png',
            'tag' => 'content',
            'config' => $this->renderDefaultConfig(),
        );
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }

    public function getConfigList()
    {
        $inputs = array(
            array(
                'type' => 'DeoClass',
                'name' => 'class',
                'label' => $this->l('CSS Class'),
                'default' => ''
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable language selector'),
                'name' => 'languageselector',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => '',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable currency selector'),
                'name' => 'currencyselector',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => '',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable customer signin'),
                'name' => 'customersignin',
                'is_bool' => true,
                'values' => DeoSetting::returnYesNo(),
                'default' => '1',
                'form_group_class' => '',
            )
        );
        return $inputs;
    }

    private static function getNameSimple($name)
    {
        return preg_replace('/\s\(.*\)$/', '', $name);
    }

    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        $currencies = array();
        $languages = array();

        //add parameters for language selector
        $languages = Language::getLanguages(true);

        if (count($languages) && $assign['formAtts']['languageselector']) {
            $current_language = null;
            foreach ($languages as $lang) {
                $lang['name_simple'] = DeoCustomerActions::getNameSimple($lang['name']);
                if ($lang['id_lang'] == Context::getContext()->language->id){
                    $current_language = $lang;
                }
            }

            // if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            //     $countries = Carrier::getDeliveredCountries(Context::getContext()->language->id, true, true);
            // } else {
            //     $countries = Country::getCountries(Context::getContext()->language->id, true);
            // }
            // $assign['countries'] = $countries;

            $assign['img_lang_url'] = _THEME_LANG_DIR_;
            // $assign['lang_iso'] = Context::getContext()->language->iso_code;
            $assign['languages'] = $languages;
            $assign['current_language'] = $current_language;

        }

        //add parameters for currency selector
        if ($assign['formAtts']['currencyselector']) {
            $current_currency = null;
            $serializer = new ObjectPresenter;
            $currencies = array_map(
                function ($currency) use ($serializer, &$current_currency) {
                    $currencyArray = $serializer->present($currency);

                    // serializer doesn't see 'sign' because it is not a regular
                    // ObjectModel field.
                    $currencyArray['sign'] = $currency->sign;

                    $url = Context::getContext()->link->getLanguageLink(Context::getContext()->language->id);

                    $extraParams = array(
                        'SubmitCurrency' => 1,
                        'id_currency' => $currency->id
                    );

                    $partialQueryString = http_build_query($extraParams);
                    $separator = empty(parse_url($url)['query']) ? '?' : '&';

                    $url .= $separator . $partialQueryString;

                    $currencyArray['url'] = $url;

                    if ($currency->id === Context::getContext()->currency->id) {
                        $currencyArray['current'] = true;
                        $current_currency = $currencyArray;
                    } else {
                        $currencyArray['current'] = false;
                    }

                    return $currencyArray;
                },
                Currency::getCurrencies(true, true)
            );
            $assign['currencies'] = $currencies;
            $assign['current_currency'] = $current_currency;
        }

        // $assign['cookie'] = Context::getContext()->cookie;
        // $assign['blockcurrencies_sign'] = Context::getContext()->currency->sign;
        // $assign['catalog_mode'] = Configuration::get('PS_CATALOG_MODE');
        
        //add parameters for user info
        if ($assign['formAtts']['customersignin']) {
            $logged = Context::getContext()->customer->isLogged();
            $assign['logged'] = $logged;

            if ($logged) {
                $customerName = Context::getContext()->customer->firstname.' '.Context::getContext()->customer->lastname;
            } else {
                $customerName = '';
            }
            $assign['customerName'] = $customerName;

            $link = Context::getContext()->link;
            $assign['logout_url'] = $link->getPageLink('index', true, null, 'mylogout');
            $assign['my_account_url'] = $link->getPageLink('my-account', true);
        }else{
            if (count($currencies) == 0 && count($languages) == 0){
                $assign['active'] = 0;
            }
        }

        return $assign;
    }
}
