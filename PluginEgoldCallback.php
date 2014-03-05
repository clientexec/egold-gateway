<?php
require_once 'modules/admin/models/PluginCallback.php';
require_once 'modules/billing/models/class.gateway.plugin.php';

class PluginEgoldCallback extends PluginCallback
{
    
    function processCallback()
    {
        // ignore blank browser requests
        if (!isset($_POST['PAYMENT_ID'])) {
            return;
        }
        
        //Create plug in class to interact with CE
        $cPlugin = new Plugin($_POST['PAYMENT_ID'], 'egold', $this->user);
        
        $hash  = "$_POST[PAYMENT_ID]:$_POST[PAYEE_ACCOUNT]:$_POST[PAYMENT_AMOUNT]:$_POST[PAYMENT_UNITS]:$_POST[PAYMENT_METAL_ID]:$_POST[PAYMENT_BATCH_NUM]:";
        $hash .= "$_POST[PAYER_ACCOUNT]:".md5(trim($cPlugin->GetPluginVariable("plugin_egold_Alternate Passphrase"))).":$_POST[ACTUAL_PAYMENT_OUNCES]:$_POST[USD_PER_OUNCE]:$_POST[FEEWEIGHT]:$_POST[TIMESTAMPGMT]";
        $hash  = trim(md5(strtoupper(trim($hash))));
        
        //$cPlugin->m_TransactionID = $_POST[PAYMENT_BATCH_NUM];
        $cPlugin->setAmount($_POST["PAYMENT_AMOUNT"]);
        $cPlugin->setAction('charge');
        
        if (strcasecmp(trim($_POST['V2_HASH']), $hash) == 0) {
           $cPlugin->PaymentAccepted($_POST["PAYMENT_AMOUNT"]);
        } else {
           $cPlugin->PaymentRejected("Error");
        }
    }
    
}

?>
