<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

use Symfony\Component\Translation\TranslatorInterface;

class DeoCheckoutCustomerFormatter implements FormFormatterInterface
{
    private $translator;
    private $language;

    private $ask_for_birthdate = true;
    private $ask_for_partner_optin = true;
    private $partner_optin_is_required = false;
    private $ask_for_password = true;
    private $password_is_required = false;
    private $ask_for_new_password = false;
    private $id_gender_required = false;

    private $birthday_required = false;

    public function __construct(
        TranslatorInterface $translator,
        Language $language
    ) {
        $this->translator = $translator;
        $this->language   = $language;
    }

    public function setBirthdayRequired($birthday_required)
    {
        $this->birthday_required = $birthday_required;
        return $this;
    }

    public function setAskForBirthdate($ask_for_birthdate)
    {
        $this->ask_for_birthdate = $ask_for_birthdate;
        return $this;
    }

    public function setAskForPartnerOptin($ask_for_partner_optin)
    {
        $this->ask_for_partner_optin = $ask_for_partner_optin;
        return $this;
    }

    public function setPartnerOptinRequired($partner_optin_is_required)
    {
        $this->partner_optin_is_required = $partner_optin_is_required;
        return $this;
    }

    public function setAskForPassword($ask_for_password)
    {
        $this->ask_for_password = $ask_for_password;
        return $this;
    }

    public function setAskForNewPassword($ask_for_new_password)
    {
        $this->ask_for_new_password = $ask_for_new_password;
        return $this;
    }

    public function setPasswordRequired($password_is_required)
    {
        $this->password_is_required = $password_is_required;
        return $this;
    }

    public function setIdGenderRequired($id_gender_required)
    {
        $this->id_gender_required = $id_gender_required;
        return $this;
    }

    public function getFormat()
    {
        $format = array();

        $format['id_customer'] = (new FormField)
            ->setName('id_customer')
            ->setType('hidden');

        $genderField = (new FormField)
            ->setName('id_gender')
            ->setType('radio-buttons')
            ->setLabel(
                $this->translator->trans(
                    'Social title', array(), 'Shop.Forms.Labels'
                )
            )
            ->setRequired($this->id_gender_required);
        foreach (Gender::getGenders($this->language->id) as $gender) {
            $genderField->addAvailableValue($gender->id, $gender->name);
        }
        $format[$genderField->getName()] = $genderField;

        $format['firstname'] = (new FormField)
            ->setName('firstname')
            ->setLabel(
                $this->translator->trans(
                    'First name', array(), 'Shop.Forms.Labels'
                )
            )
            ->setRequired(true);

        $format['lastname'] = (new FormField)
            ->setName('lastname')
            ->setLabel(
                $this->translator->trans(
                    'Last name', array(), 'Shop.Forms.Labels'
                )
            )
            ->setRequired(true);

        // if (Configuration::get('PS_B2B_ENABLE')) {
        $format['company'] = (new FormField)
            ->setName('company')
            ->setType('text')
            ->setLabel($this->translator->trans(
                'Company', array(), 'Shop.Forms.Labels'
            ));
        $format['siret']   = (new FormField)
            ->setName('siret')
            ->setType('text')
            ->setLabel($this->translator->trans(
            // Please localize this string with the applicable registration number type in your country. For example : "SIRET" in France and "Código fiscal" in Spain.
                'Identification number', array(), 'Shop.Forms.Labels'
            ));
        // }

        $format['email'] = (new FormField)
            ->setName('email')
            ->setType('email')
            ->setLabel(
                $this->translator->trans(
                    'Email', array(), 'Shop.Forms.Labels'
                )
            )
            ->setRequired(true);

        if ($this->ask_for_password) {
            $format['password'] = (new FormField)
                ->setName('password')
                ->setType('password')
                ->setLabel(
                    $this->translator->trans(
                        'Password', array(), 'Shop.Forms.Labels'
                    )
                )
                ->setRequired($this->password_is_required)
                ->addConstraint('isPlaintextPassword');
        }

        if ($this->ask_for_new_password) {
            $format['new_password'] = (new FormField)
                ->setName('new_password')
                ->setType('password')
                ->setLabel(
                    $this->translator->trans(
                        'New password', array(), 'Shop.Forms.Labels'
                    )
                );
        }

        if ($this->ask_for_birthdate) {
            $format['birthday'] = (new FormField)
                ->setName('birthday')
                ->setType('text')
                ->setLabel(
                    $this->translator->trans(
                        'Birthdate', array(), 'Shop.Forms.Labels'
                    )
                )
                ->setRequired($this->birthday_required)
                ->addAvailableValue('placeholder', Tools::getDateFormat())
                ->addAvailableValue(
                    'comment',
                    $this->translator->trans('(E.g.: %date_format%)',
                        array('%date_format%' => Tools::formatDateStr('31 May 1970')), 'Shop.Forms.Help')
                );
        }

        if ($this->ask_for_partner_optin) {
            $format['optin'] = (new FormField)
                ->setName('optin')
                ->setType('checkbox')
                ->setLabel(
                    $this->translator->trans(
                        'Receive offers from our partners', array(), 'Shop.Theme.Customeraccount'
                    )
                )
                ->setRequired($this->partner_optin_is_required);
        }

        $additionalCustomerFormFields = Hook::exec(
            'additionalCustomerFormFields',
            array('get-deo-required-checkboxes' => 1),
            null,
            true
        );

        if (is_array($additionalCustomerFormFields)) {
            foreach ($additionalCustomerFormFields as $moduleName => $additionnalFormFields) {
                if (!is_array($additionnalFormFields)) {
                    continue;
                }

                foreach ($additionnalFormFields as $formField) {
                    $formField->moduleName = $moduleName;
                    // For logged in customer, make 'account' fields non-required
                    $thisContext           = Context::getContext();
                    if (isset($thisContext)
                        && isset($thisContext->customer)
                        && $thisContext->customer->isLogged()
                        && !in_array($moduleName, array("deotemplate"))) {
                        $formField->setRequired(false);
                    }
                    $format[$moduleName . '_' . $formField->getName()] = $formField;
                }
            }
        }

        return $this->addConstraints($format);
    }

    private function addConstraints(array $format)
    {
        $constraints = Customer::$definition['fields'];

        foreach ($format as $field) {
            if (!empty($constraints[$field->getName()]['validate'])) {
                $field->addConstraint(
                    $constraints[$field->getName()]['validate']
                );
            }
        }

        return $format;
    }
}
