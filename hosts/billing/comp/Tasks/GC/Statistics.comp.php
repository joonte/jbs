<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['GC']['StatisticsSettings'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TRUE;
#-------------------------------------------------------------------------------
// запускаем по субботам (в любом варианте, неделя либо 0-6, либо 1-7 - должно сработать)
if(Date('N') != 6)
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# Users
$Statistics = Array(
		'Stamp'		=> Time(),
		'Year'		=> Date('Y'),
		'Month'		=> Date('m'),
		'Day'		=> Date('d'),
		'TableID'	=> 'Users',
		'PackageID'	=> NULL,
		);
#-------------------------------------------------------------------------------
$Wheres = Array(
		// всего юзеров
		'Total'		=> '1 = 1',
		// уникальных юзеров с заказами (активные клиенты)
		'Active'	=> '(SELECT COUNT(DISTINCT(`UserID`)) FROM `OrdersOwners` WHERE `UserID` = `Users`.`ID`) > 0',
		// зареганные за последнюю неделю (новые клиенты)
		'New'		=> '`RegisterDate` > UNIX_TIMESTAMP() - 7*24*3600',
		// с оплаченными счетами, но без услуг
		'Suspended'	=> Array('(SELECT COUNT(DISTINCT(`UserID`)) FROM `OrdersOwners` WHERE `UserID` = `Users`.`ID`) = 0 ','(SELECT COUNT(DISTINCT(`UserID`)) FROM `InvoicesOwners` WHERE `UserID` = `Users`.`ID`) > 0')
		);
#-------------------------------------------------------------------------------
foreach(Array_Keys($Wheres) as $Key){
	#-------------------------------------------------------------------------------
	$Count = DB_Count('Users',Array('Where'=>$Wheres[$Key]));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Statistics[$Key] = $Count;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$IsInsert = DB_Insert('Statistics',$Statistics);
if(Is_Error($IsInsert))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# Users OnLine
$Statistics = Array(
		'Stamp'		=> Time(),
		'Year'		=> Date('Y'),
		'Month'		=> Date('m'),
		'Day'		=> Date('d'),
		'TableID'	=> 'OnLine',
		'PackageID'	=> NULL,
		);
#-------------------------------------------------------------------------------
$OnLine1d = DB_Select('RequestLog',Array('COUNT(DISTINCT(`UserID`)) AS `Count`'),Array('UNIQ','Where'=>Array('UserID NOT IN (SELECT `ID` FROM `Users` WHERE `GroupID` != 2000000)','`CreateDate` > UNIX_TIMESTAMP() - 7*24*3600')));
#-------------------------------------------------------------------------------
if(Is_Error($OnLine1d))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Statistics['Active'] = $OnLine1d['Count'];
#-------------------------------------------------------------------------------
$IsInsert = DB_Insert('Statistics',$Statistics);
if(Is_Error($IsInsert))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// доходность серверов
$Comp = Comp_Load('Statistics/ServersIncome',Array('IsCreate'=>TRUE));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Servers = DB_Select('TmpData',Array('ID','Params'),Array('UNIQ','Where'=>'`AppID` = "Statistics/ServersIncome"','SortOn'=>'CreateDate','Limits'=>Array(0,1)));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	// что-то есть, вносим в БД
	$Statistics = Array(
			'Stamp'		=> Time(),
			'Year'		=> Date('Y'),
			'Month'		=> Date('m'),
			'Day'		=> Date('d'),
			'TableID'	=> 'Servers',
			);
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Servers['Params']) as $Key){
		#-------------------------------------------------------------------------------
		$Server = $Servers['Params'][$Key];
		#-------------------------------------------------------------------------------
		$Statistics['PackageID']	= $Key;				// имя сервера
		$Statistics['Total']		= $Server['NumAccounts'];	// общее число аккаунтов
		$Statistics['Active']		= $Server['PaidAccounts'];	// оплаченных аккаунтов
		$Statistics['New']		= Round($Server['ServerIncome']);// прибыль сервера
		$Statistics['Waiting']		= Round($Server['AccountIncome']);// приблыь 1-го аккаунта
		#-------------------------------------------------------------------------------
		// вносим в таблицу
		$IsInsert = DB_Insert('Statistics',$Statistics);
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# счета на оплату, число самих счетов
$Statistics = Array(
		'Stamp'		=> Time(),
		'Year'		=> Date('Y'),
		'Month'		=> Date('m'),
		'Day'		=> Date('d'),
		'TableID'	=> 'Invoices',
		'PackageID'	=> NULL,
		);
#-------------------------------------------------------------------------------
$Wheres = Array(
		// всего счетов
		'Total'		=> '1 = 1',
		// оплаченных счетов за неделю)
		'Active'	=> '`StatusID` = "Payed" AND `StatusDate` > UNIX_TIMESTAMP() - 7*24*3600',
		// новых, за неделю
		'New'		=> '`CreateDate` > UNIX_TIMESTAMP() - 7*24*3600',
		// ждущих оплаты, за неделю (т.е. авписано не оплачено за неделю)
		'Waiting'	=> '`StatusID` = "Waiting" AND `StatusDate` > UNIX_TIMESTAMP() - 7*24*3600',
		// отменено, за неделю
		'Suspended'	=> '`StatusID` = "Rejected" AND `StatusDate` > UNIX_TIMESTAMP() - 7*24*3600',
		);
#-------------------------------------------------------------------------------
foreach(Array_Keys($Wheres) as $Key){
	#-------------------------------------------------------------------------------
	$Count = DB_Count('Invoices',Array('Where'=>$Wheres[$Key]));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Statistics[$Key] = $Count;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$IsInsert = DB_Insert('Statistics',$Statistics);
if(Is_Error($IsInsert))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# счета на оплату, суммы счетов
$Statistics = Array(
		'Stamp'		=> Time(),
		'Year'		=> Date('Y'),
		'Month'		=> Date('m'),
		'Day'		=> Date('d'),
		'TableID'	=> 'Invoices',
		'PackageID'	=> 'Summ',
		);
#-------------------------------------------------------------------------------
foreach(Array_Keys($Wheres) as $Key){
	#-------------------------------------------------------------------------------
        $Invoice = DB_Select('Invoices','SUM(`Summ`) AS `Summ`',Array('UNIQ','Where'=>$Wheres[$Key]));
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
	$Statistics[$Key] = $Invoice['Summ'];
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$IsInsert = DB_Insert('Statistics',$Statistics);
if(Is_Error($IsInsert))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# Штатные сервисы
$Where = Array('`Code` != "Default"','`IsHidden` = "no"');
#-------------------------------------------------------------------------------
$Services = DB_Select('Services',Array('ID','Code','Name'),Array('Where'=>$Where));
switch(ValueOf($Services)){
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
$Insert = Array(
		'Stamp'		=> Time(),
		'Year'		=> Date('Y'),
		'Month'		=> Date('m'),
		'Day'		=> Date('d'),
		'PackageID'	=> NULL
		);
#-------------------------------------------------------------------------------
foreach($Services as $Service){
	#-------------------------------------------------------------------------------
	$Statistics = $Insert;	
	$Statistics['TableID'] = $Service['Code'];
	#-------------------------------------------------------------------------------
	$Wheres = Array(
			// всего заказов
			'Total'		=> Array(),
			// активных
			'Active'	=> Array('`StatusID` = "Active"'),
			// заказано за неделю
			'New'		=> Array('`OrderDate` > UNIX_TIMESTAMP() - 7*24*3600'),
			// неоплаченных никогда
			'Waiting'	=> Array('`StatusID` = "Waiting"'),
			// заблокированных
			'Suspended'	=> Array('`StatusID` = "Suspended"')
			);
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Wheres) as $Key){
		#-------------------------------------------------------------------------------
		$Where = $Wheres[$Key];
		#-------------------------------------------------------------------------------
		$Where[] = SPrintF('`ServiceID` = %u',$Service['ID']);
		#-------------------------------------------------------------------------------
		$Count = DB_Count('OrdersOwners',Array('Where'=>$Where));
		if(Is_Error($Count))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Statistics[$Key] = $Count;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('Statistics',$Statistics);
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// для выделенных серверов по тарифам бессымысленно, они индивидуальные все
	if($Service['Code'] == 'DS')
		continue;
	#-------------------------------------------------------------------------------
	# по тарифам
	$Columns = Array('ID','Name');
	#-------------------------------------------------------------------------------
	if($Service['Code'] == 'Domain')
		$Columns[] = 'ServerID';
	#-------------------------------------------------------------------------------
	$Schemes = DB_Select(SPrintF('%sSchemes',$Service['Code']),$Columns,Array('SortOn'=>'SortID'));
	switch(ValueOf($Schemes)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		continue 2;
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$Statistics = $Insert;
	$Statistics['TableID'] = $Service['Code'];
	#-------------------------------------------------------------------------------
	foreach($Schemes as $Scheme){
		#-------------------------------------------------------------------------------
		# костыль для доменов - слишком много тарифных планов
		if($Service['Code'] == 'Domain'){
			#-------------------------------------------------------------------------------
			$Count = DB_Count(SPrintF('%sOrdersOwners',$Service['Code']),Array('Where'=>SPrintF('`SchemeID` = %u',$Scheme['ID'])));
			#-------------------------------------------------------------------------------
			if(Is_Error($Count))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			if(!$Count)
				continue;
			#-------------------------------------------------------------------------------
			$Registrator = DB_Select('Servers',Array('ID','Params'),Array('UNIQ','ID'=>$Scheme['ServerID']));
			if(Is_Error($Registrator))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Statistics['PackageID'] = SPrintF('%s / %s',$Registrator['Params']['Name'],$Scheme['Name']);
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$Statistics['PackageID'] = $Scheme['Name'];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		# поле есть только в OrdersOwners
		$Wheres['New'] = Array(SPrintF('(SELECT `OrderDate` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `%sOrdersOwners`.`OrderID`) > UNIX_TIMESTAMP() - 7*24*3600',$Service['Code']));
		#-------------------------------------------------------------------------------
		foreach(Array_Keys($Wheres) as $Key){
			#-------------------------------------------------------------------------------
			$Where = $Wheres[$Key];
			#-------------------------------------------------------------------------------
			$Where[] = SPrintF('`SchemeID` = %u',$Scheme['ID']);
			#-------------------------------------------------------------------------------
			$Count = DB_Count(SPrintF('%sOrdersOwners',$Service['Code']),Array('Where'=>$Where));
			if(Is_Error($Count))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Statistics[$Key] = $Count;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$IsInsert = DB_Insert('Statistics',$Statistics);
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
# TODO услуги настроенные вручную - тоже надо

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------







?>
