<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['GC']['CheckOrphanedDomainOwners'];
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TRUE;
#-------------------------------------------------------------------------------
if(Date('N') != $Settings['DayOfWeek'])
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array('ID','DomainName','(SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`) as `DomainZone`','UserID');
#-------------------------------------------------------------------------------
$Where = Array('`StatusID` = "Active"', 'Ns1Name LIKE "%.host-food.ru"');
#-------------------------------------------------------------------------------
$Domains = DB_Select('DomainOrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Domains)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Snatchers = Array();
#-------------------------------------------------------------------------------
$Count1 = 0;
#-------------------------------------------------------------------------------
foreach($Domains as $Domain){
	#-------------------------------------------------------------------------------
	$DomainName = SPrintF('%s.%s',$Domain['DomainName'],$Domain['DomainZone']);
	#-------------------------------------------------------------------------------
	#Debug(SPrintF('[comp/Tasks/GC/CheckOrphanedDomainOwners]: domain = %s; UserID = %u',$DomainName,$Domain['UserID']));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Count1++;
	#-------------------------------------------------------------------------------
	# проверяем домен на заказах хостинга
	$Where = Array(
			'`StatusID` = "Active"',
			SPrintF('`UserID` != %u',$Domain['UserID']),
			SPrintF("`Parked` LIKE '%%%s%%'",$DomainName)
			);
	#-------------------------------------------------------------------------------
	$Count = DB_Count('HostingOrdersOwners',Array('Where'=>$Where));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Count){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/GC/CheckOrphanedDomainOwners]: domain found on hosting with not some owner = %s; SizeOf($Domains) = %u; Count = %u',$DomainName,SizeOf($Domains),$Count1));
		#-------------------------------------------------------------------------------
		$HostingOrder = DB_Select('HostingOrdersOwners',Array('Login','UserID'),Array('Where'=>$Where,'UNIQ','Limits'=>Array(0,1)));
		#-------------------------------------------------------------------------------
		switch(ValueOf($HostingOrder)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			# No more...
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		if(IsSet($Snatchers[$HostingOrder['UserID']])){
			#-------------------------------------------------------------------------------
			$Snatchers[$HostingOrder['UserID']][] = $DomainName;
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$Snatchers[$HostingOrder['UserID']] = Array($DomainName);
			#-------------------------------------------------------------------------------
		}
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# проверяем домен на заказах VPS
	$Where = Array(
			'`StatusID` = "Active"',
			SPrintF('`UserID` != %u',$Domain['UserID']),
			SPrintF("`Parked` LIKE '%%%s%%'",$DomainName)
			);
	#-------------------------------------------------------------------------------
	$Count = DB_Count('DNSmanagerOrdersOwners',Array('Where'=>$Where));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Count){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/GC/CheckOrphanedDomainOwners]: domain found on VPS with not some owner = %s; SizeOf($Domains) = %u; Count = %u',$DomainName,SizeOf($Domains),$Count1));
		#-------------------------------------------------------------------------------
		$DNSmanagerOrder = DB_Select('DNSmanagerOrdersOwners',Array('Login','UserID'),Array('Where'=>$Where,'UNIQ','Limits'=>Array(0,1)));
		#-------------------------------------------------------------------------------
		switch(ValueOf($DNSmanagerOrder)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			# No more...
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		if(IsSet($Snatchers[$DNSmanagerOrder['UserID']])){
			#-------------------------------------------------------------------------------
			$Snatchers[$DNSmanagerOrder['UserID']][] = $DomainName;
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$Snatchers[$DNSmanagerOrder['UserID']] = Array($DomainName);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# ищщем сотрудников указанной группы
$Employers = Array();
#-------------------------------------------------------------------------------
foreach(Explode(',',$Config['SendToGroupIDs']) as $SendToGroupID){
	#-------------------------------------------------------------------------------
	$Entrance = Tree_Entrance('Groups',$SendToGroupID);
	#-------------------------------------------------------------------------------
	switch(ValueOf($Entrance)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$String = Implode(',',$Entrance);
		#-------------------------------------------------------------------------------
		$Users = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',$String)));
		#---------------------------------------------------------------
		#-------------------------------------------------------------------------------
		switch(ValueOf($Users)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			Debug(SPrintF("[comp/Tasks/GC/CheckOrphanedDomainOwners]: не найдено сотрудников отдела %s",$SendToGroupID));
			break;
		case 'array':
			#-------------------------------------------------------------------------------
			Debug(SPrintF("[comp/Tasks/GC/CheckOrphanedDomainOwners]: найдено %s сотрудников отдела %s",SizeOf($Users),$SendToGroupID));
			#-------------------------------------------------------------------------------
			foreach($Users as $User)
				if(!In_Array($User['ID'],$Employers))
					$Employers[] = $User['ID'];
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
# если массив оказался пуст - найти всех сотрудников, раз нет сотрудников в заданной группе
if(SizeOf($Employers) < 1){
	#-------------------------------------------------------------------------------
	$Entrance = Tree_Entrance('Groups',3000000);
	#-------------------------------------------------------------------------------
	switch(ValueOf($Entrance)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$String = Implode(',',$Entrance);
		#-------------------------------------------------------------------------------
		$Users = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',$String)));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Users)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			Debug(SPrintF("[comp/Tasks/GC/CheckOrphanedDomainOwners]: найдено %s сотрудников любых отделов",SizeOf($Users)));
			#-------------------------------------------------------------------------------
			foreach($Users as $User)
				if(!In_Array($User['ID'],$Employers))
					$Employers[] = $User['ID'];
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
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
$Theme = "Проверка чужих доменов у пользователей";
$Message = "";
#-------------------------------------------------------------------------------
# перебираем юзеров с чужими доменами
foreach(Array_Keys($Snatchers) as $UserID){
	#-------------------------------------------------------------------------------
	# если у юзера чужих доменов больше чем лимит - шлём письмо
	if(SizeOf($Snatchers[$UserID]) > $Settings['Limit']){
		#-------------------------------------------------------------------------------
		$User = DB_Select('Users',Array('Email','ID'),Array('ID'=>$UserID,'UNIQ'));
		if(Is_Error($User))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/GC/CheckOrphanedDomainOwners]: Email = %s; Domains = %u',$User['Email'],SizeOf($Snatchers[$UserID])));
		#-------------------------------------------------------------------------------
		$Message = SPrintF("%s\nПользователь %s, чужих доменов %u:\n%s\n",$Message,$User['Email'],SizeOf($Snatchers[$UserID]),Implode("\n",$Snatchers[$UserID]));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# если письмо пустое - ничего не шлём
if(StrLen($Message) < 20)
	return TRUE;

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($Employers as $Employer){
	#-------------------------------------------------------------------------------
	$msg = new DispatchMsg(Array('Theme'=>$Theme,'Message'=>$Message), (integer)$Employer['ID'], 100 /*$FromID*/);
	#-------------------------------------------------------------------------------
	$IsSend = NotificationManager::sendMsg($msg);
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsSend)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
	case 'true':
		#-------------------------------------------------------------------------------
		# No more...
		Debug(SPrintF("[comp/Tasks/GC/CheckOrphanedDomainOwners]: Сообщение для сотрудника #%s отослано",$Employer['ID']));
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
