<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$CurrentDay = (integer)(Time()/86400);
#-------------------------------------------------------------------------------
$Where = SPrintF("`StatusID` = 'Active' AND `ConsiderDay` < %u",$CurrentDay);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','OrderID','ContractID','ConsiderDay','SchemeID','(SELECT `IsAutoProlong` FROM `Orders` WHERE `HostingOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`');
$HostingOrders = DB_Select('HostingOrdersOwners',$Columns,Array('Where'=>$Where,'Limit'=>Array('Start'=>0,'Length'=>5)));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return MkTime(4,15,0,Date('n'),Date('j')+1,Date('Y'));
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($HostingOrders as $HostingOrder){
	#-------------------------------------------------------------------------------
	$OrderID	= (integer)$HostingOrder['OrderID'];
	$HostingOrderID	= (integer)$HostingOrder['ID'];
	#------------------------------TRANSACTION--------------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('OrdersConsider'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Where = SPrintF('`OrderID` = %u AND `DaysRemainded` > 0 AND `ID` = (SELECT MIN(`ID`) FROM `OrdersConsider` WHERE `OrderID` = %u AND `DaysRemainded` > 0)',$OrderID,$OrderID);
	#-------------------------------------------------------------------------------
	$OrdersConsider = DB_Select('OrdersConsider','*',Array('UNIQ','Where'=>$Where));
	#-------------------------------------------------------------------------------
	switch(ValueOf($OrdersConsider)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		# check autoprolongation
		if($HostingOrder['IsAutoProlong']){
			#-------------------------------------------------------------------------------
			Debug("[comp/Tasks/HostingConsider]: autoprolongation for " . $HostingOrder['OrderID']);
			#-------------------------------------------------------------------------------
			$HostingScheme = DB_Select('HostingSchemes','MinDaysPay',Array('UNIQ','ID'=>$HostingOrder['SchemeID']));
			#-------------------------------------------------------------------------------
			switch(ValueOf($HostingScheme)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				#-------------------------------------------------------------------------------
				$HostingOrderPay = Comp_Load('www/API/HostingOrderPay',Array('HostingOrderID'=>$HostingOrderID,'DaysPay'=>$HostingScheme['MinDaysPay'],'IsNoBasket'=>TRUE,'PayMessage'=>'Автоматическое продление заказа, оплата с баланса договора'));
				#-------------------------------------------------------------------------------
				switch(ValueOf($HostingOrderPay)){
				case 'error':
					return ERROR | @Trigger_Error(500);

				case 'exception':
					#-------------------------------------------------------------------------------
					$Event = Array('UserID'=>$HostingOrder['UserID'],'Text'=>SPrintF('Не удалость автоматически оплатить заказ хостинга, причина (%s)',$HostingOrderPay->String));
					$Event = Comp_Load('Events/EventInsert',$Event);
					if(!$Event)
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>'Suspended','RowsIDs'=>$HostingOrderID,'Comment'=>SPrintF('Срок действия заказа окончен/%s',$HostingOrderPay->String)));
					#-------------------------------------------------------------------------------
					switch(ValueOf($Comp)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						# No more...
						break 4;
					default:
						return ERROR | @Trigger_Error(101);
					}
					#-------------------------------------------------------------------------------
				case 'array':
					# No more...
					break 3;
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		}else{	# autoprolongation -> no autoprolongation
			#-------------------------------------------------------------------------------
			Debug("[comp/Tasks/HostingConsider]: NO autoprolongation for " . $HostingOrder['OrderID']);
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>'Suspended','RowsIDs'=>$HostingOrderID,'Comment'=>'Срок действия заказа окончен/Автопродление отключено'));
			switch(ValueOf($Comp)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				# No more...
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('OrdersConsider',Array('DaysRemainded'=>$OrdersConsider['DaysRemainded']-1),Array('ID'=>$OrdersConsider['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$DaysConsidered = (integer)$OrdersConsider['DaysConsidered'];
		#-------------------------------------------------------------------------------
		if($DaysConsidered){
			#-------------------------------------------------------------------------------
			$CurrentMonth = (Date('Y') - 1970)*12 + (integer)Date('n');
			#-------------------------------------------------------------------------------
			$Number = Comp_Load('Formats/Order/Number',$HostingOrder['OrderID']);
			if(Is_Error($Number))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$IWorkComplite = Array(
						'ContractID'	=> $HostingOrder['ContractID'],
						'Month'		=> $CurrentMonth,
						'ServiceID'	=> 10000,
						'Comment'	=> SPrintF('№%s',$Number),
						'Amount'	=> 1,
						'Cost'		=> $OrdersConsider['Cost'],
						'Discont'	=> $OrdersConsider['Discont']
						);
			#-------------------------------------------------------------------------------
			$IsInsert = DB_Insert('WorksComplite',$IWorkComplite);
			if(Is_Error($IsInsert))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>$DaysConsidered-1),Array('ID'=>$OrdersConsider['ID']));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
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
	$ConsiderDay = (integer)$HostingOrder['ConsiderDay'];
	#-------------------------------------------------------------------------------
	$ConsiderDay = ($ConsiderDay?$ConsiderDay+1:$CurrentDay);
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('HostingOrders',Array('ConsiderDay'=>$ConsiderDay),Array('ID'=>$HostingOrderID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Commit($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return 60;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
