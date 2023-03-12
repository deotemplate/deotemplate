<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */


class DeoCheckoutCustomerAddressPersister
{
    private $customer;
    private $token;
    private $cart;

    public function __construct(Customer $customer, Cart $cart, $token)
    {
        $this->customer = $customer;
        $this->cart     = $cart;
        $this->token    = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    private function authorizeChange(Address $address, $token)
    {
        if ($address->id_customer && (int)$address->id_customer !== (int)$this->customer->id) {
            // Can't touch anybody else's address
            return false;
        }

        if ($token !== $this->token) {
            // XSS?
            return false;
        }

        return true;
    }

    public function areAddressesDifferent($address1, $address2)
    {
        // compare following fields:
        $compareFields = array(
            'id_customer',
            'id_country',
            'id_state',
            'country',
            'company',
            'lastname',
            'firstname',
            'address1',
            'address2',
            'postcode',
            'city',
            'other',
            'phone',
            'phone_mobile',
            'vat_number',
            'dni'
        );
        foreach ($compareFields as $field) {
            if ($address1->{$field} != $address2->{$field}) {
                return true;
            }
        }
        return false;
    }

    public function save(Address $address, $token, $attachCustomerId = true)
    {
        if (!$this->authorizeChange($address, $token)) {
            return false;
        }

        if ($attachCustomerId) {
            $address->id_customer = $this->customer->id;
        }

        if ($address->id && $address->isUsed()) {
            $old_address = new Address($address->id);
            if ($this->areAddressesDifferent($old_address, $address)) {
                $address->id = $address->id_address = null;

                try {
                    $old_address->delete();
                } catch (Exception $e) {
                    // Special treatment for 'dni' field - if it's not set in old (to be deleted) address ,BUT, required now on country level
                    if (strpos($e->getMessage(), 'dni')) {
                        // Retry deletion with DNI set from new address
                        $old_address->dni = $address->dni;
                        $old_address->delete();
                    }
                }

                return $address->save();
            }
        }

        return $address->save();
    }

    public function delete(Address $address, $token)
    {
        if (!$this->authorizeChange($address, $token)) {
            return false;
        }

        $id = $address->id;
        $ok = $address->delete();

        if ($ok) {
            if ($this->cart->id_address_invoice == $id) {
                unset($this->cart->id_address_invoice);
            }
            if ($this->cart->id_address_delivery == $id) {
                unset($this->cart->id_address_delivery);
                $this->cart->updateAddressId(
                    $id,
                    Address::getFirstCustomerAddressId($this->customer->id)
                );
            }
        }

        return $ok;
    }
}
