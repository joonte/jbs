<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Null($Args)){
	#-------------------------------------------------------------------------------
	if(Is_Error(System_Load('modules/Authorisation.mod')))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Args		= IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$OrderID	= (integer) @$Args['OrderID'];
$IsNoDiscont	= (boolean) @$Args['IsNoDiscont'];
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/www/API/OrderRestore]: OrderID = %s; IsNoDiscont = %s',$OrderID,($IsNoDiscont)?'TRUE':'FALSE'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём сервис услуги
$Where = SPrintF('`ID` = (SELECT `ServiceID` FROM `OrdersOwners` WHERE `ID` = %u )',$OrderID);
#-------------------------------------------------------------------------------
$Service = DB_Select('Services',Array('ID','Code','Name','NameShort'),Array('UNIQ','Where'=>$Where));
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
// даныне заказа
$Order = DB_Select(SPrintF('%sOrdersOwners',$Service['Code']),Array('ID','OrderID','UserID','ContractID','(SELECT `TypeID` FROM `Contracts` WHERE `Contracts`.`ID` = `ContractID`) AS `TypeID`'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$OrderID)));
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
#-------------------------------------------------------------------------------
// разрешение на чтение информации о заказе
$IsPermission = Permission_Check(SPrintF('%sOrdersRead',$Service['Code']),(integer)$GLOBALS['__USER']['ID'],(integer)$Order['UserID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsPermission)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	return ERROR | @Trigger_Error(700);
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsConsiderManage = Permission_Check(SPrintF('%sOrdersConsider',$Service['Code']),(integer)$GLOBALS['__USER']['ID'],(integer)$Order['UserID']);
#-------------------------------------------------------------------------------
// тварь дрожжащая или право имеет? =)
switch(ValueOf($IsConsiderManage)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	#-------------------------------------------------------------------------------
	// если это юзер, то вариант один: без скидки.
	$IsNoDiscont = TRUE;
	#-------------------------------------------------------------------------------
	// если это юзер, и юрлицо - возврата нет
	if(In_Array($Order['TypeID'],Array('Individual','Juridical')))
		return new gException('USER_CANT_REFUND_JURIDICAL','Услуга находится на договоре юрлица, для возврата обратитесь в систему поддержки');
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
case 'true':
	// если это админ - то возможно и со скидкой и без
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$OrderConsiders = DB_Select('OrdersConsider',Array('ID','DaysReserved','DaysRemainded','Cost','Discont','`DaysRemainded`*`Cost`*(1-`Discont`) as `SummRemainded`'),Array('Where'=>SPrintF('`OrderID` = %u AND `DaysRemainded` > 0',$Order['OrderID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($OrderConsiders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return Array('Status'=>'Ok');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#--------------------------------TRANSACTION------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID(SPrintF('%sOrderRestore',$Service['Code'])))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($OrderConsiders as $OrderConsider){
	#-------------------------------------------------------------------------------
	// сумма возврата, с учётом скидок
	$SummRemainded = (double)$OrderConsider['SummRemainded'];
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/API/OrderRestore]: DaysRemainded = %s; SummRemainded = %s',$OrderConsider['DaysRemainded'],$SummRemainded));
	#-------------------------------------------------------------------------------
	// сумма возврата, как именно её рассчитываем
	if($IsNoDiscont){
		#-------------------------------------------------------------------------------
		// без учёта скидок
		#-------------------------------------------------------------------------------
		// при ручноё правке, возможно что DaysRemainded > DaysReserved.
		if($OrderConsider['DaysRemainded'] > $OrderConsider['DaysReserved'])
			continue;
		#-------------------------------------------------------------------------------
		// оплата за использованные дни, без учёта скидок
		$NoDiscontPayment = ($OrderConsider['DaysReserved'] - $OrderConsider['DaysRemainded'])*$OrderConsider['Cost'];
		#-------------------------------------------------------------------------------
		// реальная сумма оплаты за эту строку учёта
		$PaymentSumm = $OrderConsider['DaysReserved']*$OrderConsider['Cost']*(1 - $OrderConsider['Discont']);
		#-------------------------------------------------------------------------------
		// если оплата за использованные дни больше реальнооплаченной суммы - возврат нулевой
		if($NoDiscontPayment >= $PaymentSumm)
			$NoDiscontPayment = 0;
		#-------------------------------------------------------------------------------
		// сумма возврата без учёта скидок
		$Refund = $PaymentSumm - $NoDiscontPayment;
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		// с учётом скидок
		$Refund = $SummRemainded;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/API/OrderRestore]: DaysRemainded = %s; SummRemainded = %s; Refund = %s',$OrderConsider['DaysRemainded'],$SummRemainded,$Refund));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// если есть что возвращать в одном из вариантов возврата
	if($SummRemainded || $Refund){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Formats/Order/Number',$Order['OrderID']);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		// нули в историю операций не записываем, событие не создаём
		if($Refund > 0){
			#-------------------------------------------------------------------------------
			$IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$Order['ContractID'],'Summ'=>$Refund,'ServiceID'=>3000,'Comment'=>SPrintF('Услуга "%s", #%s',$Service['NameShort'],$Comp)));
			#-------------------------------------------------------------------------------
			switch(ValueOf($IsUpdate)){
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
			$Comp = Comp_Load('Formats/Currency',$Refund);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Text = SPrintF('%s возврат средств за заказ (#%u), услуга (%s), сумма (%s)',($IsConsiderManage)?'Осуществлён':'Пользователем осуществлён',$OrderID,$Service['NameShort'],$Comp);
			#-------------------------------------------------------------------------------
			$Event = Array(
					'UserID'        => $Order['UserID'],
					'PriorityID'    => 'Hosting',
					'Text'          => $Text
					);
			#-------------------------------------------------------------------------------
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('OrdersConsider',Array('DaysReserved'=>($OrderConsider['DaysReserved'] - $OrderConsider['DaysRemainded']),'DaysRemainded'=>0,'DaysConsidered'=>0),Array('ID'=>$OrderConsider['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#---------------------------END TRANSACTION-------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
