<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/HostingServer.class.php','classes/DNSmanagerServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Where = Array(
		'`Services`.`ID` = `ServersGroups`.`ServiceID`',
		'(`ServersGroups`.`ID` = `Servers`.`ServersGroupID`)',
		'(SELECT `ServiceID` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ServersGroups`.`ID`) = 10000 OR (SELECT `ServiceID` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ServersGroups`.`ID`) = 52000',
		);
#-------------------------------------------------------------------------------
$Columns = Array(
		'`Servers`.`ID`','Address','`Servers`.`IsActive`','`Servers`.`Params`',
		'(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `Servers`.`ServersGroupID`) AS `Name`',
		'(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServersGroups`.`ServiceID`) AS `Code`',
		);
#-------------------------------------------------------------------------------
$Servers = DB_Select(Array('Servers','ServersGroups','Services'),$Columns,Array('Where'=>$Where,'SortOn'=>Array('ServersGroupID','Address')));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return 1800;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Array = Array();
#-------------------------------------------------------------------------------
foreach($Servers as $Server){
	#-------------------------------------------------------------------------------
	#if($Server['Address'] != 'dns0.host-food.ru')
	#	continue;
	#if($Server['Code'] != 'Hosting')
	#	continue;
	#-------------------------------------------------------------------------------
	if(!$Server['IsActive'])
		continue;
	#-------------------------------------------------------------------------------
	# если время последнего опроса задано, и с тех пор прошло меньше 15 минут - пропускаем
	if(IsSet($Server['Params']['LastQuestioning']) && $Server['Params']['LastQuestioning'] > Time() - 1800)
		continue;
	#-------------------------------------------------------------------------------
	$Array[] = $Server;
}
#-------------------------------------------------------------------------------
if(SizeOf($Array) < 1){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/ServersQuestioning]: все сервера опрошены'));
	#-------------------------------------------------------------------------------
	return 1800;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Tasks/ServersQuestioning]: необходимо опросить серверов: %u',SizeOf($Array)));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array();
#-------------------------------------------------------------------------------
$Server = Current($Array);
#-------------------------------------------------------------------------------
if(Is_Null($Server['Name']))
	$Server['Name'] = 'NoGroup';
#-------------------------------------------------------------------------------
if(!IsSet($GLOBALS['TaskReturnInfo'][$Server['Name']]))
	$GLOBALS['TaskReturnInfo'][$Server['Name']] = Array();
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'][$Server['Name']][] = $Server['Address'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ClassName = SPrintF('%sServer',$Server['Code']);
#-------------------------------------------------------------------------------
$ClassServer = new $ClassName();
#-------------------------------------------------------------------------------
$IsSelected = $ClassServer->Select((integer)$Server['ID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsSelected)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'true':
	#-------------------------------------------------------------------------------
	$Users = $ClassServer->GetDomains();
	#-------------------------------------------------------------------------------
	switch(ValueOf($Users)){
	case 'error':
		# No more...
		break 2;
	case 'exception':
		# No more...
		break 2;
	case 'array':
		#-------------------------------------------------------------------------------
		if(Count($Users)){
			#-------------------------------------------------------------------------------
			$Array = Array();
			#-------------------------------------------------------------------------------
			foreach(Array_Keys($Users) as $UserID)
				$Array[] = SPrintF("'%s'",$UserID);
			#-------------------------------------------------------------------------------
			$Where = SPrintF('`ServerID` = %u AND `Login` IN (%s)',$Server['ID'],Implode(',',$Array));
			#-------------------------------------------------------------------------------
			$Orders = DB_Select(SPrintF('%sOrdersOwners',$Server['Code']),Array('ID','Login'),Array('Where'=>$Where));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Orders)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				# No more...
				break;
			case 'array':
				#-------------------------------------------------------------------------------
				foreach($Orders as $Order){
					#-------------------------------------------------------------------------------
					$Parked = $Users[$Order['Login']];
					#-------------------------------------------------------------------------------
					$IsUpdate = DB_Update(SPrintF('%sOrders',$Server['Code']),Array('Domain'=>(Count($Parked)?Current($Parked):'not-found'),'Parked'=>Implode(',',$Parked)),Array('ID'=>$Order['ID']));
					if(Is_Error($IsUpdate))
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
		}
		#-------------------------------------------------------------------------------
		break 2;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Server['Params']['LastQuestioning'] = Time();
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('Servers',Array('Params'=>$Server['Params']),Array('ID'=>$Server['ID']));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return 30;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>