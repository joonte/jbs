<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','ProxyOrderID','ProxySchemeID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/ProxyServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ProxyOrder = DB_Select('ProxyOrdersOwners',Array('ID','UserID','OrderID','SchemeID','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ProxyOrdersOwners`.`OrderID`) AS `ServerID`','Login','(SELECT `Name` FROM `ProxySchemes` WHERE `ProxySchemes`.`ID` = `ProxyOrdersOwners`.`OldSchemeID`) as `SchemeName`'),Array('UNIQ','ID'=>$ProxyOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ProxyOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	$ProxyOrderID = (integer)$ProxyOrder['ID'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# достаём историю статусов, с сортировкой в обратном направлении
	$Where = Array(
			'`ModeID` = "ProxyOrders"',
			SPrintF('`RowID` = %u',$ProxyOrderID)
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
			Debug(SPrintF('[comp/Tasks/ProxySchemeChange]: StatusID = %s',$Status['StatusID']));
			#-------------------------------------------------------------------------------
			if(In_Array($Status['StatusID'],Array('Active')))
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
	$ProxyNewScheme = DB_Select('ProxySchemes','*',Array('UNIQ','ID'=>$ProxyOrder['SchemeID']));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ProxyNewScheme)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$User = DB_Select('Users','*',Array('UNIQ','ID'=>$ProxyOrder['UserID']));
		#-------------------------------------------------------------------------------
		switch(ValueOf($User)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			$ProxyNewScheme['Email'] = $User['Email'];	# add email, for JBS-473
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$ClassProxyServer = new ProxyServer();
		#-------------------------------------------------------------------------------
		$IsSelected = $ClassProxyServer->Select((integer)$ProxyOrder['ServerID']);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsSelected)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'true':
			#-------------------------------------------------------------------------------
			$GLOBALS['TaskReturnInfo'] = Array(($ClassProxyServer->Settings['Address'])=>Array($ProxyOrder['Login']),$ProxyOrder['SchemeName']=>Array($ProxyNewScheme['Name']));
			#-------------------------------------------------------------------------------
			#Debug(SPrintF("[comp/Tasks/ProxySchemeChange]: ProxyNewScheme = %s",print_r($ProxyNewScheme,true)));
			$SchemeChange = $ClassProxyServer->SchemeChange($ProxyOrder['Login'],$ProxyNewScheme);
			#-------------------------------------------------------------------------------
			switch(ValueOf($SchemeChange)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				#-------------------------------------------------------------------------------
				$IsUpdate = DB_Update('ProxyOrders',Array('SchemeID'=>$ProxySchemeID),Array('ID'=>$ProxyOrderID));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ProxyOrders','StatusID'=>$StatusID,'RowsIDs'=>$ProxyOrderID,'Comment'=>$SchemeChange->String));
				#-------------------------------------------------------------------------------
				switch(ValueOf($Comp)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return ERROR | @Trigger_Error(400);
				case 'array':
					#-------------------------------------------------------------------------------
					$Event = Array(
							'UserID'	=> $ProxyOrder['UserID'],
							'PriorityID'	=> 'Error',
							'Text'		=> SPrintF('Не удалось сменить тарифный план заказу вторичного DNS [%s] в автоматическом режиме, причина (%s)',$ProxyOrder['Login'],$SchemeChange->String),
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
					$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ProxyOrders','StatusID'=>$StatusID,'RowsIDs'=>$ProxyOrderID,'Comment'=>IsSet($Message)?$Message:'Тарифный план изменён'));
					#-------------------------------------------------------------------------------
					switch(ValueOf($Comp)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						#-------------------------------------------------------------------------------
					$Event = Array(
							'UserID'	=> $ProxyOrder['UserID'],
							'PriorityID'	=> 'Hosting',
							'Text'		=> SPrintF('Успешно изменён тарифный план (%s->%s) заказа на вторичный DNS [%s], сервер (%s)',$ProxyOrder['SchemeName'],$ProxyNewScheme['Name'],$ProxyOrder['Login'],$ClassProxyServer->Settings['Address']),
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
