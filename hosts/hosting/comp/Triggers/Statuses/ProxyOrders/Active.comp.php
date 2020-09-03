<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ProxyOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Order = DB_Select('Orders',Array('ID','ContractID','ServiceID'),Array('UNIQ','ID'=>$ProxyOrder['OrderID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Order)){
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
$Contract = DB_Select('Contracts',Array('ID','IsUponConsider'),Array('UNIQ','ID'=>$Order['ContractID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
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
if(!$Contract['IsUponConsider']){
	#-------------------------------------------------------------------------------
	$CurrentMonth = (Date('Y') - 1970)*12 + (integer)Date('n');
	#-------------------------------------------------------------------------------
	$Number = Comp_Load('Formats/Order/Number',$Order['ID']);
	if(Is_Error($Number))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Where = SPrintF('`OrderID` = %u AND `DaysRemainded` > 0',$Order['ID']);
	#-------------------------------------------------------------------------------
	$OrdersConsider = DB_Select('OrdersConsider','*',Array('Where'=>$Where));
	#-------------------------------------------------------------------------------
	switch(ValueOf($OrdersConsider)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if(Is_Error(DB_Transaction($TransactionID = UniqID('OrdersConsider'))))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		foreach($OrdersConsider as $ConsiderItem){
			#-------------------------------------------------------------------------------
			$IWorkComplite = Array(
						'ContractID'	=> $Contract['ID'],
						'Month'		=> $CurrentMonth,
						'ServiceID'	=> $Order['ServiceID'],
						'Comment'	=> SPrintF('№%s',$Number),
						'Amount'	=> $ConsiderItem['DaysConsidered'],
						'Cost'		=> $ConsiderItem['Cost'],
						'Discont'	=> $ConsiderItem['Discont']
						);
			#-------------------------------------------------------------------------------
			$IsInsert = DB_Insert('WorksComplite',$IWorkComplite);
			if(Is_Error($IsInsert))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$ConsiderItem['ID']));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if(Is_Error(DB_Commit($TransactionID)))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
switch($ProxyOrder['StatusID']){
case 'SchemeChange':
	#-------------------------------------------------------------------------------
	$ProxyScheme = DB_Select('ProxySchemes','CostDay',Array('UNIQ','ID'=>$ProxyOrder['SchemeID']));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ProxyScheme)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$Cost = $ProxyScheme['CostDay'];
		#-------------------------------------------------------------------------------
		$IsQuery = DB_Query(SPrintF('UPDATE `OrdersConsider` SET `DaysRemainded` = `DaysRemainded`*(`Cost`/%f), `DaysConsidered` = `DaysConsidered`*(`Cost`/%f), `Cost` = %f WHERE `DaysRemainded` > 0 AND `OrderID` = %u AND `Cost` != %f',$Cost,$Cost,$Cost,$Order['ID'],$Cost));
		if(Is_Error($IsQuery))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		break 2;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
case 'Suspended':
	#-------------------------------------------------------------------------------
	# added by lissyara, for JBS-536
	$Where = Array(SPrintF('`Params` = \'{"ID":"%u"}\'',$ProxyOrder['ID']));
	#-------------------------------------------------------------------------------
	$TaskExecuteTime = DB_Select('Tasks','ExecuteDate',Array('UNIQ','Where'=>$Where,'SortOn'=>'ExecuteDate','IsDesc'=>TRUE,'Limits'=>Array(0,1)));
	#-------------------------------------------------------------------------------
	switch(ValueOf($TaskExecuteTime)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		$ExecuteDate = Time();
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		$ExecuteDate = $TaskExecuteTime['ExecuteDate'] + 2*60;
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#$ExecuteDate = Time();
	#-------------------------------------------------------------------------------
	$IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$ProxyOrder['UserID'],'TypeID'=>'ProxyActive','ExecuteDate'=>$ExecuteDate,'Params'=>Array($ProxyOrder['ID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsAdd)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		# No more...
		break 2;
	default:
		return ERROR | @Trigger_Error(101);
	}
default:
	# No more...
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// если заказ активен, то обнуление дней учёта списывает один день при оплате
if($ProxyOrder['StatusID'] != 'Active'){
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('ProxyOrders',Array('ConsiderDay'=>0),Array('ID'=>$ProxyOrder['ID']));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Parked = Array();
#-------------------------------------------------------------------------------
if($ProxyOrder['IP'])
	$Parked[] = $ProxyOrder['IP'];
#-------------------------------------------------------------------------------
if($ProxyOrder['Host'])
	$Parked[] = $ProxyOrder['Host'];
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Orders/OrdersHistory',Array('OrderID'=>$ProxyOrder['OrderID'],'Parked'=>$Parked));
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
