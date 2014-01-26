<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru  */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Result = Array();
#-------------------------------------------------------------------------------
$Where = Array(
		'`ToUserID` = @local.__USER_ID',
		'`IsExecuted` = "no"'
		);
$IOrdersTransfer = DB_Select('OrdersTransfer',Array('*','(SELECT `NameShort` FROM `Services` WHERE `Services`.`ID` = `OrdersTransfer`.`ServiceID`) AS `NameShort`','(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `OrdersTransfer`.`ServiceID`) AS `Code`'),Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($IOrdersTransfer)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($IOrdersTransfer as $OrdersTransfer){
		#-------------------------------------------------------------------------------
		# проверяем, не прошло ли время передачи аккаунта?
		if($OrdersTransfer['CreateDate'] + 24*3600 < Time()){
			# помечаем как выполненную
			$IsUpdate = DB_Update('OrdersTransfer',Array('IsExecuted'=>TRUE),Array('ID'=>$OrdersTransfer['ID']));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			return $Result;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		# достаём данные передающей стороны
		$User = DB_Select('Users',Array('ID','Email'),Array('UNIQ','ID'=>$OrdersTransfer['UserID']));
		#-------------------------------------------------------------------------------
		switch(ValueOf($User)){
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
		#-------------------------------------------------------------------------------
		# достаём данные заказа
		$Order = DB_Select(SPrintF('%sOrdersOwners',($OrdersTransfer['Code'] == 'Default')?'':$OrdersTransfer['Code']),Array('*'),Array('UNIQ','ID'=>$OrdersTransfer['ServiceOrderID']));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Order)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#-------------------------------------------------------------------------------
			return $Result;
			#return ERROR | @Trigger_Error(400);
			#-------------------------------------------------------------------------------
		case 'array':
			#-------------------------------------------------------------------------------
			$OrderID = Comp_Load('Formats/Order/Number',IsSet($Order['OrderID'])?$Order['OrderID']:$Order['ID']);
			if(Is_Error($OrderID))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Params = Array('User'=>$User,'OrdersTransfer'=>$OrdersTransfer,'Order'=>$Order,'OrderID'=>IsSet($Order['OrderID'])?$Order['OrderID']:$Order['ID']);
			#-------------------------------------------------------------------------------
			$NoBody = new Tag('NOBODY');
			#-------------------------------------------------------------------------------
			# No more...
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# проверяем наличие заполненного профиля
		$Where = Array(
				'`UserID` = @local.__USER_ID',
				'`TypeID` != "Default"',
				'`StatusID` = "Complite" OR `StatusID` = "Public"',
				);
		$Profiles = DB_Select('Contracts',Array('*'),Array('Where'=>$Where));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Profiles)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#-------------------------------------------------------------------------------
			# нету подходящих профилей
			$NoBody->AddHTML(TemplateReplace('Notes.User.OrdersTransfer.Contracts',$Params));
			$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>"javascript:ShowWindow('/ContractMake');"),'[создать договор]')));
			#-------------------------------------------------------------------------------
			$Result[] = $NoBody;
			#-------------------------------------------------------------------------------
			return $Result;
			#-------------------------------------------------------------------------------
		case 'array':
			# No more...
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$NoBody->AddHTML(TemplateReplace('Notes.User.OrdersTransfer.Message',$Params));
		$NoBody->AddChild(new Tag('STRONG',new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/API/OrdersTransfer',{OrdersTransferID:%u});",$OrdersTransfer['ID'])),'[принять заказ]')));
		#-------------------------------------------------------------------------------
		$Result[] = $NoBody;
		#-------------------------------------------------------------------------------
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return $Result;
#-------------------------------------------------------------------------------

?>
