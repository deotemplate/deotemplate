<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */

// vim: foldmethod=marker

/* Generic exception class
 */
// use library from prestashop

if (!class_exists('OAuthException')) {

    class OAuthException extends Exception
    {
        // pass
    }
}

require_once(dirname(__FILE__) . '/OAuthConsumer.php');
require_once(dirname(__FILE__) . '/OAuthToken.php');
require_once(dirname(__FILE__) . '/OAuthSignatureMethod.php');
require_once(dirname(__FILE__) . '/OAuthSignatureMethodHMACSHA1.php');
require_once(dirname(__FILE__) . '/OAuthSignatureMethodPLAINTEXT.php');
require_once(dirname(__FILE__) . '/OAuthSignatureMethodRSASHA1.php');
require_once(dirname(__FILE__) . '/OAuthRequest.php');
require_once(dirname(__FILE__) . '/OAuthServer.php');
require_once(dirname(__FILE__) . '/OAuthDataStore.php');
require_once(dirname(__FILE__) . '/OAuthUtil.php');
