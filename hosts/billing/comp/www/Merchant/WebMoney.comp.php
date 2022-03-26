<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = &Args();
#-------------------------------------------------------------------------------
if(!Count($Args))
	return 'No args...';
#-------------------------------------------------------------------------------
$ArgsIDs = Array('LMI_PREREQUEST','LMI_PAYEE_PURSE','LMI_PAYMENT_AMOUNT','LMI_PAYMENT_NO','LMI_MODE','LMI_SYS_INVS_NO','LMI_SYS_TRANS_NO','LMI_SYS_TRANS_DATE','LMI_PAYER_PURSE','LMI_PAYER_PURSE','LMI_PAYER_WM','LMI_HASH');
#-------------------------------------------------------------------------------
foreach($ArgsIDs as $ArgID)
	$Args[$ArgID] = @$Args[$ArgID];
#-------------------------------------------------------------------------------
if($Args['LMI_PREREQUEST'])
	return 'YES';
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
# Определяем тип кошелька, LMI_PAYEE_PURSE - содержит номер кошелька,
# который начинается с Z или R соответственно
$LMI_PAYEE_PURSE = $Args['LMI_PAYEE_PURSE'];
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems'][SPrintF('WebMoney%s',$LMI_PAYEE_PURSE[0])];
#-------------------------------------------------------------------------------
$Hash = $LMI_PAYEE_PURSE.
        $Args['LMI_PAYMENT_AMOUNT'].
        $Args['LMI_PAYMENT_NO'].
        $Args['LMI_MODE'].
        $Args['LMI_SYS_INVS_NO'].
        $Args['LMI_SYS_TRANS_NO'].
        $Args['LMI_SYS_TRANS_DATE'].
        $Settings['Hash'].
        $Args['LMI_PAYER_PURSE'].
        $Args['LMI_PAYER_WM'];
#-------------------------------------------------------------------------------
$Hash = StrToUpper(Hash('sha256', $Hash));
#-------------------------------------------------------------------------------
if($Hash != $Args['LMI_HASH'])
	return ERROR | @Trigger_Error('[comp/Merchant/WebMoney]: проверка подлинности завершилась неудачей');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$Args['LMI_PAYMENT_NO']));
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
if(Round($Invoice['Summ']/$Settings['Course'],2) != $Args['LMI_PAYMENT_AMOUNT'])
	return ERROR | @Trigger_Error('[comp/Merchant/WebMoney]: проверка суммы платежа завершилась неудачей');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Users/Init',100);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$Invoice['ID'],'Comment'=>'Автоматическое зачисление'));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	return 'YES';
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
