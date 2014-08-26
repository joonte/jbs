<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','HostingOrderID','HostingSchemeID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/HostingServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$HostingOrder = DB_Select('HostingOrdersOwners',Array('ID','UserID','OrderID','SchemeID','ServerID','Login','(SELECT `Name` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`OldSchemeID`) as `SchemeName`'),Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	$HostingOrderID = (integer)$HostingOrder['ID'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# достаём историю статусов, с сортировкой в обратном направлении
	$Where = Array(
			'`ModeID` = "HostingOrders"',
			SPrintF('`RowID` = %u',$HostingOrderID)
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
			Debug(SPrintF('[comp/Tasks/HostingSchemeChange]: StatusID = %s',$Status['StatusID']));
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
	$HostingNewScheme = DB_Select('HostingSchemes','*',Array('UNIQ','ID'=>$HostingOrder['SchemeID']));
	#-------------------------------------------------------------------------------
	switch(ValueOf($HostingNewScheme)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$User = DB_Select('Users','*',Array('UNIQ','ID'=>$HostingOrder['UserID']));
		#-------------------------------------------------------------------------------
		switch(ValueOf($User)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			$HostingNewScheme['Email'] = $User['Email'];	# add email, for JBS-473
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$ClassHostingServer = new HostingServer();
		#-------------------------------------------------------------------------------
		$IsSelected = $ClassHostingServer->Select((integer)$HostingOrder['ServerID']);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsSelected)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'true':
			#-------------------------------------------------------------------------------
			$GLOBALS['TaskReturnInfo'] = Array($ClassHostingServer->Settings['Address'],$HostingOrder['Login'],$HostingOrder['SchemeName'],$HostingNewScheme['Name']);
			#-------------------------------------------------------------------------------
			#Debug(SPrintF("[comp/Tasks/HostingSchemeChange]: HostingNewScheme = %s",print_r($HostingNewScheme,true)));
			$SchemeChange = $ClassHostingServer->SchemeChange($HostingOrder['Login'],$HostingNewScheme);
			#-------------------------------------------------------------------------------
			switch(ValueOf($SchemeChange)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				#-------------------------------------------------------------------------------
				$IsUpdate = DB_Update('HostingOrders',Array('SchemeID'=>$HostingSchemeID),Array('ID'=>$HostingOrderID));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>$StatusID,'RowsIDs'=>$HostingOrderID,'Comment'=>$SchemeChange->String));
				#-------------------------------------------------------------------------------
				switch(ValueOf($Comp)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return ERROR | @Trigger_Error(400);
				case 'array':
					#-------------------------------------------------------------------------------
					$Event = Array(
							'UserID'	=> $HostingOrder['UserID'],
							'PriorityID'	=> 'Error',
							'Text'		=> SPrintF('Не удалось сменить тарифный план заказу хостинга [%s] в автоматическом режиме, причина (%s)',$HostingOrder['Login'],$SchemeChange->String),
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
					$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>$StatusID,'RowsIDs'=>$HostingOrderID,'Comment'=>IsSet($Message)?$Message:'Тарифный план изменен'));
					#-------------------------------------------------------------------------------
					switch(ValueOf($Comp)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						#-------------------------------------------------------------------------------
					$Event = Array(
							'UserID'	=> $HostingOrder['UserID'],
							'PriorityID'	=> 'Hosting',
							'Text'		=> SPrintF('Успешно изменён тарифный план (%s->%s) заказа на хостинг [%s], сервер (%s)',$HostingOrder['SchemeName'],$HostingNewScheme['Name'],$HostingOrder['Login'],$ClassHostingServer->Settings['Address']),
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
