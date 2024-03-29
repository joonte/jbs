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
$InvoiceID	= (integer) @$Args['InvoiceID'];
$CreateDate	= (integer) @$Args['CreateDate'];
$PaymentSystemID=  (string) @$Args['PaymentSystemID'];
$ServiceIDs	=   (array) @$Args['ServiceIDs'];
$Comments	=   (array) @$Args['Comments'];
$Amounts	=   (array) @$Args['Amounts'];
$ItemSumms	=   (array) @$Args['ItemSumms'];
$IsDeletes	=   (array) @$Args['IsDeletes'];
$IsCheckSent	= (boolean) @$Args['IsCheckSent'];
$Summ		=  (double) @$Args['Summ'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Summ)
	return new gException('ZERO_SUMM','Сумма счёта не может быть нулевой');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Invoice = DB_Select('InvoicesOwners',Array('ID','UserID','ContractID','IsPosted'),Array('UNIQ','ID'=>$InvoiceID));
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
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('InvoicesEdit',(integer)$__USER['ID'],(integer)$Invoice['UserID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsPermission)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	return ERROR | @Trigger_Error(700);
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$InvoiceID = $Invoice['ID'];
#-------------------------------------------------------------------------------
if($Invoice['IsPosted'])
	if(!$__USER['IsAdmin'])
		return new gException('ACCOUNT_PAYED','Счёт оплачен и не может быть изменен');
#-------------------------------------------------------------------------------
#-------------------------------TRANSACTION-------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('InvoiceEdit'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('ID','CreateDate','TypeID'),Array('UNIQ','ID'=>$Invoice['ContractID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
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
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Messages = Messages();
#-------------------------------------------------------------------------------
$PaymentSystems = $Config['Invoices']['PaymentSystems'];
#-------------------------------------------------------------------------------
if(!$PaymentSystemID)
	return new gException('PAYMENT_SYSTEM_NOT_SELECTED','Платёжная система не указана');
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$PaymentSystems = $Config['Invoices']['PaymentSystems'];
#-------------------------------------------------------------------------------
if(!IsSet($PaymentSystems[$PaymentSystemID]))
	return new gException('PAYMENT_SYSTEM_NOT_FOUND','Платёжная система не найдена');
#-------------------------------------------------------------------------------
$PaymentSystem = $PaymentSystems[$PaymentSystemID];
#-------------------------------------------------------------------------------
if(!$PaymentSystem['ContractsTypes'][$Contract['TypeID']])
	return new gException('WRONG_CONTRACT_TYPE','Данный вид договора не может быть использован для выписки счета данного типа');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IInvoice = Array('PaymentSystemID'=>$PaymentSystemID);
#-------------------------------------------------------------------------------
if($__USER['IsAdmin']){
	#-------------------------------------------------------------------------------
	$IInvoice['CreateDate'] = $CreateDate;
	#-------------------------------------------------------------------------------
	$IInvoice['IsCheckSent'] = $IsCheckSent;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('Invoices',$IInvoice,Array('ID'=>$InvoiceID));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// правим поля счёта
if($__USER['IsAdmin'] && Is_Array($ServiceIDs) && Count($ServiceIDs)){
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($ServiceIDs) as $Key){
		#-------------------------------------------------------------------------------
		if(IsSet($IsDeletes[$Key])){
			#-------------------------------------------------------------------------------
			// стоит галка про удаление элемента счёта
			$IsDelete = DB_Delete('InvoicesItems',Array('ID'=>$Key));
			if(Is_Error($IsDelete))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$IInvoicesItems = Array();
			#-------------------------------------------------------------------------------
			$IInvoicesItems['ServiceID'] = $ServiceIDs[$Key];
			#-------------------------------------------------------------------------------
			$IInvoicesItems['Comment'] = $Comments[$Key];
			#-------------------------------------------------------------------------------
			$IInvoicesItems['Amount'] = $Amounts[$Key];
			#-------------------------------------------------------------------------------
			$IInvoicesItems['Summ'] = $ItemSumms[$Key];
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$IsUpdate = DB_Update('InvoicesItems',$IInvoicesItems,Array('ID'=>$Key));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Invoices/Build',$InvoiceID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
