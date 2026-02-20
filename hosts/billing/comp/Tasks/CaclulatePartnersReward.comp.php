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
$Columns = Array(
			'DISTINCT(`OwnerID`) AS `DistinctOwnerID`',
			'(SELECT `Email` FROM `Users` WHERE `ID` = `DistinctOwnerID`) AS `Email`',
			'COUNT(`ID`) AS `NumDependUsers`',
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
	#Debug("[comp/Tasks/CaclulatePartnersReward]: Processing owner #" . $Owner['DistinctOwnerID']);
#if($Owner['DistinctOwnerID'] == 2248){
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
		break;
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: У клиента (%s) найден договор #%u, тип (%s)',$Owner['Email'],$Contract['ID'],$Contract['TypeID']));
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
		$MessageToUser = "За прошедший месяц, вам перечислено от ваших рефералов:\n\n";
		#-------------------------------------------------------------------------------
		$TotalSummToUser = 0;
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
				/* если указан номер заказа, то достаём его дату. если номер не указана, возвращаем ноль (1970 год) */
				'IF(`OrderID`>0,(SELECT `OrderDate` FROM `Orders` WHERE `Orders`.`ID` = `OrderID`),0) AS `OrderDate`',
				);
		#-------------------------------------------------------------------------------
		$WorksComplites = DB_Select(Array('WorksCompliteOwners','Users'),$Columns,Array('Where'=>Array('`Users`.`ID`=`WorksCompliteOwners`.`UserID`','`Users`.`OwnerID`=2248','`Month`=642')));
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
		foreach($WorksComplites as $WorksComplite){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: обработка выполенныех работ юзера (%s), владелец %s',$WorksComplite['Email'],$Owner['Email']));
			#-------------------------------------------------------------------------------
			// проверяем дату регистрации реферала и дату заказа услуги
			if($WorksComplite['RegisterDate'] > StrToTime('2026-03-01') && $WorksComplite['OrderDate'] > StrToTime('2026-03-01')){
				#-------------------------------------------------------------------------------
				$Percent = $PartnerPercents[$WorksComplite['ServiceID']];
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: старый реферал/услуга (%s/%s), дата регистрации юзера (%s), дата заказа услуги (%s)',$WorksComplite['Email'],$WorksComplite['OrderID'],Date('Y-m-d H:i:s',$WorksComplite['RegisterDate'],$WorksComplite['OrderDate'])));
				#-------------------------------------------------------------------------------
				$Percent = 5;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			# считаем вознаграждение
			$Reward = Round(($WorksComplite['Amount'] * $WorksComplite['Cost'] - $WorksComplite['Amount'] * $WorksComplite['Cost'] * $WorksComplite['Discont']) * $Percent / 100, 2); 
			#-------------------------------------------------------------------------------
			# для админов, общая сумма
			$TotalSumm = $TotalSumm + $Reward;
			#-------------------------------------------------------------------------------
			if($Reward > 0){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: Вознаграждение %s от реферала (%s) составляет %s',$Owner['Email'],$WorksComplite['Email'],$Reward));
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
					# добавляем к общей сумме
					$TotalSummToUser = $TotalSummToUser + $Reward;
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
		#Debug("[comp/Tasks/CaclulatePartnersReward]: message for #" . $WorksComplite['UserID'] . " is " . $MessageToUser);
		# если общая сумма больше нуля - надо слать письмо
		if(IntVal($TotalSummToUser) > 0){
			#-------------------------------------------------------------------------------
			$MessageToUser = SPrintF("%sИтого, за прошедший месяц: %01.2f	рублей\n",$MessageToUser,$TotalSummToUser);
			$IsSend = NotificationManager::sendMsg(new Message('PartnersReward',(integer)$Owner['DistinctOwnerID'],Array('Theme'=>$Theme,'Message'=>$MessageToUser,'Summ'=>SPrintF('%01.2f',$TotalSummToUser))));
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
			# сообщение админам/сотрудникам
			$MessageToAdmins = SPrintF("%sНачислено пользователю [%s]:	%01.2f	рублей\n",$MessageToAdmins,$Owner['Email'],$TotalSummToUser);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
#}
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// если ничё не не начислено - нефига и письма персоналу слать
if(IntVal($TotalSumm) < 1)
	return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$MessageToAdmins = SPrintF("\nИтого: %01.2f рублей",$MessageToAdmins,$TotalSumm);
Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: Total reward summ = %s',$TotalSumm));
Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: Admin message = %s',$MessageToAdmins));
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
