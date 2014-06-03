<?php
require_once 'modules/admin/models/GatewayPlugin.php';

/**
* @package Plugins
*/
class PluginEgold extends GatewayPlugin
{
    function getVariables()
    {
        /* Specification
              itemkey     - used to identify variable in your other functions
              type        - text,textarea,yesno,password
              description - description of the variable, displayed in ClientExec
        */

        $variables = array (
                   lang("Plugin Name") => array (
                                        "type"          =>"hidden",
                                        "description"   =>lang("How CE sees this plugin (not to be confused with the Signup Name)"),
                                        "value"         =>lang("e-gold")
                                       ),
                   lang("User ID") => array (
                                        "type"          =>"text",
                                        "description"   =>lang("ID used to identify you to E-Gold.<br>NOTE: This ID is required if you have selected E-Gold as a payment gateway for any of your clients."),
                                        "value"         =>""
                                       ),
                   lang("Alternate Passphrase") => array (
                                        "type"          =>"text",
                                        "description"   =>lang("Password used to verify valid transactions from E-Gold Callbacks.<br>NOTE: This password has to match the value set in the E-Gold Account Information."),
                                        "value"         =>""
                                       ),
                   lang("Visa") => array (
                                        "type"          =>"yesno",
                                        "description"   =>lang("Select YES to allow Visa card acceptance with this plugin.  No will prevent this card type."),
                                        "value"         =>"0"
                                       ),
                   lang("MasterCard") => array (
                                        "type"          =>"yesno",
                                        "description"   =>lang("Select YES to allow MasterCard acceptance with this plugin. No will prevent this card type."),
                                        "value"         =>"0"
                                       ),
                   lang("AmericanExpress") => array (
                                        "type"          =>"yesno",
                                        "description"   =>lang("Select YES to allow American Express card acceptance with this plugin. No will prevent this card type."),
                                        "value"         =>"0"
                                       ),
                   lang("Discover") => array (
                                        "type"          =>"yesno",
                                        "description"   =>lang("Select YES to allow Discover card acceptance with this plugin. No will prevent this card type."),
                                        "value"         =>"0"
                                       ),
                   lang("Invoice After Signup") => array (
                                        "type"          =>"yesno",
                                        "description"   =>lang("Select YES if you want an invoice sent to the customer after signup is complete."),
                                        "value"         =>"1"
                                       ),
                   lang("Signup Name") => array (
                                        "type"          =>"text",
                                        "description"   =>lang("Select the name to display in the signup process for this payment type. Example: eCheck or Credit Card."),
                                        "value"         =>"egold"
                                       ),
                   lang("Dummy Plugin") => array (
                                        "type"          =>"hidden",
                                        "description"   =>lang("1 = Only used to specify a billing type for a customer. 0 = full fledged plugin requiring complete functions"),
                                        "value"         =>"0"
                                       ),
                   lang("Accept CC Number") => array (
                                        "type"          =>"hidden",
                                        "description"   =>lang("Selecting YES allows the entering of CC numbers when using this plugin type. No will prevent entering of cc information"),
                                        "value"         =>"0"
                                       ),
                   lang("Auto Payment") => array (
                                        "type"          =>"hidden",
                                        "description"   =>lang("No description"),
                                        "value"         =>"0"
                                       ),
                   lang("30 Day Billing") => array (
                                        "type"          =>"hidden",
                                        "description"   =>lang("Select YES if you want ClientExec to treat monthly billing by 30 day intervals.  If you select NO then the same day will be used to determine intervals."),
                                        "value"         =>"0"
                                       ),
                   lang("Check CVV2") => array (
                                        "type"          =>"hidden",
                                        "description"   =>lang("Select YES if you want to accept CVV2 for this plugin."),
                                        "value"         =>"0"
                                       )
        );
        return $variables;
    }

    function _getCurrency($currency)
    {
       $CURRENCY = array('USD' => 1,
                         'CAD' => 2,
                         'GBP' => 44,
                         'AUD' => 61,
                         'JPY' => 81,
                         'EUR' => 85);

       return $CURRENCY[$currency];
    }

    function credit($params)
    {}

    function singlepayment($params) {
        //Function needs to build the url to the payment processor, then redirect
        //Plugin variables can be accesses via $params["plugin_[pluginname]_[variable]"] (ex. $params["plugin_paypal_UserID"])
        $stat_url = mb_substr($params['clientExecURL'],-1,1) == "//" ? $params['clientExecURL']."plugins/gateways/egold/callback.php" : $params['clientExecURL']."/plugins/gateways/egold/callback.php";

        $strURL  = "https://www.e-gold.com/sci_asp/payments.asp?";
        $strURL .= "PAYEE_ACCOUNT=".$params["plugin_egold_User ID"];
        $strURL .= "&PAYEE_NAME=".$params["companyName"];
        $strURL .= "&PAYMENT_ID=".$params['invoiceNumber'];
        $strURL .= "&PAYMENT_AMOUNT=".sprintf("%01.2f", round($params["invoiceTotal"], 2));
        $strURL .= "&STATUS_URL=".$stat_url;
        $strURL .= "&PAYMENT_URL=".$params["clientExecURL"];
        $strURL .= "&NOPAYMENT_URL=".$params["clientExecURL"];
        $strURL .= "&PAYMENT_URL_METHOD=LINK";
        $strURL .= "&NOPAYMENT_URL_METHOD=LINK";
        $strURL .= "&PAYMENT_UNITS=".$this->_getCurrency($params["currencytype"]);
        $strURL .= "&PAYMENT_METAL_ID=1";
        $strURL .= "&BAGGAGE_FIELDS=";
        $strURL .= "&SUGGESTED_MEMO=Payment Invoice ".$params['invoiceNumber'];
        header("Location: $strURL");
        exit;
    }
}
?>
