<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


class HelperOnepagecheckout
{
    const SEPARATE_PAYMENT_KEY_NAME = 'separate_paypment';
    const ADDRESS_TYPE_INVOICE = 'invoice';
    const ADDRESS_TYPE_DELIVERY = 'delivery';

    const enforcedSeparatePaymentModules = array('xps_checkout', 'braintreeofficial');

    public function isJsonField($fieldName)
    {
        $json_fields = array(DeoHelper::getConfigName('CUSTOMER_FIELDS'), DeoHelper::getConfigName('INVOICE_FIELDS'), DeoHelper::getConfigName('DELIVERY_FIELDS'));
        return in_array($fieldName, $json_fields);
    }


    public function getAddressObjectCustomFields() {
        $instanceProperties = array();
        if ($adr_object = new \Address()) {
            // we just need dummy code to create Address instance, although, we need to access static property
            // as some modules might add to Address::$definition in __construct method
            // And, special case - einvoice module - uses 3 public properties ei_sdi, ei_pec, ei_pa
            $instanceProperties = get_object_vars($adr_object);
        }
        $addressObjectFieldsDefinition = \Address::$definition['fields'];
        $addressObjectFieldsSystem     = array(
            'id_customer',
            'id_manufacturer',
            'id_supplier',
            'id_warehouse',
            'alias',
            'deleted',
            'date_add',
            'date_upd'
        );
        $addressObjectFieldsDefault     = array(
            'company',
            'vat_number',
            'dni',
            'firstname',
            'lastname',
            'address1',
            'address2',
            'city',
            'id_state',
            'postcode',
            'id_country',
            'phone',
            'phone_mobile',
            'other',
        );
        $addressObjectInstanceFieldsDefault = array(
            'country',
            'id',
            'id_shop_list',
            'force_id'
        );

        return array_diff(array_keys(array_merge($addressObjectFieldsDefinition, $instanceProperties)), $addressObjectFieldsSystem, $addressObjectFieldsDefault, $addressObjectInstanceFieldsDefault);
    }


    public function parseFormFields($fields_name){
        $result =  array();
        $dbVal = DeoHelper::getConfig($fields_name);
        $decodedString = json_decode($dbVal, true);
        if (!is_array($decodedString)){
            if ($fields_name === 'customer_fields') {
                $decodedString = $this->customer_fields;
            }elseif ($fields_name === 'invoice_fields' || $fields_name === 'delivery_fields') {
                $decodedString = $this->invoice_fields;
            }
            $result = $decodedString;
        }

        if (JSON_ERROR_NONE == json_last_error()) {
            // Special treatment for password field - always set by core option 'PS_GUEST_CHECKOUT_ENABLED'
            if ($fields_name === 'customer_fields') {
                $decodedString['password']['required'] = !Configuration::get('PS_GUEST_CHECKOUT_ENABLED');
            }elseif ($fields_name === 'invoice_fields' || $fields_name === 'delivery_fields') {
                // Check if Address object have any custom fields, that we could add here
                foreach ($this->getAddressObjectCustomFields() as $customFieldName) {
                    // Only set defaults, when we don't yet have it managed through config
                    if (!isset($decodedString[$customFieldName])) {
                        $decodedString[$customFieldName] = array(
                            'visible' => true,
                            'required' => false,
                            'width' => '100',
                            'live' => false
                        );
                    }
                }
            }
            $result = $decodedString;
        }

        return $result;
    }

    public function translateLastJsonError()
    {
        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                return ' - Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return ' - Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return ' - Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return ' - Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return ' - Unknown error';
        }
    }


    public $enable_onepagecheckout = 0;

    /* Customer editable options, these are only defaults, they get overwritten by BO setup */
    public $separate_cart_summary = 1; // PS 1.7 checkout has dedicated summary review step; this option=ON, will keep it
    public $primary_address = self::ADDRESS_TYPE_INVOICE; // 'invoice' or 'delivery'

    public $customer_fields = array(
        "id_gender"             => array("visible" => false, "required" => false, "width" => 12),
        "email"                 => array("visible" => true, "required" => true, "width" => 12),
        "password"              => array("visible" => true, "required" => false, "width" => 12),
        "firstname"             => array("visible" => false, "required" => true, "width" => 6),
        "lastname"              => array("visible" => false, "required" => true, "width" => 6),
        "birthday"              => array("visible" => false, "required" => false, "width" => 12),
        "optin"                 => array("visible" => false, "required" => false, "width" => 12),
        "ps_emailsubscription"  => array("visible" => true, "required" => false, "width" => 12),
        "ps_dataprivacy"        => array("visible" => true, "required" => true, "width" => 12),
        "psgdpr"                => array("visible" => true, "required" => true, "width" => 12),
    );

    public $module_customer_fields = array('ps_emailsubscription', 'ps_dataprivacy', 'psgdpr');

    public $invoice_fields = array(
        "firstname"    => array("visible" => true, "required" => true, "width" => 6, "live" => false),
        "lastname"     => array("visible" => true, "required" => true, "width" => 6, "live" => false),
        "company"      => array("visible" => true, "required" => false, "width" => 12, "live" => false),
        "dni"          => array("visible" => true, "required" => false, "width" => 12, "live" => false),
        "vat_number"   => array("visible" => true, "required" => false, "width" => 12, "live" => false),
        "address1"     => array("visible" => true, "required" => true, "width" => 12, "live" => true),
        "address2"     => array("visible" => false, "required" => false, "width" => 12, "live" => true),
        "city"         => array("visible" => true, "required" => true, "width" => 12, "live" => true),
        "State:name"   => array("visible" => true, "required" => true, "width" => 12, "live" => true),
        "postcode"     => array("visible" => true, "required" => false, "width" => 12, "live" => true),
        "Country:name" => array("visible" => true, "required" => true, "width" => 12, "live" => true),
        "phone"        => array("visible" => true, "required" => true, "width" => 12, "live" => false),
        "phone_mobile" => array("visible" => false, "required" => false, "width" => 12, "live" => false),
        "other"        => array("visible" => false, "required" => false, "width" => 12, "live" => false)
    );

    public $delivery_fields = array(
        "firstname"    => array("visible" => true, "required" => true, "width" => 6, "live" => false),
        "lastname"     => array("visible" => true, "required" => true, "width" => 6, "live" => false),
        "company"      => array("visible" => false, "required" => false, "width" => 12, "live" => false),
        "dni"          => array("visible" => false, "required" => false, "width" => 12, "live" => false),
        "vat_number"   => array("visible" => false, "required" => false, "width" => 12, "live" => false),
        "address1"     => array("visible" => true, "required" => true, "width" => 12, "live" => true),
        "address2"     => array("visible" => false, "required" => false, "width" => 12, "live" => true),
        "city"         => array("visible" => true, "required" => true, "width" => 12, "live" => true),
        "State:name"   => array("visible" => true, "required" => true, "width" => 12, "live" => true),
        "postcode"     => array("visible" => true, "required" => false, "width" => 12, "live" => true),
        "Country:name" => array("visible" => true, "required" => true, "width" => 12, "live" => true),
        "phone"        => array("visible" => true, "required" => true, "width" => 12, "live" => false),
        "phone_mobile" => array("visible" => false, "required" => false, "width" => 12, "live" => false),
        "other"        => array("visible" => false, "required" => false, "width" => 12, "live" => false)
    );


    public $expand_second_address = 0;
    public $offer_second_address = 1;

    public $default_payment_method = 'ps_wirepayment';

    public $mark_required_fields = 1;

    public $clean_checkout_session_after_confirmation = 0;

    public $show_block_reassurance = 0;

    public $show_order_message = 0;
    public $postcode_remove_spaces = 0;
    public $separate_payment = 0;

    public $show_i_am_business = 1;
    public $show_i_am_private = 0;
    public $create_account_checkbox = 1;
    public $business_fields = 'company, dni, vat_number';
    public $private_fields = 'dni';
    public $business_disabled_fields = '';
    public $shipping_required_fields = '';
    public $payment_required_fields = '';
    public $show_shipping_country_in_carriers = 0;
    public $blocks_update_loader = '';
    public $show_product_stock_info = 0;
    public $newsletter_checked = 0;
    public $allow_guest_checkout_for_registered = 1;
    public $show_call_prefix = 0;
    public $initialize_address = 0;

    public $required_checkbox_1 = '';
    public $required_checkbox_2 = '';

    /* Not currently used options */
    public $require_email_first = 1; // Disallow address changes, if email is not entered; useful for Abandoned cart reminders
    public $in_field_labels = 1;
}
