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
	$Where = SPrintF('(`GroupID` IN (%s) OR `UserID` = %u) AND (`FromSchemeID` = %u OR ISNULL(`FromSchemeID`)) AND `DaysPay` <= %u',Implode(',',$Entrance),$UserID,$SchemeID,$DaysPay);
	#-------------------------------------------------------------
	$Politic = DB_Select('Politics','*',Array('UNIQ','Where'=>$Where,'SortOn'=>'Discont','IsDesc'=>TRUE,'Limits'=>Array(0,1)));
	#-------------------------------------------------------------
	switch(ValueOf($Politic)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break 2;
	case 'array':
		#---------------------------------------------------------
		$IsInsert = DB_Insert('Bonuses',Array('UserID'=>$UserID,'ServiceID'=>$ServiceID,'SchemeID'=>$SchemeID,'DaysReserved'=>$DaysPay,'Discont'=>$Politic['Discont']));
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
