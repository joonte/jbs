<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('PaymentSystemID','InvoiceID','Summ');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['Yandex.p2p'];
#-------------------------------------------------------------------------------
$Send = $Settings['Send'];
#-------------------------------------------------------------------------------
$Send['sum'] = Round($Summ/$Settings['Course'],2);

#-------------------------------------------------------------------------------
$Send['label'] = $InvoiceID;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Send['successURL'] = SPrintF('%s://%s/Invoices',URL_SCHEME,HOST_ID);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Invoice/Number',$InvoiceID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$Send['short-dest'] .= SPrintF('%s, %s (%s)',$Comp,Translit($__USER['Name']),$__USER['Email']);
#-------------------------------------------------------------------------------
$Send['targets'] = $Send['short-dest'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# х.з. что это
#$Send['cms_name'] = 'JBS';
#$Send['writable-targets'] = 'false';
#$Send['writable-sum'] = 'false';
#$Send['paymentType'] = 'PC';
#$Send['comment-needed'] = 'false';
$Send['quickpay-form'] = 'shop';
#$Send['is-inner-form'] = 'false';
#-------------------------------------------------------------------------------
#$Send[''] =

#-------------------------------------------------------------------------------
return $Send;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
