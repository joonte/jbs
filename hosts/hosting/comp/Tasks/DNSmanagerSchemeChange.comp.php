<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','DNSmanagerOrderID','DNSmanagerSchemeID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/DNSmanagerServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DNSmanagerOrder = DB_Select('DNSmanagerOrdersOwners',Array('ID','UserID','OrderID','SchemeID','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`) AS `ServerID`','Login','(SELECT `Name` FROM `DNSmanagerSchemes` WHERE `DNSmanagerSchemes`.`ID` = `DNSmanagerOrdersOwners`.`OldSchemeID`) as `SchemeName`'),Array('UNIQ','ID'=>$DNSmanagerOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	$DNSmanagerOrderID = (integer)$DNSmanagerOrder['ID'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# достаём историю статусов, с сортировкой в обратном направлении
	$Where = Array(
			'`ModeID` = "DNSmanagerOrders"',
			SPrintF('`RowID` = %u',$DNSmanagerOrderID)
			);
	#-------------------------------------------------------------------------------
	$StatusesHistory = DB_Select('StatusesHistory','StatusID',Array('Where'=>$Where,'SortOn'=>'ID'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($StatusesHistory)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		$StatusID = 'Active';
		$Message  = 'История статусов не найдена, установлен статус по умолчанию';
	case 'array':
		#-------------------------------------------------------------------------------
		foreach($StatusesHistory as $Status){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/DNSmanagerSchemeChange]: StatusID = %s',$Status['StatusID']));
			#-------------------------------------------------------------------------------
			if(In_Array($Status['StatusID'],Array('Active','Suspended')))
				$StatusID = $Status['StatusID'];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		# не найден подходящий статус - ставим активный, и сообщение
		if(!IsSet($StatusID)){
			#-------------------------------------------------------------------------------
			$StatusID = 'Active';
			$Message  = 'Предыдущий статус не найден, установлен статус по умолчанию';
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
	$DNSmanagerNewScheme = DB_Select('DNSmanagerSchemes','*',Array('UNIQ','ID'=>$DNSmanagerOrder['SchemeID']));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DNSmanagerNewScheme)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$User = DB_Select('Users','*',Array('UNIQ','ID'=>$DNSmanagerOrder['UserID']));
		#-------------------------------------------------------------------------------
		switch(ValueOf($User)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			$DNSmanagerNewScheme['Email'] = $User['Email'];	# add email, for JBS-473
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$ClassDNSmanagerServer = new DNSmanagerServer();
		#-------------------------------------------------------------------------------
		$IsSelected = $ClassDNSmanagerServer->Select((integer)$DNSmanagerOrder['ServerID']);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsSelected)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'true':
			#-------------------------------------------------------------------------------
			$GLOBALS['TaskReturnInfo'] = Array($ClassDNSmanagerServer->Settings['Address'],$DNSmanagerOrder['Login'],$DNSmanagerOrder['SchemeName'],$DNSmanagerNewScheme['Name']);
			#-------------------------------------------------------------------------------
			#Debug(SPrintF("[comp/Tasks/DNSmanagerSchemeChange]: DNSmanagerNewScheme = %s",print_r($DNSmanagerNewScheme,true)));
			$SchemeChange = $ClassDNSmanagerServer->SchemeChange($DNSmanagerOrder['Login'],$DNSmanagerNewScheme);
			#-------------------------------------------------------------------------------
			switch(ValueOf($SchemeChange)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				#-------------------------------------------------------------------------------
				$IsUpdate = DB_Update('DNSmanagerOrders',Array('SchemeID'=>$DNSmanagerSchemeID),Array('ID'=>$DNSmanagerOrderID));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DNSmanagerOrders','StatusID'=>$StatusID,'RowsIDs'=>$DNSmanagerOrderID,'Comment'=>$SchemeChange->String));
				#-------------------------------------------------------------------------------
				switch(ValueOf($Comp)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return ERROR | @Trigger_Error(400);
				case 'array':
					#-------------------------------------------------------------------------------
					$Event = Array(
							'UserID'	=> $DNSmanagerOrder['UserID'],
							'PriorityID'	=> 'Error',
							'Text'		=> SPrintF('Не удалось сменить тарифный план заказу вторичного DNS [%s] в автоматическом режиме, причина (%s)',$DNSmanagerOrder['Login'],$SchemeChange->String),
							'IsReaded'	=> FALSE
							);
					#-------------------------------------------------------------------------------
					$Event = Comp_Load('Events/EventInsert',$Event);
					if(!$Event)
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					return TRUE;
					#-------------------------------------------------------------------------------
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------------
				case 'true':
					#-------------------------------------------------------------------------------
					$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DNSmanagerOrders','StatusID'=>$StatusID,'RowsIDs'=>$DNSmanagerOrderID,'Comment'=>IsSet($Message)?$Message:'Тарифный план изменён'));
					#-------------------------------------------------------------------------------
					switch(ValueOf($Comp)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						#-------------------------------------------------------------------------------
					$Event = Array(
							'UserID'	=> $DNSmanagerOrder['UserID'],
							'PriorityID'	=> 'Hosting',
							'Text'		=> SPrintF('Успешно изменён тарифный план (%s->%s) заказа на вторичный DNS [%s], сервер (%s)',$DNSmanagerOrder['SchemeName'],$DNSmanagerNewScheme['Name'],$DNSmanagerOrder['Login'],$ClassDNSmanagerServer->Settings['Address']),
							);
					$Event = Comp_Load('Events/EventInsert',$Event);
					if(!$Event)
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					return TRUE;
					#-------------------------------------------------------------------------------
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
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

?>
