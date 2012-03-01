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

# если партнёрка не включена - ничё не делаем и проверка через час
if($Settings['IsActive'] != 1){
	return(time() + 3600);
}

# vars
$total_summ = 0;
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
			Debug("[comp/Tasks/CaclulatePartnersReward]: For owner [" . $Owner['Email'] . "] found #" . $Contracts['ID'] . " contract with type " . $Contracts['TypeID']);
			$MessageToUser = "За прошедший месяц, вам перечислено от ваших рефералов:\n\n";
			$TotalSummToUser = 0;
			# Select depend users complites works
			#-------------------------------------------------------------------------------
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
				$Reward = round(($Row['Amount'] * $Row['Cost'] - $Row['Amount'] * $Row['Cost'] * $Row['Discont']) * $Settings['PartnersRewardPercent'] / 100, 2);
				# to admins
				$total_summ = $total_summ + $Reward;
				#-------------------------------------------------------------------------------
				if($Reward > 0){
					Debug("[comp/Tasks/CaclulatePartnersReward]: Reward for [" . $Owner['Email'] . "] from #" . $Row['UserID'] . ' = ' . $Reward);
					# fill user ballance
					$Comment = "Начисления по партнёрской программе за " . date('Y/m',MkTime(4,0,0,Date('n')-1,5,Date('Y'))) . ", пользователь #" . $Row['UserID'];
							
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
						$MessageToUser .= "Начисления от пользователя #" . $Row['UserID'] . "	составили " . $Reward . "	рублей\n";
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
			if($TotalSummToUser > 0){
				$MessageToUser .= "Итого, за прошедший месяц: " . $TotalSummToUser . " рублей";
				$IsSend = NotificationManager::sendMsg('PartnersReneward',(integer)$Owner['DistinctOwnerID'],Array('Theme'=>$Theme,'Message'=>$MessageToUser));
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
				$MessageToAdmins .= "Начислено пользователю [" . $Owner['Email'] . "]:	" . $TotalSummToUser . "	рублей\n";
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

$MessageToAdmins .= "\nИтого: " . $total_summ . " рублей";
Debug("[comp/Tasks/CaclulatePartnersReward]: Total reward summ = " . $total_summ);
Debug("[comp/Tasks/CaclulatePartnersReward]: Admin message = " . $MessageToAdmins);

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
				$IsSend = NotificationManager::sendMsg('PartnersReneward',(integer)$Employer['ID'],Array('Theme'=>$Theme,'Message'=>$MessageToAdmins));
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
return MkTime(4,0,0,Date('n')+1,5,Date('Y'));
#-------------------------------------------------------------------------------


?>
