<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
if(!Count($Args))
	return 'No args...';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# yandex protocol version = commonHTTP-3.0
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ArgsIDs = Array(
		'notification_type',
		'operation_id',
		'amount',
		'withdraw_amount',
		'currency',
		'datetime',
		'sender',
		'codepro',
		'label',
		'sha1_hash',
		'requestDatetime',
		'action',
		'md5',
		'shopId',
		'orderNumber',
		'customerNumber',
		'orderCreatedDatetime',
		'orderSumAmount',
		'orderSumCurrencyPaycash',
		'orderSumBankPaycash',
		'shopSumAmount',
		'shopSumCurrencyPaycash',
		'shopSumBankPaycash'
		);
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
	$Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Date = Date('c', Time());
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(StrLen($Args['sha1_hash']) > 1){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/Merchant/YooMoney]: физики: лопатник'));
	#-------------------------------------------------------------------------------
	$Settings = $Config['Invoices']['PaymentSystems']['Yandex.p2p'];
	#-------------------------------------------------------------------------------
	$Sha1 = Array(
			$Args['notification_type'],
			$Args['operation_id'],
			$Args['amount'],
			$Args['currency'],
			$Args['datetime'],
			$Args['sender'],
			$Args['codepro'],
			$Settings['Hash'],
			$Args['label'],
			);
	#-------------------------------------------------------------------------------
	if(Sha1(Implode('&',$Sha1)) != $Args['sha1_hash'])
		return ERROR | @Trigger_Error('[comp/www/Merchant/YooMoney]: проверка подлинности завершилась неудачей');
	#-------------------------------------------------------------------------------
	$OrderID = $Args['label'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$OrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Invoice)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	if(Round($Invoice['Summ']/$Settings['Course'],2) != $Args['withdraw_amount'])
		return ERROR | @Trigger_Error('[comp/Merchant/YooMoney]: проверка суммы платежа завершилась неудачей');
	#-------------------------------------------------------------------------------
	$InvoiceID = $Invoice['ID'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Users/Init',100);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$InvoiceID,'Comment'=>'Автоматическое зачисление'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		return TemplateReplace('www.Merchant.YooMoney',Array('Args'=>$Args,'Date'=>$Date),FALSE);
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/Merchant/YooMoney]:  юрики: яндекс-касса'));
	#-------------------------------------------------------------------------------
	$OrderID = $Args['orderNumber'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Settings = $Config['Invoices']['PaymentSystems']['YooMoney'];
	#-------------------------------------------------------------------------------
	$Md5 = Array(
		$Args['action'],
		$Args['orderSumAmount'],
		$Args['orderSumCurrencyPaycash'],
		$Args['orderSumBankPaycash'],
		$Args['shopId'],
		$Args['invoiceId'],
		$Args['customerNumber'],
		$Settings['Hash']
	);
	#-------------------------------------------------------------------------------
	if(StrToUpper(Md5(Implode(';',$Md5))) != $Args['md5'])
		return ERROR | @Trigger_Error('[comp/www/Merchant/YooMoney]: проверка подлинности завершилась неудачей');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$OrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Invoice)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	if(Round($Invoice['Summ']/$Settings['Course'],2) != $Args['orderSumAmount'])
		return ERROR | @Trigger_Error('[comp/Merchant/YooMoney]: проверка суммы платежа завершилась неудачей');
	#-------------------------------------------------------------------------------
	$InvoiceID = $Invoice['ID'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	switch($Args['action']){
	case 'checkOrder':
		#-------------------------------------------------------------------------------
		$Date = Date('c', Time());
		#-------------------------------------------------------------------------------
		return TemplateReplace('www.Merchant.YooMoney',Array('Args'=>$Args,'Date'=>$Date),FALSE);
		#-------------------------------------------------------------------------------
	case 'paymentAviso':
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Users/Init',100);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$InvoiceID,'Comment'=>'Автоматическое зачисление'));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Comp)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			return TemplateReplace('www.Merchant.YooMoney',Array('Args'=>$Args,'Date'=>$Date),FALSE);
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
