<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('SystemID','InvoiceID','Summ');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['RBKMoney'];
#-------------------------------------------------------------------------------
$Send = $Settings['Send'];
#-------------------------------------------------------------------------------
$Send['orderId'] = $InvoiceID;
#-------------------------------------------------------------------------------
$Send['recipientAmount'] = Round($Summ/$Settings['Course'],2);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Invoice/Number',$InvoiceID);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$Send['serviceName'] .= SPrintF('%s, %s (%s)',$Comp,Translit($__USER['Name']),$__USER['Email']);
#-------------------------------------------------------------------------------
$Send['successUrl'] = SPrintF('%s://%s/Invoices',URL_SCHEME,HOST_ID);
$Send['failUrl']    = SPrintF('%s://%s/Invoices?Error=yes',URL_SCHEME,HOST_ID);
#-------------------------------------------------------------------------------
return $Send;
#-------------------------------------------------------------------------------

?>
