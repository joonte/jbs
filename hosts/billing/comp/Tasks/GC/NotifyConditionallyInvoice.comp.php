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
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['GC']['NotifyConditionallyInvoiceSettings'];
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# выхлоп для сотрудников бухгалтерии
$Out = "";
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = "`StatusID` = 'Conditionally'";
#-------------------------------------------------------------------------------
$Invoices = DB_Select('InvoicesOwners',Array('ID','UserID','ContractID','StatusDate','Summ'),Array('SortOn'=>'UserID', 'IsDesc'=>TRUE, 'Where'=>$Where));
switch(ValueOf($Invoices)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Invoices as $Invoice){
		#-------------------------------------------------------------------------------
		# added by lissyara 2012-09-30 in 20:20 MSK, for JBS-109
		# перебираем всех юзеров с условными инвойсами, смотрим сколько от статуса
		# если от статуса больше чем в настройках то отменяем счета:
		if($Invoice['StatusDate'] < Time() - $Settings['DaysBeforeRejectConditionallyInvoice']*24*3600){
			#-------------------------------------------------------------------------------
			// откатываем инвойс в статус "Удалён"
			$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Rejected','RowsIDs'=>$Invoice['ID'],'Comment'=>'Отмена условно оплаченного счёта'));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Comp)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return $StatusSet;
			case 'array':
				#-------------------------------------------------------------------------------
				$IsUpdate = DB_Update('Invoices',Array('IsPosted'=>FALSE),Array('ID'=>$Invoice['ID']));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				# No more...
				break;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		}
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
# достаём условные счета ещё раз - уже без тех которые были отменены
$Where = "`StatusID` = 'Conditionally'";
#-------------------------------------------------------------------------------
$Invoices = DB_Select('InvoicesOwners',Array('ID','UserID','CreateDate','Summ','(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `InvoicesOwners`.`UserID`) AS `UserEmail`'),Array('SortOn'=>'UserID', 'IsDesc'=>TRUE, 'Where'=>$Where));
switch(ValueOf($Invoices)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Invoices as $Invoice){
		#-------------------------------------------------------------------------------
		$Out = SPrintF("%sНеоплаченный счёт на сумму %s от пользователя %s\n",$Out,$Invoice['Summ'],$Invoice['UserEmail']);
		#-------------------------------------------------------------------------------
		Debug(SPrintF("[Tasks/GC/NotifyConditionallyInvoice]: Уведомление о условно оплаченном счёте #%d.",$Invoice['ID']));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
                // генерируем ссылку на оплату
                $PaymentLink = SPrintF('%s://%s/Invoices/%u/',Url_Scheme(),HOST_ID,$Invoice['ID']);
		#-------------------------------------------------------------------------------
		$Invoice['PaymentLink'] = $PaymentLink;
		#-------------------------------------------------------------------------------
		#----------------------------------TRANSACTION----------------------------------
		if(Is_Error(DB_Transaction($TransactionID = UniqID('Tasks/GC/NotifyConditionallyInvoice'))))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Msg = new Message('ConditionallyPayedInvoice',$Invoice['UserID'],$Invoice);
		$IsSend = NotificationManager::sendMsg($Msg);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsSend)){
		case 'true':
			#-------------------------------------------------------------------------------
			$Event = Array('UserID'=>$Invoice['UserID'],'PriorityID'=>'Billing','Text'=>SPrintF('Уведомление о условно оплаченном счёте #%d, неоплачен более %d дней',$Invoice['ID'],$Settings['DaysBeforeNotice']));
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'exception':
			#-------------------------------------------------------------------------------
			$Event = Array('UserID'=>$Invoice['UserID'],'PriorityID'=>'Billing','Text'=>SPrintF('Уведомление о условно оплаченном счёте #%d не доставлено. Не удалось оповестить пользователя ни одним из методов.',$Invoice['ID']));
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
	}
	#-------------------------------------------------------------------------------
        break;
	#-------------------------------------------------------------------------------
default:
        return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# 4. достаём все договора с отрицательным баллансом - добавляем в отчёт для бухов
$Users = DB_Select('ContractsOwners',Array('DISTINCT(`UserID`) AS `UserID`','Balance','`ID` AS `ContractID`','(SELECT MAX(`CreateDate`) FROM `Postings` WHERE `ContractID` = `ContractsOwners`.`ID`) AS `LastOperation`','(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `ContractsOwners`.`UserID`) AS `UserEmail`'),Array('GroupBy'=>'UserID', 'Where'=>'`Balance` < 0'));
switch(ValueOf($Users)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Users as $User){
		#-------------------------------------------------------------------------------
		# проверяем наличие у клиента активных заказов - если их нет, то бугалтерию и дёргать бесполезно, т.к. с него и взять нечего
		$Count = DB_Count('OrdersOwners',Array('Where'=>SPrintF('`UserID` = %u AND `StatusID` = "Active"',$User['UserID'])));
		if(Is_Error($Count))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		# добавляем в отчёт для бухгалтерии
		if($Count)
			$Out = $Out . SPrintF("Отрицательный балланс (%s) у клиента %s\n",$User['Balance'],$User['UserEmail']);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// если последние движения были более чем DaysBeforePreTrial - меняем сообщение (переменную в шаблон, и в шаблоне варианты)
		if(Time() > $User['LastOperation'] + $Settings['DaysBeforePreTrial']*24*3600)
			$User['PreTrial'] = TRUE;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# шлём юзеру уведомление
		$UserBalance = $User;
		#-------------------------------------------------------------------------------
		$Summ = Comp_Load('Formats/Currency',Abs($User['Balance']));
		if(Is_Error($Summ))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$UserBalance['Balance'] = $Summ;
		#-------------------------------------------------------------------------------
		$Msg = new Message('NegativeContractBalance', $User['UserID'], $UserBalance/* = $User*/);
		$IsSend = NotificationManager::sendMsg($Msg);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsSend)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'true':
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		# вешаем событие
		if($Config['Invoices']['EventOnNegativeBalance']){
			#-------------------------------------------------------------------------
			$Event = Array(
					'UserID'	=> $User['UserID'],
					'PriorityID'	=> 'Billing',
					'Text'		=> SPrintF('У пользователя отрицательный баланс (%s)',$User['Balance']),
					'IsReaded'	=> FALSE
					);
			$Event = Comp_Load('Events/EventInsert',$Event);
			#-------------------------------------------------------------------------
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------
		}
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
# 5. Обновляем лимиты пользователя
$Result = DB_Query('UPDATE `Users` SET `LayPayMaxSumm`=((SELECT SUM(`Summ`) FROM `InvoicesOwners` WHERE `InvoicesOwners`.`UserID`=`Users`.`ID` AND `StatusID` = "Payed") / 10) WHERE ((SELECT SUM(`Summ`) FROM `InvoicesOwners` WHERE `InvoicesOwners`.`UserID`=`Users`.`ID` AND `StatusID` = "Payed") / 10) > `LayPayMaxSumm`');
if(Is_Error($Result))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------
$Result = DB_Query('UPDATE `Users` SET `LayPayMaxSumm` = 0 WHERE `LayPayMaxSumm` IS NULL');
if(Is_Error($Result))
	return ERROR | @Trigger_Error(500);
#Debug(SPrintF("[Tasks/GC/NotifyConditionallyInvoice]: отчёт для бухгалтерии %s",$Out));
#-------------------------------------------------------------------
#-------------------------------------------------------------------
# ищщем сотрудников бухгалтерии
$Entrance = Tree_Entrance('Groups',3200000);
#-------------------------------------------------------------------
switch(ValueOf($Entrance)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#---------------------------------------------------------------
	$String = Implode(',',$Entrance);
	#---------------------------------------------------------------
	$Employers = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',$String)));
	#---------------------------------------------------------------
	switch(ValueOf($Employers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# найти всех сотрудников, раз нет сотрудников в бухгалтерии
		$Entrance = Tree_Entrance('Groups',3000000);
		#-------------------------------------------------------------------
		switch(ValueOf($Entrance)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#---------------------------------------------------------------
			$String = Implode(',',$Entrance);
			#---------------------------------------------------------------
			$Employers = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',$String)));
			#---------------------------------------------------------------
			switch(ValueOf($Employers)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				Debug(SPrintF("[Tasks/GC/NotifyConditionallyInvoice]: найдено %s сотрудников любых отделов",SizeOf($Employers)));
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		break;
	case 'array':
		Debug(SPrintF("[Tasks/GC/NotifyConditionallyInvoice]: найдено %s сотрудников отдела бухгалтерии",SizeOf($Employers)));
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(StrLen($Out) == 0)
	return TRUE;
#-------------------------------------------------------------------------------
foreach($Employers as $Employer){
	#-------------------------------------------------------------------------------
	if($Employer['ID'] > 2000 || $Employer['ID'] == 100){
		#-------------------------------------------------------------------------------
		$Msg = new DispatchMsg(Array('Theme'=>'Список условно оплаченных счетов','Message'=>$Out), (integer)$Employer['ID']);
		$IsSend = NotificationManager::sendMsg($Msg);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsSend)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#-------------------------------------------------------------------------------
			# No more...
			break;
			#-------------------------------------------------------------------------------
		case 'true':
			#-------------------------------------------------------------------------------
			# No more...
			Debug(SPrintF("[Tasks/GC/NotifyConditionallyInvoice]: Сообщение для сотрудника #%s отослано",$Employer['ID']));
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------


?>
