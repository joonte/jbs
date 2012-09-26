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
// выбираем неактивных пользователей
$Where = Array(
			/* идентификатор больше 2000 - ниже, тока у системных */
			'`ID` > 2000',
			/* не защищённый */
			'`IsProtected` = "no"',
			/* не входил в биллинг больше года */
			'`EnterDate` < UNIX_TIMESTAMP() - 365 * 24 * 3600',
			/* нет заказов */
			'(SELECT COUNT(*) FROM `OrdersOwners` WHERE `UserID` = `Users`.`ID`) = 0',
			/* нет выписанных счетов на оплату */
			'(SELECT COUNT(*) FROM `InvoicesOwners` WHERE `UserID` = `Users`.`ID`) = 0',
			/* нет договоров с баллансом больше нуля */
			'(SELECT SUM(`Balance`) FROM `ContractsOwners` WHERE `UserID` = `Users`.`ID`) = 0',
			/* нет рефералов */
			'(SELECT COUNT(*) FROM `Users` WHERE `OwnerID` = `Users`.`ID`) = 0'
		);
#-------------------------------------------------------------------------------
$Users = DB_Select('Users', Array('ID','Email','Name'),Array('Where'=>$Where));
switch(ValueOf($Users)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return TRUE;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
// перебираем полученных пользователей.
foreach($Users as $User){
	// удаляем юзера
	$Comp = Comp_Load('www/API/Delete',Array('TableID'=>'Users','RowsIDs'=>$User['ID']));
	#-------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'array':
		$Event = Array(
				'PriorityID'    => 'Billing',
				'Text'          => SPrintF('Удалён пользователь (%s/%s) не заходивший в биллинг более года',$User['Name'],$User['Email'])
			);
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
		break;
	default:
		return ERROR | @Trigger_Error(500);
	}
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>
