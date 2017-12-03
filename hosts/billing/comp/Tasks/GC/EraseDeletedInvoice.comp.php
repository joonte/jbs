<?php

#-------------------------------------------------------------------------------
/** @author Sergey Sedov (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['GC']['Invoices'];
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(
		"`StatusID` = 'Rejected'","`IsPosted` = 'no'",
		SPrintF("`StatusDate` < UNIX_TIMESTAMP( ) - %d *86400", $Settings['DaysBeforeErase'])
		);
#-------------------------------------------------------------------------------
$Invoices = DB_Select('InvoicesOwners',Array('ID','UserID'),Array('SortOn'=>'CreateDate', 'IsDesc'=>TRUE, 'Where'=>$Where));
switch(ValueOf($Invoices)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return TRUE;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($Invoices as $Invoice){
	#-------------------------------------------------------------------------------
	Debug( SPrintF("[Tasks/GC/EraseDeletedInvoice]: Удаление счёта #%d.",$Invoice['ID']) );
	#-------------------------------------------------------------------------------
	#----------------------------------TRANSACTION----------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/EraseDeletedInvoice'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/Delete',Array('TableID'=>'Invoices','RowsIDs'=>$Invoice['ID']));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'array':
		#-------------------------------------------------------------------------------
		$Event = Array('UserID'=>$Invoice['UserID'],'PriorityID'=>'Billing','Text'=>SPrintF('Отменённый счёт #%d автоматически удалён, оплата не поступила в течение %d дней.',$Invoice['ID'],$Settings['DaysBeforeErase']));
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(500);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Commit($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# JBS-1230: очистка файла с адресами URL
#-------------------------------------------------------------------------------
# файл в котором хранятся номера счетов и адреса редиректа
$Tmp = System_Element('tmp');
if(Is_Error($Tmp))
	return ERROR | @Trigger_Error('[Tasks/GC/EraseDeletedInvoice]: не удалось найти временную папку');
#-------------------------------------------------------------------------------
$SberBankFileDB = SPrintF('%s/SberBank.txt',$Tmp);
#-------------------------------------------------------------------------------
# массив, куда собираем живые адреса
$Out = Array('#Invoice'=>'URL');
#-------------------------------------------------------------------------------
if(File_Exists($SberBankFileDB)){
	#-------------------------------------------------------------------------------
	$IsRead = IO_Read($SberBankFileDB);
	if(Is_Error($IsRead))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Lines = Explode("\n", Trim($IsRead));
	#-------------------------------------------------------------------------------
	foreach($Lines as $Line){
		#-------------------------------------------------------------------------------
		#Debug(SPrintF("[Tasks/GC/EraseDeletedInvoice]: Line = %s",$Line));
		#-------------------------------------------------------------------------------
		List($InvoiceTMP, $URL) = Preg_Split("/[\s]+/",$Line);
                #-------------------------------------------------------------------------------
		#Debug(SPrintF("[Tasks/GC/EraseDeletedInvoice]: InvoiceTMP = %s; URL = %s",$InvoiceTMP,$URL));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# проверка что номер счёта - это число
		if(!Is_Numeric($InvoiceTMP))
			continue;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# смотрим, есть ли такой счёт вообще
		$Count = DB_Count('Invoices',Array('ID'=>IntVal($InvoiceTMP)));
		if(Is_Error($Count))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if(!$Count)
			continue;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# смотрим, не оплачен ли счёт
		$Count = DB_Count('Invoices',Array('Where'=>Array(SPrintF('`ID` = %u',$InvoiceTMP),'`StatusID` = "Payed"')));
		if(Is_Error($Count))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if($Count)
			continue;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# закидывем номер и адрес в строку
		$Out[$InvoiceTMP] = $URL;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#Debug(SPrintF("[Tasks/GC/EraseDeletedInvoice]: Out =  %s",print_r($Out,true)));
	#-------------------------------------------------------------------------------
	# удаляем файл, и создаём заново с новым содержимым
	if(!@UnLink($SberBankFileDB))
		return ERROR | @Trigger_Error(SPrintF('[Tasks/GC/EraseDeletedInvoice]: не удалось удалить файл %s',$SberBankFileDB));
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Out) as $InvoiceID){
		#-------------------------------------------------------------------------------
		$IsWrite = IO_Write($SberBankFileDB,SPrintF("%s\t\t%s\n",$InvoiceID,$Out[$InvoiceID]));
		if(Is_Error($IsWrite))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$IsWrite = IO_Write($SberBankFileDB,SPrintF("#end of saved codes\n"));
	if(Is_Error($IsWrite))
		return ERROR | @Trigger_Error(500);

	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# вообще, бессмысленно, оно за раз всё удаляет.
# но пусть будет, как пример кода
$Count = DB_Count('Invoices',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return ($Count?$Count:TRUE);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
