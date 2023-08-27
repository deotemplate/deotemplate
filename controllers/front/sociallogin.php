<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

include_once(_PS_MODULE_DIR_ . 'deotemplate/classes/SocialLogin/TwitterOAuth.php');
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
//use PrestaShop\PrestaShop\Core\Crypto\Hashing as Crypto;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\DeleteCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\DeleteCustomerHandlerInterface;

class DeoTemplateSocialLoginModuleFrontController extends ModuleFrontController
{

    public $php_self;

    public function displayAjax()
    {
        // Add or remove product with Ajax
        $context = Context::getContext();
        $action = Tools::getValue('action');

        $array_result = array();
        $errors = array();
        $success = array();
        // Instance of module class for translations
        // reset password
        if ($action == 'reset-password') {
            // check validate
            if (!($email = trim(Tools::getValue('email-reset'))) || !Validate::isEmail($email)) {
                $errors[] = $this->module->l('Invalid email address', 'sociallogin');
            } else {
                // check email exist
                $customer = new Customer();
                $customer->getByEmail($email);
                if (is_null($customer->email)) {
                    $customer->email = $email;
                }

                if (!Validate::isLoadedObject($customer)) {
                    $errors[] = $this->module->l('This email is not registered as an account', 'sociallogin');
                } elseif (!$customer->active) {
                    $errors[] = $this->module->l('You cannot regenerate the password for this account.', 'sociallogin');
                } elseif ((strtotime($customer->last_passwd_gen . '+' . ($minTime = (int) Configuration::get('PS_PASSWD_TIME_FRONT')) . $this->module->l(' minute(s)', 'sociallogin')) - time()) > 0) {
                    $errors[] = $this->module->l('You can regenerate your password only every ', 'sociallogin') . (int) $minTime . $this->module->l(' minute(s)', 'sociallogin');
                } else {
                    if (!$customer->hasRecentResetPasswordToken()) {
                        $customer->stampResetPasswordToken();
                        $customer->update();
                    }

                    // send mail to reset password
                    $mailParams = array(
                        '{email}' => $customer->email,
                        '{lastname}' => $customer->lastname,
                        '{firstname}' => $customer->firstname,
                        '{url}' => $this->context->link->getPageLink('password', true, null, 'token=' . $customer->secure_key . '&id_customer=' . (int) $customer->id . '&reset_token=' . $customer->reset_password_token),
                    );

                    if (Mail::Send($this->context->language->id, 'password_query', $this->module->l('Password query confirmation', 'sociallogin'), $mailParams, $customer->email, $customer->firstname . ' ' . $customer->lastname)) {
                        $success[] = $this->module->l('If this email address has been registered in our shop, you will receive a link to reset your password', 'sociallogin');
                    } else {
                        $errors[] = $this->module->l('An error occurred while sending the email.', 'sociallogin');
                    }
                }
            }
        }

        // customer login
        if ($action == 'customer-login') {
            // check validate
            if (!($email = trim(Tools::getValue('email_login'))) || !Validate::isEmail($email)) {
                $errors[] = $this->module->l('Invalid email address', 'sociallogin');
            }
            if (!($pass = trim(Tools::getValue('password_login')))) {
                $errors[] = $this->module->l('Invalid password', 'sociallogin');
            }
            if (!Tools::getValue('keep_login') && (int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE_CHECK_COOKIE') && (int) DeoHelper::getConfig('SOCIAL_LOGIN_LIFETIME_COOKIE') != 0) {
                $this->context->cookie->customer_last_activity = time();
            }
            if (!count($errors)) {
                Hook::exec('actionAuthenticationBefore');

                // check email exist
                $customer = new Customer();
                $authentication = $customer->getByEmail($email, $pass);

                if (isset($authentication->active) && !$authentication->active) {
                    $errors[] = $this->module->l('Your account not available at this time, please contact us', 'sociallogin');
                } elseif (!$authentication || !$customer->id || $customer->is_guest) {
                    $errors[] = $this->module->l('Your account not correct. Please check again.', 'sociallogin');
                } else {
                    // update cookie to login
                    $this->context->updateCustomer($customer);

                    Hook::exec('actionAuthentication', array('customer' => $this->context->customer));

                    // Login information have changed, so we check if the cart rules still apply
                    CartRule::autoRemoveFromCart($this->context);
                    CartRule::autoAddToCart($this->context);
                    $success[] = $this->module->l('You have successfully logged in', 'sociallogin');
                }
            }
        }

        // create new account
        if ($action == 'create-account') {
            // check validate
            if (!($email = trim(Tools::getValue('register-email'))) || !Validate::isEmail($email)) {
                $errors[] = $this->module->l('Invalid email address', 'sociallogin');
            }

            if (!($pass = trim(Tools::getValue('register-password'))) || (!Validate::isAcceptablePasswordLength($pass) || !Validate::isAcceptablePasswordScore($pass))) {
                $errors[] = $this->module->l('Invalid password', 'sociallogin');
            }

            if (!($repeat_pass = trim(Tools::getValue('repeat-register-password'))) ||  (!Validate::isAcceptablePasswordLength($repeat_pass) || !Validate::isAcceptablePasswordScore($repeat_pass))) {
                $errors[] = $this->module->l('Invalid repeat password', 'sociallogin');
            }

            if (!($firstname = trim(Tools::getValue('firstname'))) || !Validate::isName($firstname)) {
                $errors[] = $this->module->l('Invalid first name. Only allow character is string from a - z', 'sociallogin');
            }

            if (!($lastname = trim(Tools::getValue('lastname'))) || !Validate::isName($lastname)) {
                $errors[] = $this->module->l('Invalid last name. Only allow character is string from a - z', 'sociallogin');
            }

            if (trim(Tools::getValue('check_terms')) == 0 && (int) DeoHelper::getConfig('SOCIAL_LOGIN_ENABLE_CHECK_TERMS') == 1) {
                $errors[] = $this->module->l('Please read terms and condition and agree with our terms and condition', 'sociallogin');
            }

            if (trim(Tools::getValue('repeat-register-password')) != trim(Tools::getValue('register-password'))){
                $errors[] = $this->module->l('Repeat password is not same with password', 'sociallogin');
            }

            if (!count($errors)) {
                // check email exist to register
                if (Customer::customerExists($email, true, true)) {
                    $errors[] = $this->module->l('This email is already used, please choose another one or sign in', 'sociallogin');
                } else {
                    $hookResult = array_reduce(
                        Hook::exec('actionSubmitAccountBefore', array(), null, true),
                        function ($carry, $item) {
                            return $carry && $item;
                        },
                        true
                    );
                    // add new account
                    $customer = new Customer();
                    $customer->firstname = $firstname;
                    $customer->lastname = $lastname;
                    $customer->email = $email;
                    $customer->passwd = $this->get('hashing')->hash($pass, _COOKIE_KEY_);

                    if ($hookResult && $customer->save()) {
                        $this->context->updateCustomer($customer);
                        $this->context->cart->update();
                        // send mail new account
                        if (!$customer->is_guest && Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
                            Mail::Send($this->context->language->id, 'account_social', $this->module->l('Welcome!', 'sociallogin'), array(
                                '{firstname}' => $customer->firstname,
                                '{lastname}' => $customer->lastname,
                                '{email}' => $customer->email), $customer->email, $customer->firstname . ' ' . $customer->lastname, null, null, null, null, _PS_MODULE_DIR_ . $this->module->name . '/mails/');
                        }

                        Hook::exec('actionCustomerAccountAdd', array(
                            'newCustomer' => $customer,
                        ));
                        $success[] = $this->module->l('You have successfully created a new account', 'sociallogin');
                    } else {
                        $errors[] = $this->module->l('An error occurred while creating the new account.', 'sociallogin');
                    }
                }
            }
        }

        // create new account
        if ($action == 'social-login') {
            if (!($email = trim(Tools::getValue('email'))) || !Validate::isEmail($email)) {
                $errors[] = $this->module->l('Invalid email address', 'sociallogin');
            }

            if (!($firstname = trim(Tools::getValue('first_name'))) || !Validate::isName($firstname)) {
                $errors[] = $this->module->l('Invalid first name', 'sociallogin');
            }
            if (!($lastname = trim(Tools::getValue('last_name'))) || !Validate::isName($lastname)) {
                $errors[] = $this->module->l('Invalid last name', 'sociallogin');
            }

            if (!count($errors)) {
                if (Customer::customerExists($email, true, true)) {
                    Hook::exec('actionAuthenticationBefore');

                    // check email exist
                    $customer = new Customer();
                    $authentication = $customer->getByEmail(Tools::getValue('email'), null, true);

                    if (isset($authentication->active) && !$authentication->active) {
                        $errors[] = $this->module->l('Your account isn\'t available at this time, please contact us', 'sociallogin');
                    } elseif (!$authentication || !$customer->id || $customer->is_guest) {
                        $errors[] = $this->module->l('Authentication failed.', 'sociallogin');
                    } else {
                        // update cookie to login
                        $this->context->updateCustomer($customer);

                        Hook::exec('actionAuthentication', array('customer' => $this->context->customer));

                        // Login information have changed, so we check if the cart rules still apply
                        CartRule::autoRemoveFromCart($this->context);
                        CartRule::autoAddToCart($this->context);
                        $success[] = $this->module->l('You have successfully logged in', 'sociallogin');
                    }
                } else {
                    $hookResult = array_reduce(
                        Hook::exec('actionSubmitAccountBefore', array(), null, true),
                        function ($carry, $item) {
                            return $carry && $item;
                        },
                        true
                    );
                    // add new account
                    $customer = new Customer();
                    $customer->firstname = $firstname;
                    $customer->lastname = $lastname;
                    $customer->email = $email;
                    $password = Tools::passwdGen();
                    $customer->passwd = $this->get('hashing')->hash($password, _COOKIE_KEY_);

                    if ($hookResult && $customer->save()) {
                        $this->context->updateCustomer($customer);
                        $this->context->cart->update();
                        // send mail new account
                        if (!$customer->is_guest && Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
                            Mail::Send($this->context->language->id, 'account_social', $this->module->l('Welcome!', 'sociallogin'), array(
                                '{firstname}' => $customer->firstname,
                                '{lastname}' => $customer->lastname,
                                '{email}' => $customer->email,
                                '{password}' => $password), $customer->email, $customer->firstname . ' ' . $customer->lastname, null, null, null, null, _PS_MODULE_DIR_ . $this->module->name . '/mails/');
                        }

                        Hook::exec('actionCustomerAccountAdd', array('newCustomer' => $customer));
                        $success[] = $this->module->l('You have successfully created a new account', 'sociallogin');
                    } else {
                        $errors[] = $this->module->l('An error occurred while creating the new account.', 'sociallogin');
                    }
                }
            }
        }

        if ($action == 'delete-account') {
            $result = array(
                'success' => false,
                'message' => $this->module->l('Delete Error!', 'sociallogin')
            );
            if (!$this->context->customer->isLogged()) {
                $result['message'] = $this->module->l('You are not loggin!', 'sociallogin');
            }else{
                // deny_registration_after or allow_registration_after
                $ALLOW_CUSTOMER_REGISTRATION = 'allow_registration_after';
                $customerId = $this->context->customer->id;
                $customer = new Customer($customerId);

                $command = new DeleteCustomerCommand($customerId , $ALLOW_CUSTOMER_REGISTRATION);
                if ($command->getDeleteMethod()->isAllowedToRegisterAfterDelete()) {
                    $customer->delete();
                    $result['success'] = true;
                    $result['message'] = $this->module->l('Delete Account Successful! All account informations has been deleted in our store. You will redirect to home page after 2 seconds', 'sociallogin');
                }
            }

            die(json_encode($result));
        }


        define('CONSUMER_KEY', DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_APIKEY')); // YOUR CONSUMER KEY
        define('CONSUMER_SECRET', DeoHelper::getConfig('SOCIAL_LOGIN_TWITTER_APISECRET')); //YOUR CONSUMER SECRET KEY
        define('OAUTH_CALLBACK', urlencode($this->context->link->getModuleLink('deotemplate', 'sociallogin')));  // Redirect URL
        if (Tools::getValue('request')) {
            //Fresh authentication

            $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
            $request_token = $connection->getRequestToken(OAUTH_CALLBACK);

            //Received token info from twitter
            // $_SESSION['token']             = $request_token['oauth_token'];
            // $_SESSION['token_secret']     = $request_token['oauth_token_secret'];
            $context->cookie->twitter_token = $request_token['oauth_token'];
            $context->cookie->twitter_token_secret = $request_token['oauth_token_secret'];

            //Any value other than 200 is failure, so continue only if http code is 200
            if ($connection->http_code == '200') {
                //redirect user to twitter
                $twitter_url = $connection->getAuthorizeURL($request_token['oauth_token'], true, Tools::getValue('lang'));
                Tools::redirect($twitter_url);
            } else {
                die("Error connecting to twitter! Please try again later!");
            }
        }

        if (Tools::getValue('oauth_token') && Tools::getValue('oauth_token') == $context->cookie->twitter_token) {
            $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $context->cookie->twitter_token, $context->cookie->twitter_token_secret);
            // $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
            // echo Tools::getValue('oauth_verifier');
            $access_token = $connection->getAccessToken(Tools::getValue('oauth_verifier'));
            if ($connection->http_code == '200') {
                $user_data = $connection->get('account/verify_credentials', array('include_entities' => 'true', 'skip_status' => 'true', 'include_email' => 'true'));
                // print_r($access_token);
                // print_r($user_data);
                // die();
                $name = explode(" ", $user_data['name']);
                $first_name = '';
                $last_name = '';
                $email = '';
                if (isset($name[0])) {
                    $first_name = $name[0];
                }
                if (isset($name[1])) {
                    $last_name = $name[1];
                }
                if (isset($user_data['email'])) {
                    $email = $user_data['email'];
                }

                $twitter_callback = $this->buildTwitterLoginCallBack($first_name, $last_name, $email);
                die($twitter_callback);
            } else {
                die("Error, Please try again later!");
            }
        }




        $array_result['success'] = $success;
        $array_result['errors'] = $errors;
        die(json_encode($array_result));
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->ajax = true;
        $this->php_self = 'social-login';
        if (Tools::getValue('ajax')) {
            return;
        }

        parent::initContent();
    }

    // build html for slidebar type
    public function buildTwitterLoginCallBack($firstname, $lastname, $email)
    {
        $this->context->smarty->assign(array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
        ));
        $output = $this->module->fetch('module:deotemplate/views/templates/front/social-login/twitter_callback.tpl');

        return $output;
    }
}
