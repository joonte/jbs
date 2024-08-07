<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('PaymentSystemID','InvoiceID','Summ');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['Tinkoff'];
#-------------------------------------------------------------------------------
$Send = $Settings['Send'];
#-------------------------------------------------------------------------------
#DEBUG(print_r($Settings,true));
#-------------------------------------------------------------------------------
/* сумма, в копейках */
$Send['amount'] = Round($Summ/$Settings['Course'],2) * 100;
#-------------------------------------------------------------------------------
/* номер заказа (счёта) */
$Send['OrderId'] = $InvoiceID;
#-------------------------------------------------------------------------------
$Valute = Array(
		'RUB'	=> 643,
		'USD'	=> 840,
		'EUR'	=> 978,
		'UAH'	=> 980,
		'BYR'	=> 933
		);
#-------------------------------------------------------------------------------
/* числовой код валюты. по уму, надо куда-то в конфиг выносить массив */
//$Send['currency'] = $Valute[$Settings['Valute']];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Invoice/Number',$InvoiceID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$Send['description']	.= SPrintF('%s, %s (%s)',$Comp,Translit($__USER['Name']),$__USER['Email']);
#-------------------------------------------------------------------------------
$Send['SuccessURL']	= SPrintF('%s://%s/v2/Invoices',URL_SCHEME,HOST_ID);
$Send['FailURL']	= SPrintF('%s://%s/v2/Invoices?Error=yes',URL_SCHEME,HOST_ID);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# а ещё в люобй форме есть CSRF
#$Send['CSRF'] = $GLOBALS['CSRF'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Send;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
