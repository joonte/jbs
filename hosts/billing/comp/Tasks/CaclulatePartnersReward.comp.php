<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru  */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['CaclulatePartnersReward'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# достаём время выполнения
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecuteTime'=>$Settings['ExecuteTime'],'ExecuteDayOfMonth'=>$Settings['ExecuteDayOfMonth'],'ExecuteMonths'=>$Settings['ExecuteMonths'],'DefaultTime'=>MkTime(4,0,0,Date('n')+1,5,Date('Y'))));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# если неактивна, то через день запуск
if(!$Settings['IsActive'])
	return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# выбираем значения партнёрских процентов по каждой услуге
$PartnerPercents = Array();
#-------------------------------------------------------------------------------
$Services = DB_Select('Services',Array('ID','PartnersRewardPercent'));
switch(ValueOf($Services)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Services as $Service)
		$PartnerPercents[$Service['ID']] = $Service['PartnersRewardPercent'];
		//$PartnerPercents[$Service['ID']] = ($Service['PartnersRewardPercent'] < 0)?$Settings['PartnersRewardPercent']:$Service['PartnersRewardPercent'];
	#------------------------------------------------------------------------------- 
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# vars
$TotalSumm = 0;
#-------------------------------------------------------------------------------
$MessageToAdmins = "Начисления по реферальной программе за прошлый месяц:\n\n";
#-------------------------------------------------------------------------------
$MonthsNames = Array('Декабрь','Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
#-------------------------------------------------------------------------------
$PreviousTime = MkTime(4,0,0,Date('n')-1,5,Date('Y'));
#-------------------------------------------------------------------------------
$PreviousYear = date('Y',$PreviousTime);
#-------------------------------------------------------------------------------
$PreviousMonthName = $MonthsNames[Date('n',$PreviousTime)];
#-------------------------------------------------------------------------------
$Theme = SPrintF('Начисления по партнёрской программе за %s %u г.',$PreviousMonthName,$PreviousYear);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// выбираем рефералов
$Columns = Array(
			'DISTINCT(`OwnerID`) AS `DistinctOwnerID`',
			'(SELECT `Email` FROM `Users` WHERE `ID` = `DistinctOwnerID`) AS `Email`',
			'COUNT(`ID`) AS `NumDependUsers`',
			'(SELECT `Params` FROM `TmpData` WHERE `AppID` = "DependUsers.Statistics" AND `UserID` = `DistinctOwnerID` LIMIT 1) AS `Params`',
			'(SELECT `ID` FROM `TmpData` WHERE `AppID` = "DependUsers.Statistics" AND `UserID` = `DistinctOwnerID` LIMIT 1) AS `TmpDataID`',

		);
#-------------------------------------------------------------------------------
$Owners = DB_Select('Users',$Columns,Array('Where'=>'`OwnerID` != 1','GroupBy'=>'OwnerID'));
switch(ValueOf($Owners)){
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
$PreviousMonth = (Date('Y') - 1970)*12 + (integer)Date('n') - 1;
#-------------------------------------------------------------------------------
foreach($Owners as $Owner){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: Обработка владельца #%u',$Owner['DistinctOwnerID']));
	#if($Owner['DistinctOwnerID'] == 2248)
	#if($Owner['DistinctOwnerID'] != 3815)
	#	continue;
	#-------------------------------------------------------------------------------
	# проверяем наличие договора 'NaturalPartner'
	$Where = Array(
			SPrintF('`UserID` = %u',$Owner['DistinctOwnerID']),
			'`TypeID` = "NaturalPartner"',
			);
	#-------------------------------------------------------------------------------
	$Contract = DB_Select('Contracts','*',Array('UNIQ','Where'=>$Where,'Limits'=>Array('Start'=>0,'Length'=>1),'SortOn'=>'ID'));
	switch(ValueOf($Contract)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		# TODO: п хорошему, надо послать клиенту письмо. что ему бы деньги-то начислились, но договора нет. поэтому фиг.
		Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: У клиента (%s) отсутствует договор с типом "NaturalPartner"',$Owner['Email']));
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: У клиента (%s) найден договор #%u, тип (%s)',$Owner['Email'],$Contract['ID'],$Contract['TypeID']));
		#-------------------------------------------------------------------------------
		$MessageToUser = "За прошедший месяц, вам перечислено от ваших рефералов:\n\n";
		#-------------------------------------------------------------------------------
		// массив для добавления в TmpData
		$TmpData = Array(
					'Summ'			=> 0,				// сумма выплаченная юзеру *
					'ReferalsCount'		=> $Owner['NumDependUsers'],	// число рефералов *
					'ReferalsWithPayments'	=> 0,				// число рефералов с платежами
					'ReferalsPaymentsCount'	=> 0,				// число платежей рефералов
					'ReferalsPaymentsSumm'	=> 0,				// сумма платежей реферала
					'ReferalsWorks'		=> 0,				// число выполных работ у рефералов *
					'ReferalsWithWorks'	=> 0,				// число рефералов с выполенными работами
				);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$StartTime	= StrToTime('first day of previous month 00:00');
		$EndTime	= StrToTime('last day of previous month 23:59') + 59;
		#-------------------------------------------------------------------------------
		// число платжей совершённых рефералами
		$Where = Array(
				SPrintF('`UserID` IN (SELECT `ID` FROM `Users` WHERE `OwnerID` = %u)',$Owner['DistinctOwnerID']),
				SPrintF('`StatusDate` > %u',$StartTime),SPrintF('`StatusDate` < %u',$EndTime),'`StatusID` = "Payed"',
				);
		#-------------------------------------------------------------------------------
		$Columns = Array(
				'COUNT(DISTINCT(`UserID`)) AS `ReferalsWithPayments`',
				'COUNT(*) AS `ReferalsPaymentsCount`',
				'IF(ISNULL(SUM(`Summ`)),0,SUM(`Summ`)) AS `ReferalsPaymentsSumm`',
				);
		#-------------------------------------------------------------------------------
		$Invoices = DB_Select(Array('InvoicesOwners'),$Columns,Array('Where'=>$Where,'UNIQ'));
		switch(ValueOf($Invoices)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#-------------------------------------------------------------------------------
			// нет строк. всё по нулям
			$TmpData['ReferalsWithPayments'] = $TmpData['ReferalsPaymentsCount'] = $TmpData['ReferalsPaymentsSumm'] = 0;
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'array':
			#-------------------------------------------------------------------------------
			$TmpData['ReferalsWithPayments'] = $Invoices['ReferalsWithPayments'];
			#-------------------------------------------------------------------------------
			$TmpData['ReferalsPaymentsCount'] = $Invoices['ReferalsPaymentsCount'];
			#-------------------------------------------------------------------------------
			$TmpData['ReferalsPaymentsSumm'] = $Invoices['ReferalsPaymentsSumm'];
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# Выбираем выполенныне за прошлый месяц работы у партнёра
		$Where = Array(
					'`Users`.`ID` = `WorksCompliteOwners`.`UserID`',
					SPrintF('`Users`.`OwnerID` = %u',$Owner['DistinctOwnerID']),
					SPrintF('`Month` = %u',$PreviousMonth),
				);
		#-------------------------------------------------------------------------------
		$Columns = Array(
				'RegisterDate','Email','`WorksCompliteOwners`.*',
				/* если в работах указан номер заказа, то достаём его дату. если номер не указана, возвращаем ноль (1970 год) */
				'IF(`OrderID`>0,(SELECT `CreateDate` FROM `OrdersHistory` WHERE `OrdersHistory`.`OrderID` = `WorksCompliteOwners`.`OrderID`),0) AS `OrderDate`',
				);
		#-------------------------------------------------------------------------------
		$WorksComplites = DB_Select(Array('WorksCompliteOwners','Users'),$Columns,Array('Where'=>$Where));
		switch(ValueOf($WorksComplites)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			break 2;
		case 'array':
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		// для подсчёта ReferalsWithWorks
		$Array = Array();
		#-------------------------------------------------------------------------------
		foreach($WorksComplites as $WorksComplite){
			#-------------------------------------------------------------------------------
			// добавляем счётчик работ
			$TmpData['ReferalsWorks']++;
			#-------------------------------------------------------------------------------
			// добавляем юзера к юзерам с работами (ReferalsWithWorks)
			if(!In_Array($WorksComplite['UserID'],$Array))
				$Array[] = $WorksComplite['UserID'];
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: обработка выполенныех работ юзера (%s), владелец %s',$WorksComplite['Email'],$Owner['Email']));
			#-------------------------------------------------------------------------------
			// проверяем дату регистрации реферала и дату заказа услуги, процент будет разный
			if($WorksComplite['RegisterDate'] > StrToTime('2026-03-01') && $WorksComplite['OrderDate'] > StrToTime('2026-03-01')){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: новый реферал (%s) и услуга (%s), дата регистрации юзера (%s), дата заказа услуги (%s)',$WorksComplite['Email'],$WorksComplite['OrderID'],Date('Y-m-d H:i:s',$WorksComplite['RegisterDate']),Date('Y-m-d H:i:s',$WorksComplite['OrderDate'])));
				#-------------------------------------------------------------------------------
				$Percent = $PartnerPercents[$WorksComplite['ServiceID']];
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: старый реферал/услуга (%s/%s), дата регистрации юзера (%s), дата заказа услуги (%s)',$WorksComplite['Email'],$WorksComplite['OrderID'],Date('Y-m-d H:i:s',$WorksComplite['RegisterDate']),Date('Y-m-d H:i:s',$WorksComplite['OrderDate'])));
				#-------------------------------------------------------------------------------
				$Percent = 5;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			# считаем вознаграждение
			$Reward = Round(($WorksComplite['Amount'] * $WorksComplite['Cost'] - $WorksComplite['Amount'] * $WorksComplite['Cost'] * $WorksComplite['Discont']) * $Percent / 100, 2); 
			#-------------------------------------------------------------------------------
			// добавляем вознаграждение в статситику
			$TmpData['Summ'] = $TmpData['Summ'] + $Reward;
			#-------------------------------------------------------------------------------
			# для админов, общая сумма
			$TotalSumm = $TotalSumm + $Reward;
			#-------------------------------------------------------------------------------
			if($Reward > 0){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: Вознаграждение %s от реферала (%s) составляет %s',$Owner['Email'],$WorksComplite['Email'],$Reward));
				#-------------------------------------------------------------------------------
				# пополняем балланс юзера
				$Comment = SPrintF("Начисления по партнёрской программе за %s, пользователь #%s",date('Y/m',$PreviousTime),$WorksComplite['UserID']);
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$Contract['ID'],'ServiceID'=>'1100','Comment'=>$Comment,'Summ'=>$Reward));
				#-------------------------------------------------------------------------
				switch(ValueOf($Comp)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return ERROR | @Trigger_Error(400);
				case 'array':
					#-------------------------------------------------------------------------------
					# Добавляем текстовое сообщение для юзера
					$MessageToUser = SPrintF("%sНачисления от пользователя #%u составили %01.2f	рублей\n",$MessageToUser,$WorksComplite['UserID'],$Reward);
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
		// считаем уникальных юзеров с работами
		$TmpData['ReferalsWithWorks'] = SizeOf($Array);
		#-------------------------------------------------------------------------------
		// округляем сумму
		$TmpData['Summ'] = Round($TmpData['Summ'],2);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		#Debug("[comp/Tasks/CaclulatePartnersReward]: message for #" . $WorksComplite['UserID'] . " is " . $MessageToUser);
		# если общая сумма больше нуля - надо слать письмо юзеру и строчку сотрудникам
		if(IntVal($TmpData['Summ']) > 0){
			#-------------------------------------------------------------------------------
			$MessageToUser = SPrintF("\n%sИтого, за прошедший месяц: %01.2f	рублей\n",$MessageToUser,$TmpData['Summ']);
			$IsSend = NotificationManager::sendMsg(new Message('PartnersReward',(integer)$Owner['DistinctOwnerID'],Array('Theme'=>$Theme,'Message'=>$MessageToUser,'Summ'=>$TmpData['Summ'])));
			#-------------------------------------------------------------------------------
			switch(ValueOf($IsSend)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				# No more...
			case 'true':
				# No more...
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			# сообщение админам/сотрудникам
			$MessageToAdmins = SPrintF("%sНачислено пользователю [%s]:	%01.2f	рублей\n",$MessageToAdmins,$Owner['Email'],$TmpData['Summ']);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// вносим статистику
		if(Is_Numeric($Owner['TmpDataID'])){
			#-------------------------------------------------------------------------------
			$Owner['Params'][$StartTime] = $TmpData;
			#-------------------------------------------------------------------------------
			// обновляем
			$IsUpdate = DB_Update('TmpData',Array('Params'=>$Owner['Params']),Array('ID'=>$Owner['TmpDataID']));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			// вставляем
			$IsInsert = DB_Insert('TmpData',Array('UserID'=>$Owner['DistinctOwnerID'],'AppID'=>'DependUsers.Statistics','Params'=>Array($StartTime=>$TmpData)));
			if(Is_Error($IsInsert))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
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
// если ничё не не начислено - нефига и письма персоналу слать
if(IntVal($TotalSumm) < 1)
	return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$MessageToAdmins = SPrintF("%s\nИтого: %01.2f рублей",$MessageToAdmins,$TotalSumm);
Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: TotalSumm = %s',$TotalSumm));
Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: MessageToAdmins = %s',$MessageToAdmins));
#-------------------------------------------------------------------------------
# ищем весь персонал
$Entrance = Tree_Entrance('Groups',3000000);
#-------------------------------------------------------------------------------
switch(ValueOf($Entrance)){
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
$String = Implode(',',$Entrance);
#-------------------------------------------------------------------------------
$Employers = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',$String)));
#-------------------------------------------------------------------------------
switch(ValueOf($Employers)){
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
# шлём сообщения персоналу
Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: необходимо отослать письма для (%u) сотрудникам',SizeOf($Employers)));
#-------------------------------------------------------------------------------
foreach($Employers as $Employer){
	#-------------------------------------------------------------------------------
	# выбираем реальных юзеров, системным слать лишнего не надо
	if($Employer['ID'] > 2000 || $Employer['ID'] == 100){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: Необходимо отослать сообщение сотруднику #%u',$Employer['ID']));
		$IsSend = NotificationManager::sendMsg(new Message('PartnersReward',(integer)$Employer['ID'],Array('Theme'=>$Theme,'Message'=>$MessageToAdmins,'Summ'=>SPrintF('%01.2f',$TotalSumm))));
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsSend)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			# No more...
		case 'true':
			# No more...
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
