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
$Settings = $Config['Tasks']['Types']['DBO'];
#-------------------------------------------------------------------------------
#Debug(SPrintF('[comp/Tasks/DBO]: Settings = %s',print_r($Settings,true)));
#-------------------------------------------------------------------------------
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecutePeriod'=>$Settings['ExecutePeriod']));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# если неактивна, то через день запуск
if(!$Settings['IsActive'])
	return 24*3600;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём настройки клиент-банка
$Settings = $Config['Invoices']['DBO'];
#-------------------------------------------------------------------------------
// если настроек нет или отключено - валим
if(!$Settings['IsActive'] || !$Settings['Token'])
	return 24*3600;
#-------------------------------------------------------------------------------
// загружаем библиотеку
if(Is_Error(System_Load(SPrintF('libs/DBO_%s.php',$Settings['BankName']))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/HTTP.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// получаем список оплаченных счетов
if(Is_Error($BankInvoices = GetStatement($Settings)))
	return 3600;
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'][] = SPrintF('Invoices: %u',SizeOf($BankInvoices));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём минимальный и максимальный номер счёта существующий в биллинге
$InvoicesNums = DB_Select('InvoicesOwners',Array('MAX(`ID`) AS `MAX`','MIN(`ID`) AS `MIN`'),Array('UNIQ'));
switch(ValueOf($InvoicesNums)){
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
// число которые нались в биллинге и стали оплачены
$Paid = 0;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// перебираем счета
foreach($BankInvoices as $BankInvoice){
	#-------------------------------------------------------------------------------
	// что обрабатываем
	Debug(SPrintF('[comp/Tasks/DBO]: [%s]: ищем счёт на сумму (%s)',$BankInvoice['Key'],$BankInvoice['Summ']));
	#-------------------------------------------------------------------------------
	// разбираем примечание
	$Words = Explode(" ",Str_Replace("\n"," ",$BankInvoice['Purpose']));
	#-------------------------------------------------------------------------------
	// перебираем по словам
	foreach($Words as $Word){
		#-------------------------------------------------------------------------------
		$Number = Preg_Replace('/[^0-9]/','',$Word);
		#-------------------------------------------------------------------------------
		// проверяем что число в пределах номеров существующих счетов
		if($Number >= $InvoicesNums['MIN'] && $Number <= $InvoicesNums['MAX']){
			#-------------------------------------------------------------------------------
			// выбираем счёт с таким номером
			$Invoice = DB_Select('InvoicesOwners',Array('ID','UserID','Summ','ContractID','StatusID'),Array('UNIQ','ID'=>$Number));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Invoice)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				#-------------------------------------------------------------------------------
				continue 2;
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/DBO]: НЕ найден счёт (%s)',$Number));
				#-------------------------------------------------------------------------------
			case 'array':
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			// есть такой счёт, проверяем статус
			if(!In_Array($Invoice['StatusID'],Array('Waiting','Rejected'))){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/DBO]: найден счёт (%s), статус (%s), пропускаем',$Number,$Invoice['StatusID']));
				#-------------------------------------------------------------------------------
				continue;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			// проверяем сумму
			if($Invoice['Summ'] != $BankInvoice['Summ']){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/DBO]: найден счёт (%s), сумма не совпадает: в счёте (%s), оплачено (%s), пропускаем',$Number,$Invoice['Summ'],$BankInvoice['Summ']));
				#-------------------------------------------------------------------------------
				continue;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			// проверяем плательщика, достаём даныне профиля у этого договора
			$Profile = DB_Select(Array('Contracts,Profiles'),Array('`Profiles`.`Attribs` AS `Attribs`'),Array('UNIQ','Where'=>Array(SPrintF('`Contracts`.`ID` = %u',$Invoice['ContractID']),'`Contracts`.`ProfileID` = `Profiles`.`ID`')));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Profile)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/DBO]: НЕ найден профиль для договора, счёт (%s)',$Number));
				#-------------------------------------------------------------------------------
				continue 2;
				#-------------------------------------------------------------------------------
			case 'array':
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			if($BankInvoice['Inn'] != @$Profile['Attribs']['Inn']){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/DBO]: НЕ совпадает ИНН плательщика, счёт оплачен (%s), в биллинге (%s)',$BankInvoice['Inn'],@$Profile['Attribs']['Inn']));
				#-------------------------------------------------------------------------------
				continue;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			// номер совпал, статус - не оплачен, сумма совпала, плательщик совпал. проводим
			#----------------------------------TRANSACTION----------------------------------
			if(Is_Error(DB_Transaction($TransactionID = UniqID('Tasks/DBO'))))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Payed','RowsIDs'=>$Invoice['ID'],'Comment'=>'Автоматическое проведение по ДБО'));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Comp)){
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
			if(Is_Error(DB_Commit($TransactionID)))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			// увеличиваем счётчик оплаченных счетов
			$Paid++;
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Paid > 0)
	$GLOBALS['TaskReturnInfo'][] = SPrintF('paid: %u',$Paid);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// ставим новое выполение через 20 минут
return 20*60;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
