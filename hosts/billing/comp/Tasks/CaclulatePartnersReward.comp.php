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
$MessageToAdmins = "Начисления по реферальной программе за прошлый месяц:\n\n";
$MonthsNames = Array('Декабрь','Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
$PreviousTime = MkTime(4,0,0,Date('n')-1,5,Date('Y'));
$PreviousYear = date('Y',$PreviousTime);
$PreviousMonthName = $MonthsNames[date('n',$PreviousTime)];
$Theme = "Начисления по партнёрской программе за " . SPrintF("%s %u г.",$PreviousMonthName,$PreviousYear);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# create temporary table
$Result = DB_Query("CREATE TEMPORARY TABLE `UsrOwners` SELECT DISTINCT `OwnerID` as `DistinctOwnerID`, (SELECT `Email` FROM `Users` WHERE `ID`=`DistinctOwnerID`) AS `Email`, COUNT(`ID`) AS `NumDependUsers` FROM `Users` WHERE `OwnerID`!=1 GROUP BY `OwnerID`");
#-------------------------------------------------------------------------------
if(Is_Error($Result))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Owners = DB_Select('UsrOwners','*');
switch(ValueOf($Owners)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#---------------------------------------------------------------------------
	$PreviousMonth = (Date('Y') - 1970)*12 + (integer)Date('n') - 1;
	foreach($Owners as $Owner){
		#-------------------------------------------------------------------------------
		#Debug("[comp/Tasks/CaclulatePartnersReward]: Processing owner #" . $Owner['DistinctOwnerID']);
#if($Owner['DistinctOwnerID'] == 2248){
		#-------------------------------------------------------------------------------
		# select owner contract with type 'NaturalPartner'
		$Where = "`UserID`=" . $Owner['DistinctOwnerID'] . " AND `TypeID`='NaturalPartner'";
		$Contracts = DB_Select('Contracts','*',Array('UNIQ','Where'=>$Where,'Limits'=>Array('Start'=>0,'Length'=>1),'SortOn'=>'ID'));
		switch(ValueOf($Contracts)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			# No more...
			Debug("[comp/Tasks/CaclulatePartnersReward]: For owner [" . $Owner['Email'] . "] not found 'NaturalPartner' contract");
			# TODO: need send email to this user, about it's exceptions
			break;
		case 'array':
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: For owner [%s] found #%u contract with type %s',$Owner['Email'],$Contracts['ID'],$Contracts['TypeID']));
			#-------------------------------------------------------------------------------
			# выбираем значения партнёрских процентов по каждой услуге
			# требует такого патча на базу:
			#-------------------------------------------------------------------------------
			$PartnerPercents = Array();
			#-------------------------------------------------------------------------------
			$Services = DB_Select('Services','*');
			switch(ValueOf($Services)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				#-------------------------------------------------------------------------------
				foreach($Services as $Service)
					$PartnerPercents[$Service['ID']] = ($Service['PartnersRewardPercent'] < 0)?$Settings['PartnersRewardPercent']:$Service['PartnersRewardPercent'];
				#------------------------------------------------------------------------------- 
				break;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$MessageToUser = "За прошедший месяц, вам перечислено от ваших рефералов:\n\n";
			$TotalSummToUser = 0;
			#-------------------------------------------------------------------------------
			# Select depend users complites works
			$Query = "SELECT `WorksCompliteOwners`.* FROM `WorksCompliteOwners`,`Users` WHERE `Users`.`ID`=`WorksCompliteOwners`.`UserID` AND `Users`.`OwnerID`=%s AND `Month`=%s";
			$Result = DB_Query(SPrintF($Query, $Owner['DistinctOwnerID'], $PreviousMonth));
			#-------------------------------------------------------------------------------
			if(Is_Error($Result))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Rows = MySQL::Result($Result);
			if(Is_Error($Rows))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			foreach($Rows as $Row){
				Debug("[comp/Tasks/CaclulatePartnersReward]: Processing user #" . $Row['UserID'] . " with owner [" . $Owner['Email'] . "]");
				# calculate user Reward
				$Reward = Round(($Row['Amount'] * $Row['Cost'] - $Row['Amount'] * $Row['Cost'] * $Row['Discont']) * $PartnerPercents[$Row['ServiceID']] / 100, 2); 
				# to admins
				$TotalSumm = $TotalSumm + $Reward;
				#-------------------------------------------------------------------------------
				if($Reward > 0){
					Debug("[comp/Tasks/CaclulatePartnersReward]: Reward for [" . $Owner['Email'] . "] from #" . $Row['UserID'] . ' = ' . $Reward);
					# fill user ballance
					$Comment = SPrintF("Начисления по партнёрской программе за %s, пользователь #%s",date('Y/m',$PreviousTime),$Row['UserID']);
					#-------------------------------------------------------------------------------
					$Comp = Comp_Load('www/Administrator/API/PostingMake',
							Array(	'ContractID'	=>$Contracts['ID'],
								'ServiceID'	=>'1100',
								'Comment'	=>$Comment,
								'Summ'		=>$Reward
								)
							);
					#-------------------------------------------------------------------------
					switch(ValueOf($Comp)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						# add text to message
						$MessageToUser .= "Начисления от пользователя #" . $Row['UserID'] . "	составили " . SPrintf('%01.2f',$Reward) . "	рублей\n";
						# add summ to total
						$TotalSummToUser = $TotalSummToUser + $Reward;
						# No more...
						break;
					default:
						return ERROR | @Trigger_Error(101);
					}
					#-------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------
			}
			# 
			#Debug("[comp/Tasks/CaclulatePartnersReward]: message for #" . $Row['UserID'] . " is " . $MessageToUser);
			# если общая сумма больше нуля - надо слать письмо
			if(IntVal($TotalSummToUser) > 0){
				$MessageToUser .= "Итого, за прошедший месяц: " . SPrintF('%01.2f',$TotalSummToUser) . " рублей";
				$IsSend = NotificationManager::sendMsg(new Message('PartnersReward',(integer)$Owner['DistinctOwnerID'],Array('Theme'=>$Theme,'Message'=>$MessageToUser,'Summ'=>SPrintF('%01.2f',$TotalSummToUser))));
				#---------------------------------------------------------
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
				# to admins
				$MessageToAdmins .= "Начислено пользователю [" . $Owner['Email'] . "]:	" . SPrintF('%01.2f',$TotalSummToUser) . "	рублей\n";
			}
			#-------------------------------------------------------------------------------
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}

#	$WorksComplites = DB_Select('WorksCompliteOwners','*',Array('Where'=>'`UserID`=$User');
#}
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------
#-------------------------------------------------------------------
$MessageToAdmins .= SPrintF("\nИтого: %01.2f рублей", $TotalSumm);
Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: Total reward summ = %s',$TotalSumm));
Debug(SPrintF('[comp/Tasks/CaclulatePartnersReward]: Admin message = %s',$MessageToAdmins));
#-------------------------------------------------------------------
# search all personal
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
		# No more...
		break;
	case 'array':
		# send messages to personal
		Debug("[comp/Tasks/CaclulatePartnersReward]: Need send messages to users = " . SizeOf($Employers));
		foreach($Employers as $Employer){
			# select real users
			if($Employer['ID'] > 2000 || $Employer['ID'] == 100){
				Debug("[comp/Tasks/CaclulatePartnersReward]: Need send messages to #" . (integer)$Employer['ID']);
				$IsSend = NotificationManager::sendMsg(new Message('PartnersReward',(integer)$Employer['ID'],Array('Theme'=>$Theme,'Message'=>$MessageToAdmins,'Summ'=>SPrintF('%01.2f',$TotalSumm))));
				#---------------------------------------------------------
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
			}
		}
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $ExecuteTime;
#-------------------------------------------------------------------------------


?>
