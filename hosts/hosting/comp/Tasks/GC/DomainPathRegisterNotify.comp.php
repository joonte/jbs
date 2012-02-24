<?php


#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Where = Array(
		'UNIX_TIMESTAMP() - `CreateDate` > 31 * 24 * 3600',
		"`TypeID` = 'DomainPathRegister'",
		"`IsExecuted` = 'no'"
		);
#-------------------------------------------------------------------------------
$Tasks = DB_Select('Tasks',Array('ID','Params','UserID'),Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Tasks)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	Debug("[comp/Tasks/GC/DomainPathRegisterNotify]: нет доменов где владелец не определён более 30 дней");
	return TRUE;
case 'array':
	foreach($Tasks as $Task){
		#-------------------------------------------------------------------------------
		$Params = (array)$Task['Params'];
		#-------------------------------------------------------------------------------
		$Columns = Array('DomainName','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrdersOwners`.`SchemeID`) as `DomainZone`','UserID');
		#-------------------------------------------------------------------------------
		$DomainOrder = DB_Select('DomainsOrdersOwners',$Columns,Array('UNIQ','ID'=>$Params['ID']));
		switch(ValueOf($DomainOrder)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			# TODO проверить, может быть имеет смысл вешать событие, с указанием номера таска...
			return new gException('DOMAIN_ORDER_NOT_FOUND','Заказ домена не найден');
		case 'array':
			#-------------------------------------------------------------------------------
			$Event = Array(
					'UserID'        => $DomainOrder['UserID'],
					'PriorityID'    => 'Hosting',
					'Text'          => SPrintF('Владелец домена %s.%s не определён более 30 дней',$DomainOrder['DomainName'],$DomainOrder['DomainZone']),
					'IsReaded'      => FALSE
					);
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
	}
	return TRUE;
default:
	return ERROR | @Trigger_Error(101);
}

?>
