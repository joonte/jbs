<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$DNSmanagerOrderID	= (integer) @$Args['DNSmanagerOrderID'];
$NewSchemeID		= (integer) @$Args['NewSchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','SchemeID','StatusDate','(SELECT `ServersGroupID` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`)) AS `ServersGroupID`','(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`)) AS `Params`','StatusID');
#-------------------------------------------------------------------------------
$DNSmanagerOrder = DB_Select('DNSmanagerOrdersOwners',$Columns,Array('UNIQ','ID'=>$DNSmanagerOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('HOSTING_ORDER_NOT_FOUND','Выбранный заказ не найден');
case 'array':
	#-------------------------------------------------------------------------------
	$__USER = $GLOBALS['__USER'];
	#-------------------------------------------------------------------------------
	$IsPermission = Permission_Check('DNSmanagerOrdersSchemeChange',(integer)$__USER['ID'],(integer)$DNSmanagerOrder['UserID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsPermission)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'false':
		return ERROR | @Trigger_Error(700);
	case 'true':
		#-------------------------------------------------------------------------------
		if(!In_Array($DNSmanagerOrder['StatusID'],Array('Active','Suspended')))
			return new gException('ORDER_NOT_ACTIVE','Тариф можно изменить только для активного или заблокированного заказа');
		#-------------------------------------------------------------------------------
		if(!$__USER['IsAdmin']){
			#-------------------------------------------------------------------------------
			$LastChange = Time() - $DNSmanagerOrder['StatusDate'];
			#-------------------------------------------------------------------------------
			if($LastChange < 86400){
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('Formats/Date/Remainder',$LastChange);
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				#return new gException('TIME_NOT_EXPIRED',SPrintF('Тарифный план можно менять только 1 раз в сутки, сменить тарифный план можно только через %s, однако, в случае необходимости Вы можете обратиться в службу поддержки',$Comp));
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$OldScheme = DB_Select('DNSmanagerSchemes',Array('IsSchemeChange','Name','IsProlong','ID'),Array('UNIQ','ID'=>$DNSmanagerOrder['SchemeID']));
		#-------------------------------------------------------------------------------
		switch(ValueOf($OldScheme)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			if(!$OldScheme['IsSchemeChange'])
				return new gException('SCHEME_NOT_ALLOW_SCHEME_CHANGE','Тарифный план вторичный DNS не позволяет смену тарифа');
			#-------------------------------------------------------------------------------
			$UniqID = UniqID('DNSmanagerSchemes');
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Services/Schemes','DNSmanagerSchemes',$DNSmanagerOrder['UserID'],Array('Name','ServersGroupID'),$UniqID);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$NewScheme = DB_Select($UniqID,Array('ID','ServersGroupID','IsSchemeChangeable','Name'),Array('UNIQ','ID'=>$NewSchemeID));
			#-------------------------------------------------------------------------------
			switch(ValueOf($NewScheme)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return new gException('NEW_SCHEME_NOT_FOUND','Новый тарифный план не найден');
			case 'array':
				#-------------------------------------------------------------------------------
				if($DNSmanagerOrder['SchemeID'] == $NewScheme['ID'])
					return new gException('SCHEMES_MATCHED','Старый и новый тарифные планы совпадают');
				#-------------------------------------------------------------------------------
				if(!$NewScheme['IsSchemeChangeable'])
					return new gException('SCHEME_NOT_CHANGEABLE','Выбранный тариф не позволяет переход');
				#-------------------------------------------------------------------------------
				#-------------------------------------------------------------------------------
				if($DNSmanagerOrder['ServersGroupID'] != $NewScheme['ServersGroupID'])
					return new gException('NEW_SCHEME_ANOTHER_SERVERS_GROUP','Выбранный тарифный план относиться к другой группе серверов');
				#-------------------------------------------------------------------------------
				$DNSmanagerOrderID = (integer)$DNSmanagerOrder['ID'];
				#-------------------------------------------------------------------------------
				#--------------------------TRANSACTION------------------------------------------
				if(Is_Error(DB_Transaction($TransactionID = UniqID('DNSmanagerOrderSchemeChange'))))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$DNSmanagerOrder['UserID'],'TypeID'=>'DNSmanagerSchemeChange','Params'=>Array($DNSmanagerOrderID,$DNSmanagerOrder['SchemeID'])));
				#-------------------------------------------------------------------------------
				switch(ValueOf($IsAdd)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return ERROR | @Trigger_Error(400);
				case 'array':
					#-------------------------------------------------------------------------------
					$IsUpdate = DB_Update('DNSmanagerOrders',Array('SchemeID'=>$NewSchemeID,'OldSchemeID'=>$OldScheme['ID']),Array('ID'=>$DNSmanagerOrderID));
					if(Is_Error($IsUpdate))
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DNSmanagerOrders','StatusID'=>'SchemeChange','RowsIDs'=>$DNSmanagerOrderID,'Comment'=>SPrintF('Смена тарифа [%s->%s]',$OldScheme['Name'],$NewScheme['Name'])));
					#-------------------------------------------------------------------------------
					switch(ValueOf($Comp)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						#-------------------------------------------------------------------------------
						if(Is_Error(DB_Commit($TransactionID)))
							return ERROR | @Trigger_Error(500);
						#----------------------END TRANSACTION------------------------------------------
						return Array('Status'=>'Ok');
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
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
