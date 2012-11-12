<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru  */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('UserID','GroupID','ServiceID','SchemeID','DaysPay');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Entrance = Tree_Path('Groups',(integer)$GroupID);
#-----------------------------------------------------------------
switch(ValueOf($Entrance)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------
	$Where = Array(
			SPrintF('`GroupID` IN (%s) OR `UserID` = %u',Implode(',',$Entrance),$UserID),
			/* задан сервис + (задан/не задан тариф) + не задана группа || не задан сервис + не задан тариф + задана группа */
			SPrintF('(`FromServiceID` = %u AND (`FromSchemeID` = %u OR ISNULL(`FromSchemeID`)) AND NOT EXISTS(SELECT * FROM `SchemesGroupsItems` WHERE `SchemesGroupsItems`.`SchemesGroupID` = `SchemesGroupID` AND `ServiceID` = %u AND `SchemeID` = %u)) OR (ISNULL(`FromServiceID`) AND ISNULL(`FromSchemeID`) AND EXISTS(SELECT * FROM `SchemesGroupsItems` WHERE `SchemesGroupsItems`.`SchemesGroupID` = `SchemesGroupID` AND `ServiceID` = %u AND `SchemeID` = %u))',$ServiceID,$SchemeID,$ServiceID,$SchemeID,$ServiceID,$SchemeID),
			SPrintF('`DaysPay` <= %u',$DaysPay),
			);
	#-------------------------------------------------------------
	$Politic = DB_Select('Politics',Array('*'/*,"CONCAT(``,'','','','')"*/),Array('UNIQ','Where'=>$Where,'SortOn'=>'Discont','IsDesc'=>TRUE,'Limits'=>Array(0,1)));
	#-------------------------------------------------------------
	switch(ValueOf($Politic)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break 2;
	case 'array':
		#---------------------------------------------------------
		$IsInsert = DB_Insert('Bonuses',Array('UserID'=>$UserID,'ServiceID'=>$Politic['ToServiceID'],'SchemeID'=>$Politic['ToSchemeID'],'SchemesGroupID'=>$Politic['ToSchemesGroupID'],'DaysReserved'=>($Politic['DaysDiscont']?$Politic['DaysDiscont']:$DaysPay),'Discont'=>$Politic['Discont'],'Comment'=>SPrintF('Добавлено политикой #%u',$Politic['ID'])));
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		break 2;
	default:
		return ERROR | @Trigger_Error(101);
	}
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;

?>
