<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru  */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('UserID','GroupID','ServiceID','SchemeID','DaysPay','ServiceInfo');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
# достаём скидку для тарифа. возможно, прогонять все политики и не надо
$Service = DB_Select('Services',Array('ID','Code','Name'),Array('UNIQ','ID'=>$ServiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Service)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Scheme = DB_Select(SPrintF('%sSchemes',$Service['Code']),Array('ID','Discount'),Array('UNIQ','ID'=>$SchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($Scheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# не применяем ничего, задана скидка
#if($Scheme['Discount'] > -1)
#	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Entrance = Tree_Path('Groups',(integer)$GroupID);
#-----------------------------------------------------------------
switch(ValueOf($Entrance)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;

default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UniqID = UniqID('Politics');
#-------------------------------------------------------------------------------
$Create = SPrintF("CREATE TEMPORARY TABLE `%s` AS SELECT *, CONCAT(`UserID`,':',`GroupID`,':',(IF(ISNULL(`FromServiceID`),'0',`FromServiceID`)),':',(IF(ISNULL(`FromSchemesGroupID`),'0',`FromSchemesGroupID`)),':',(IF(ISNULL(`ToServiceID`),'0',`ToServiceID`)),':',(IF(ISNULL(`ToSchemeID`),'0',`ToSchemeID`)),':',(IF(ISNULL(`ToSchemesGroupID`),'0',`ToSchemesGroupID`)),':',`DaysDiscont`) AS `UniqScheme` FROM `Politics` ORDER BY `Discont` DESC",$UniqID);
#-------------------------------------------------------------------------------
$IsQuery = DB_Query($Create);
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(
		SPrintF('`GroupID` IN (%s) OR `UserID` = %u',Implode(',',$Entrance),$UserID),
		/* задан сервис + (задан/не задан тариф) + не задана группа || не задан сервис + не задан тариф + задана группа */
		SPrintF('(`FromServiceID` = %u AND (`FromSchemeID` = %u OR ISNULL(`FromSchemeID`)) AND NOT EXISTS(SELECT * FROM `SchemesGroupsItems` WHERE `%s`.`FromSchemesGroupID` = `SchemesGroupID` AND `ServiceID` = %u AND `SchemeID` = %u)) OR (ISNULL(`FromServiceID`) AND ISNULL(`FromSchemeID`) AND EXISTS(SELECT * FROM `SchemesGroupsItems` WHERE `%s`.`FromSchemesGroupID` = `SchemesGroupID` AND `ServiceID` = %u AND `SchemeID` = %u)) OR (ISNULL(`FromServiceID`) AND ISNULL(`FromSchemeID`) AND EXISTS(SELECT * FROM `SchemesGroupsItems` WHERE `%s`.`FromSchemesGroupID` = `SchemesGroupID` AND `ServiceID` = %u AND ISNULL(`SchemeID`)))',$ServiceID,$SchemeID,$UniqID,$ServiceID,$SchemeID,$UniqID,$ServiceID,$SchemeID,$UniqID,$ServiceID),
		SPrintF('`DaysPay` <= %u',$DaysPay),
		);
#-------------------------------------------------------------------------------
$Columns = Array(
		'DISTINCT(`UniqScheme`) AS UniqScheme',
		'ToServiceID',
		'ToSchemeID',
		'ToSchemesGroupID',
		'DaysDiscont',
		'Discont',
		'ID',
		'ExpirationDate'
		);
#-------------------------------------------------------------------------------
$Politics = DB_Select($UniqID,$Columns,Array('Where'=>$Where,'GroupBy'=>'UniqScheme','SortOn'=>'Discont','IsDesc'=>TRUE));
#-------------------------------------------------------------------------------
switch(ValueOf($Politics)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-----------------------------------------------------------------------
	foreach($Politics as $Politic){
		#-------------------------------------------------------------------------------
		# сравниваем дату окончания политики с текущей
		if($Politic['ExpirationDate'] > Time()){
			#-------------------------------------------------------------------------------
			$IsInsert = DB_Insert('Bonuses',Array('UserID'=>$UserID,'ServiceID'=>$Politic['ToServiceID'],'SchemeID'=>$Politic['ToSchemeID'],'SchemesGroupID'=>$Politic['ToSchemesGroupID'],'DaysReserved'=>($Politic['DaysDiscont']?$Politic['DaysDiscont']:$DaysPay),'Discont'=>$Politic['Discont'],'Comment'=>SPrintF('Добавлено политикой #%u, оплата %s',$Politic['ID'],$ServiceInfo)));
			if(Is_Error($IsInsert))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
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
return TRUE;

?>
