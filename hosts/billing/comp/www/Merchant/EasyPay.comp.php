<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
if(!Count($Args))
	return 'No args...';
#-------------------------------------------------------------------------------
$ArgsIDs = Array('order_mer_code','sum','mer_no','card','purch_date','notify_signature');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
	$Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['EasyPay'];
#-------------------------------------------------------------------------------
$Hash = Array(
	#-----------------------------------------------------------------------------
	$Args['order_mer_code'],
	$Args['sum'],
	$Args['mer_no'],
	$Args['card'],
	$Args['purch_date'],
	$Settings['Hash']
);
#-------------------------------------------------------------------------------
if(MD5(Implode('',$Hash)) != $Args['notify_signature'])
	return ERROR | @Trigger_Error('[comp/Merchant/EasyPay]: проверка подлинности завершилась не удачей');
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$Args['order_mer_code']));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#---------------------------------------------------------------------------
	if(Floor($Invoice['Summ']/$Settings['Course']) != $Args['sum'])
		return ERROR | @Trigger_Error('[comp/Merchant/EasyPay]: проверка суммы платежа завершилась не удачей');
	#---------------------------------------------------------------------------
	$Comp = Comp_Load('Users/Init',100);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#---------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$Invoice['ID'],'Comment'=>SPrintF('Автоматическое зачисление [%s]',$Args['card'])));
	#---------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		return 'OK';
	default:
		return ERROR | @Trigger_Error(101);
	}
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
