<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }
use Symfony\Component\Translation\TranslatorInterface;

class DeoCheckoutAddressForm extends AbstractForm
{
    private $language;

    protected $template = 'customer/_partials/address-form.tpl';

    private $address;

    private $persister;

    private $checkoutModule;

    public function __construct(
        $checkoutModule,
        Smarty $smarty,
        Language $language,
        TranslatorInterface $translator,
        DeoCheckoutCustomerAddressPersister $persister,
        DeoCheckoutAddressFormatter $formatter
    ) {
        parent::__construct(
            $smarty,
            $translator,
            $formatter
        );

        $this->language  = $language;
        $this->persister = $persister;
        $this->checkoutModule = $checkoutModule;
    }

    public function loadAddressById($id_address)
    {
        $context = Context::getContext();

        $this->address = new Address($id_address, $this->language->id);

        if ($this->address->id === null) {
            return Tools::redirect('index.php?controller=404');
        }

        if (!$context->customer->isLogged() && !$context->customer->isGuest()) {
            return Tools::redirect('/index.php?controller=authentication');
        }

        if ($this->address->id_customer != $context->customer->id) {
            return Tools::redirect('index.php?controller=404');
        }

        $params               = get_object_vars($this->address);
        $params['id_address'] = $this->address->id;

        return $this->fillWith($params);
    }

    public function fillWith(array $params = array())
    {
        // This form is very tricky: fields may change depending on which
        // country is being submitted!
        // So we first update the format if a new id_country was set.
        if (isset($params['id_country'])
            && $params['id_country'] != $this->formatter->getCountry()->id
        ) {
            $this->formatter->setCountry(new Country(
                $params['id_country'],
                $this->language->id
            ));
        }

        return parent::fillWith($params);
    }

    public function validate($finalConfirmation = false)
    {
        $is_valid = true;

        if (($postcode = $this->getField('postcode'))) {
            if ($postcode->isRequired()) {
                $country = $this->formatter->getCountry();
                if (!$country->checkZipCode($postcode->getValue())) {
                    $postcode->addError($this->translator->trans('Invalid postcode - should look like "%zipcode%"',
                        array('%zipcode%' => $country->zip_code_format),
                        'Shop.Forms.Errors'));
                    $is_valid = false;
                }
            }
        }

        if (($hookReturn = Hook::exec('actionValidateCustomerAddressForm', array('form' => $this))) !== '') {
            $is_valid &= (bool)$hookReturn;
        }

        // We need to call this separately due to side-effect - getting all errors at once, not only postcode error first
        $parentErrors = parent::validate();

        return ($is_valid && $parentErrors) || !$finalConfirmation;
    }

    public function isOpcTransientAddress($alias)
    {
        return preg_match('/^opc_\d+$/', $alias);
    }

    public function getCartIdFromAddressAlias($alias)
    {
        if ($this->isOpcTransientAddress($alias)) {
            return explode('_', $alias)[1];
        } else {
            return false;
        }
    }

    public function submit($finalConfirmation = false)
    {
        if (!$this->validate($finalConfirmation)) {
            return false;
        }

        $address = new Address(
            $this->getValue('id_address'),
            $this->language->id
        );

        $defaultAliasName = $this->translator->trans('My Address', array(), 'Shop.Theme.Checkout');

        // Address validated properly and customer pressed 'confirm order' - we can set alias and customer ID for this address
        if ($finalConfirmation && ($this->isOpcTransientAddress($address->alias) || $defaultAliasName == $address->alias)) {
            $address1_param = $this->formFields['address1']->getValue();
            $aliasBase      = (Tools::strlen($address1_param) > 2) ? $address1_param : $defaultAliasName;
            $address->alias = mb_substr(preg_replace('/[<>={}]/', '', $aliasBase), 0, 32);
        }

        foreach ($this->formFields as $formField) {
            $address->{$formField->getName()} = $formField->getValue();
        }

        if (!isset($this->formFields['id_state'])) {
            $address->id_state = 0;
        }

        if (empty($address->alias)) {
            // $address->alias = $this->translator->trans('My Address', array(), 'Shop.Theme.Checkout');
            $context        = Context::getContext();
            $address->alias = "opc_" . $context->cart->id;
        }

        $this->address = $address;

        $result = false;
        try {
            $result = $this->persister->save(
                $this->address,
                $this->getValue('token'),
                $finalConfirmation || !$this->isOpcTransientAddress($address->alias) // attach_customer_id
            );
        } catch (PrestaShopException $e) {
            //$e->displayMessage();
            // we need to create some general 'field' for any error, not caught anywhere else

            $errMessage = $e->getMessage();
            // For DNI, make a special treatment
            if (strpos($errMessage, 'Address->dni')) { 
                $errorField = $this->getField('dni');
                $errorField->addError($this->checkoutModule->getTranslation('Invalid DNI'));
            } else {
                $errorField = $this->getField('general_error');
                $errorField->addError($errMessage);
            }
        }

        return $result;
    }

    public function submitNoValidate()
    {
        $address = new Address(
            $this->getValue('id_address'),
            $this->language->id
        );

        foreach ($this->formFields as $formField) {
            $address->{$formField->getName()} = $formField->getValue();
        }

        if (!isset($this->formFields['id_state'])) {
            $address->id_state = 0;
        }

        if (empty($address->alias)) {
            $address->alias = $this->translator->trans('My Address', array(), 'Shop.Theme.Checkout');
        }

        $this->address = $address;

        return $this->persister->save(
            $this->address,
            $this->getValue('token')
        );
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getTemplateVariables()
    {
        $context = Context::getContext();

        if (!$this->formFields) {
            // This is usually done by fillWith but the form may be
            // rendered before fillWith is called.
            // I don't want to assign formFields in the constructor
            // because it accesses the DB and a constructor should not
            // have side effects.
            $this->formFields = $this->formatter->getFormat();
        }

        $this->setValue('token', $this->persister->getToken());
        $formFields = array_map(
            function (FormField $item) {
                return $item->toArray();
            },
            $this->formFields
        );

        if (empty($formFields['firstname']['value'])) {
            $formFields['firstname']['value'] = $context->customer->firstname;
        }

        if (empty($formFields['lastname']['value'])) {
            $formFields['lastname']['value'] = $context->customer->lastname;
        }

        return array(
            'id_address' => (isset($this->address->id)) ? $this->address->id : 0,
            'action'     => $this->action,
            'errors'     => $this->getErrors(),
            'formFields' => $formFields,
        );
    }
}
