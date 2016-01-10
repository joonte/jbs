<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$GLOBALS['TaskReturnInfo'] = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['Consider'];
#-------------------------------------------------------------------------------
#Debug(Print_r($Settings,true));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecutePeriod'=>$Settings['ExecutePeriod']));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array('`ConsiderTypeID` = "Daily"','`Code` != "Default"','`IsProlong` = "yes"','`IsHidden` = "no"');
#-------------------------------------------------------------------------------
$Services = DB_Select('Services',Array('ID','Code','Name'),Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Services)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'][] = 'no services for consider';
	#-------------------------------------------------------------------------------
	return MkTime(1,15,0,Date('n'),Date('j')+1,Date('Y'));
	#-------------------------------------------------------------------------------
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$CurrentDay = (integer)(Time()/86400);
#-------------------------------------------------------------------------------
foreach($Services as $Service){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Orders/Consider]: processing Service = %s',$Service['Code']));
	#if($Service['Code'] != 'DNSmanager')
	#	continue;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Statuses = $Config['Statuses'][SPrintF('%sOrders',$Service['Code'])];
	#-------------------------------------------------------------------------------
	$StatusID = IsSet($Statuses['Suspended'])?'Suspended':'Deleted';
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Where = Array("`StatusID` = 'Active'",SPrintF("`ConsiderDay` < %u",$CurrentDay));
	#-------------------------------------------------------------------------------
	# затычка для вечных лицензий ISPsystem
	if($Service['Code'] == 'ISPsw')
		$Where[] = '(SELECT `ConsiderTypeID` FROM `ISPswSchemes` WHERE `ISPswSchemes`.`ID` = `ISPswOrdersOwners`.`SchemeID`)  = "Daily"';
	#-------------------------------------------------------------------------------
	$Columns = Array(
			'ID','UserID','OrderID','ContractID','ConsiderDay','SchemeID',
			SPrintF('(SELECT `IsAutoProlong` FROM `Orders` WHERE `%sOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`',$Service['Code'])
			);
	#-------------------------------------------------------------------------------
	$ServiceOrders = DB_Select(SPrintF('%sOrdersOwners',$Service['Code']),$Columns,Array('Where'=>$Where,'Limits'=>Array(0,$Settings['PerIteration'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ServiceOrders)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Orders/Consider]: no orders for consider, Service = %s',$Service['Code']));
		#-------------------------------------------------------------------------------
		continue 2;
		#-------------------------------------------------------------------------------
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	foreach($ServiceOrders as $ServiceOrder){
		#-------------------------------------------------------------------------------
		$OrderID	= (integer)$ServiceOrder['OrderID'];
		$ServiceOrderID	= (integer)$ServiceOrder['ID'];
		#-------------------------------------------------------------------------------
		if(IsSet($GLOBALS['TaskReturnInfo'][$Service['Code']])){
			#-------------------------------------------------------------------------------
			$GLOBALS['TaskReturnInfo'][$Service['Code']][] = $OrderID;
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$GLOBALS['TaskReturnInfo'][$Service['Code']] = Array($OrderID);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#------------------------------TRANSACTION--------------------------------------
		if(Is_Error(DB_Transaction($TransactionID = UniqID('OrdersConsider'))))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Where = SPrintF('`OrderID` = %u AND `DaysRemainded` > 0',$OrderID);
		#-------------------------------------------------------------------------------
		$OrdersConsider = DB_Select('OrdersConsider','*',Array('UNIQ','SortOn'=>'Discont','Limits'=>Array(0,1),'Where'=>$Where));
		#-------------------------------------------------------------------------------
		switch(ValueOf($OrdersConsider)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#-------------------------------------------------------------------------------
			# check AutoProlongation
			if($ServiceOrder['IsAutoProlong']){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Orders/Consider]: autoprolongation for %s',$ServiceOrder['OrderID']));
				#-------------------------------------------------------------------------------
				$ServiceScheme = DB_Select(SPrintF('%sSchemes',$Service['Code']),'MinDaysPay',Array('UNIQ','ID'=>$ServiceOrder['SchemeID']));
				#-------------------------------------------------------------------------------
				switch(ValueOf($ServiceScheme)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return ERROR | @Trigger_Error(400);
				case 'array':
					#-------------------------------------------------------------------------------
					$ServiceOrderPay = Comp_Load(SPrintF('www/API/%sOrderPay',$Service['Code']),Array(SPrintF('%sOrderID',$Service['Code'])=>$ServiceOrderID,'DaysPay'=>$ServiceScheme['MinDaysPay'],'IsNoBasket'=>TRUE,'PayMessage'=>'Автоматическое продление заказа, оплата с баланса договора'));
					#-------------------------------------------------------------------------------
					switch(ValueOf($ServiceOrderPay)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						#-------------------------------------------------------------------------------
						$Event = Array('UserID'=>$ServiceOrder['UserID'],'Text'=>SPrintF('Не удалость автоматически оплатить заказ %s/#%s, причина (%s)',$Service['Code'],$OrderID,$ServiceOrderPay->String));
						$Event = Comp_Load('Events/EventInsert',$Event);
						if(!$Event)
							return ERROR | @Trigger_Error(500);
						#-------------------------------------------------------------------------------
						$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>SPrintF('%sOrders',$Service['Code']),'StatusID'=>$StatusID,'RowsIDs'=>$ServiceOrderID,'Comment'=>SPrintF('Срок действия заказа окончен/%s',$ServiceOrderPay->String)));
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
					break;
					#-------------------------------------------------------------------------------
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------------
			}else{	# autoprolongation -> no autoprolongation
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/Orders/Consider]: NO AutoProlongation for %s',$ServiceOrder['OrderID']));
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>SPrintF('%sOrders',$Service['Code']),'StatusID'=>$StatusID,'RowsIDs'=>$ServiceOrderID,'Comment'=>'Срок действия заказа окончен/Автопродление отключено'));
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
				$Number = Comp_Load('Formats/Order/Number',$ServiceOrder['OrderID']);
				if(Is_Error($Number))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$IWorkComplite = Array(
							'ContractID'	=> $ServiceOrder['ContractID'],
							'Month'		=> $CurrentMonth,
							'ServiceID'	=> $Service['ID'],
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
		$ConsiderDay = (integer)$ServiceOrder['ConsiderDay'];
		#-------------------------------------------------------------------------------
		$ConsiderDay = ($ConsiderDay?$ConsiderDay+1:$CurrentDay);
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update(SPrintF('%sOrders',$Service['Code']),Array('ConsiderDay'=>$ConsiderDay),Array('ID'=>$ServiceOrderID));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if(Is_Error(DB_Commit($TransactionID)))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Orders/Consider]: end orders processing for Service = %s',$Service['Code']));
	#-------------------------------------------------------------------------------
	return IntVal($Settings['SleepTime']);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Orders/Consider]: no more orders, go to next day'));
#-------------------------------------------------------------------------------
#return MkTime(1,15,0,Date('n'),Date('j')+1,Date('Y'));
return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
