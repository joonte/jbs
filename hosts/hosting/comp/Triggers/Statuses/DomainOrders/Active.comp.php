<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('DomainOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$DomainOrderID = (integer)$DomainOrder['ID'];
#-------------------------------------------------------------------------------
$Where = SPrintF("`DomainOrderID` = %u AND `YearsRemainded` > 0",$DomainOrderID);
#-------------------------------------------------------------------------------
$DomainConsider = DB_Select('DomainConsider','*',Array('SortOn'=>'ID','Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainConsider)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	#------------------------------TRANSACTION--------------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('Triggers/Statuses/DomainOrder/Active'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$YearsRemainded = DB_Select('DomainConsider',Array('SUM(`YearsRemainded`) as `YearsRemainded`'),Array('UNIQ','Where'=>$Where));
	#-------------------------------------------------------------------------------
	switch(ValueOf($YearsRemainded)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$ExpirationDate = Max($DomainOrder['ExpirationDate'],Time());
		#-------------------------------------------------------------------------------
		$Date = Array();
		#-------------------------------------------------------------------------------
		foreach(Array('H','i','s','m','d','Y') as $Element)
			$Date[$Element] = Date($Element,$ExpirationDate);
		#-------------------------------------------------------------------------------
		$Date['Y'] += (integer)$YearsRemainded['YearsRemainded'];
		#-------------------------------------------------------------------------------
		$ExpirationDate = MkTime($Date['H'],$Date['i'],$Date['s'],$Date['m'],$Date['d'],$Date['Y']);
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('DomainOrders',Array('ProfileID'=>NULL,'ExpirationDate'=>$ExpirationDate),Array('ID'=>$DomainOrderID));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Number = Comp_Load('Formats/Order/Number',$DomainOrder['OrderID']);
		if(Is_Error($Number))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$CurrentMonth = (Date('Y') - 1970)*12 + (integer)Date('n');
		#-------------------------------------------------------------------------------
		foreach($DomainConsider as $ConsiderItem){
			#-------------------------------------------------------------------------------
			$IWorkComplite = Array(
						'ContractID'	=> $DomainOrder['ContractID'],
						'Month'		=> $CurrentMonth,
						'ServiceID'	=> $DomainOrder['ServiceID'],
						'Comment'	=> SPrintF('№%s',$Number),
						'Amount'	=> $ConsiderItem['YearsRemainded'],
						'Cost'		=> $ConsiderItem['Cost'],
						'Discont'	=> $ConsiderItem['Discont']
						);
			#-------------------------------------------------------------------------------
			$IsInsert = DB_Insert('WorksComplite',$IWorkComplite);
			if(Is_Error($IsInsert))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$IsUpdate = DB_Update('DomainConsider',Array('YearsRemainded'=>0),Array('ID'=>$ConsiderItem['ID']));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		if(Is_Error(DB_Commit($TransactionID)))
			return ERROR | @Trigger_Error(500);
		#--------------------------END TRANSACTION--------------------------------------
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
$Comp = Comp_Load('Services/Orders/OrdersHistory',Array('OrderID'=>$DomainOrder['OrderID'],'Parked'=>SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainOrder['Name'])));
switch(ValueOf($Comp)){
case 'error':
        return ERROR | @Trigger_Error(500);
case 'exception':
        return $Comp;
case 'array':
        return TRUE;
default:
        return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
