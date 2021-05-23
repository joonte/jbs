<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
$Settings = $Config['Tasks']['Types']['Taxation'];
#-------------------------------------------------------------------------------
#Debug(SPrintF('[comp/Tasks/Taxation]: Settings = %s',print_r($Settings,true)));
#-------------------------------------------------------------------------------
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecutePeriod'=>$Settings['ExecutePeriod']));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# если неактивна, то через день запуск
if(!$Settings['IsActive'])
	return 24*3600;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#Debug(SPrintF('[comp/Tasks/Taxation]: Config.Invoices.Kassa_54-FZ = %s',print_r($Config['Invoices']['Kassa_54-FZ'],true)));
#-------------------------------------------------------------------------------
# налогообложение
$TaxationSystem = $Config['Invoices']['Kassa_54-FZ']['TaxationSystem'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем, активна ли электронная касса
$Settings = $Config['Invoices']['Kassa_54-FZ'];
#-------------------------------------------------------------------------------
if(!$Settings['IsActive']){
	#-------------------------------------------------------------------------------
	# помечаем все счета как обработанные
	$IsUpdate = DB_Update('Invoices',Array('IsCheckSent'=>TRUE),Array('Where'=>'`IsCheckSent` = "no"'));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return $ExecuteTime;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# достаём все оплаченные счета, по которым не отправлен отчёт в налоговую
$Invoices = DB_Select('InvoicesOwners',Array('*','(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `UserID`) AS `Email`'),Array('Where'=>Array('`IsCheckSent` = "no"')));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoices)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	return $ExecuteTime;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array('Invoices_OK'=>Array(),'Invoices_ERROR'=>Array());
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($Invoices as $Invoice){
	#-------------------------------------------------------------------------------
	$Number = Comp_Load('Formats/Invoice/Number',$Invoice['ID']);
	if(Is_Error($Number))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/Taxation]: обработка счёта = %s; Summ = %s',$Number,$Invoice['Summ']));
	#-------------------------------------------------------------------------------
	# надо все позиции отображать:
	$InvoicesItems = DB_Select('InvoicesItems',Array('*','(SELECT `NameShort` FROM `Services` WHERE `InvoicesItems`.`ServiceID` = `ID`) AS `Name`','(SELECT `Measure` FROM `Services` WHERE `InvoicesItems`.`ServiceID` = `ID`) AS `Measure`'),Array('Where'=>Array(SPrintF('`InvoiceID` = %u',$Invoice['ID']))));
	#-------------------------------------------------------------------------------
	switch(ValueOf($InvoicesItems)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		$InvoicesItems = Array();
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Receipt = Comp_Load(SPrintF('Invoices/%s',$Settings['TaxationKassa']),$Settings,$Invoice,$Number,$InvoicesItems);
	#-------------------------------------------------------------------------------
	switch(ValueOf($Receipt)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('ERROR_SEND_RESEIPT','Произошла ошибка при фискализации счёта');
	case 'false':
		#-------------------------------------------------------------------------------
		$GLOBALS['TaskReturnInfo']['Invoices_ERROR'][] = $Number;
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	case 'true':
		#-------------------------------------------------------------------------------
		$GLOBALS['TaskReturnInfo']['Invoices_OK'][] = $Number;
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Invoices',Array('IsCheckSent'=>TRUE),Array('ID'=>$Invoice['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем результаты, если есть непрошедшие счета - вывешиваем алерт
if(SizeOf($GLOBALS['TaskReturnInfo']['Invoices_ERROR'])){
	#-------------------------------------------------------------------------------
	// проверяем, есть ли непрочитанные сообщения
	$Count = DB_Count('Events',Array('Where'=>"`IsReaded` != 'yes'"));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if(!$Count){
		#-------------------------------------------------------------------------------
		$Event = Array('UserID'=>100,'PriorityID'=>'Warning','IsReaded'=>FALSE,'Text'=>SPrintF('Некоторые счета (%s), не прошли фискализацию',Implode(',',$GLOBALS['TaskReturnInfo']['Invoices_ERROR'])));
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#Debug(SPrintF('[comp/Tasks/Taxation]: TaskReturnInfo = %s',print_r($GLOBALS['TaskReturnInfo'],true)));
#-------------------------------------------------------------------------------
if(SizeOf($GLOBALS['TaskReturnInfo']['Invoices_OK']) < 1)
	UnSet($GLOBALS['TaskReturnInfo']['Invoices_OK']);
#-------------------------------------------------------------------------------
if(SizeOf($GLOBALS['TaskReturnInfo']['Invoices_ERROR']) < 1)
	UnSet($GLOBALS['TaskReturnInfo']['Invoices_ERROR']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
