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
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$ExtraIPOrderID = (integer) @$Args['ExtraIPOrderID'];
$OrderID        = (integer) @$Args['OrderID'];
$DaysPay        = (integer) @$Args['DaysPay'];
$IsChange       = (boolean) @$Args['IsChange'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/Tree.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','ServiceID','Login','StatusID','UserID','SchemeID','DaysRemainded','(SELECT `TypeID` FROM `Contracts` WHERE `ExtraIPOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractTypeID`','(SELECT `Balance` FROM `Contracts` WHERE `ExtraIPOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`','(SELECT `GroupID` FROM `Users` WHERE `ExtraIPOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`','(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `ExtraIPOrdersOwners`.`OrderID`) as `IsPayed`','(SELECT SUM(`DaysReserved`*`Cost`*(1-`Discont`)) FROM `OrdersConsider` WHERE `OrderID`=`ExtraIPOrdersOwners`.`OrderID`) AS PayedSumm');
#-------------------------------------------------------------------------------
$Where = ($ExtraIPOrderID?SPrintF('`ID` = %u',$ExtraIPOrderID):SPrintF('`OrderID` = %u',$OrderID));
#-------------------------------------------------------------------------------
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',$Columns,Array('UNIQ','Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrder)){
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
$UserID = (integer)$ExtraIPOrder['UserID'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('ExtraIPOrdersRead',(integer)$GLOBALS['__USER']['ID'],$UserID);
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
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'ExtraIPOrderPayForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'ExtraIPOrderID','value'=>$ExtraIPOrder['ID'],'type'=>'hidden'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$DOM->AddText('Title',SPrintF('Оплата заказа IP адреса, %s',$ExtraIPOrder['Login']));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/OrderPay.js}')));
#-------------------------------------------------------------------------------
if(!In_Array($ExtraIPOrder['StatusID'],Array('Waiting','Active','Suspended')))
	return new gException('ORDER_CAN_NOT_PAY','Выделенный IP адрес не может быть оплачен');
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$ExtraIPScheme = DB_Select('ExtraIPSchemes',Array('ID','CostDay','CostInstall','MinDaysPay','MinDaysProlong','MaxDaysPay','IsActive','IsProlong'),Array('UNIQ','ID'=>$ExtraIPOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPScheme)){
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
# проверяем, это первая оплата или нет? если не первая, то минимальное число дней MinDaysProlong
Debug(SPrintF('[comp/www/ExtraIPOrderPay]: ранее оплачено за заказ %s',$ExtraIPOrder['PayedSumm']));
#-------------------------------------------------------------------------------
$MinDaysPay = ($ExtraIPOrder['PayedSumm'] > 0)?$ExtraIPScheme['MinDaysProlong']:$ExtraIPScheme['MinDaysPay'];
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/www/ExtraIPOrderPay]: минимальное число дней %s',$MinDaysPay));
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Currency',$ExtraIPScheme['CostDay']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Стоимость тарифа (в день)',$Comp);
#-------------------------------------------------------------------------------
if($ExtraIPOrder['IsPayed']){
	#-------------------------------------------------------------------------------
	if(!$ExtraIPScheme['IsProlong'])
		return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план выделенного IP адреса не позволяет продление');
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	if(!$ExtraIPScheme['IsActive'])
		return new gException('SCHEME_NOT_ACTIVE','Тарифный план выделевыделенного IP адреса не активен');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if($DaysPay){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'DaysPay','type'=>'hidden','value'=>$DaysPay));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$CostPay = 0.00;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('ExtraIPOrderPay'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Services/Politics',$ExtraIPOrder['UserID'],$ExtraIPOrder['GroupID'],$ExtraIPOrder['ServiceID'],$ExtraIPScheme['ID'],$DaysPay,SPrintF('IP/%s',$ExtraIPOrder['Login']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$DaysRemainded = $DaysPay;
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Services/Bonuses',$DaysRemainded,$ExtraIPOrder['ServiceID'],$ExtraIPScheme['ID'],$UserID,$CostPay,$ExtraIPScheme['CostDay'],FALSE);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$CostPay = $Comp['CostPay'];
	$Bonuses = $Comp['Bonuses'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Roll($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$CostPay = Round($CostPay,2);
	#-------------------------------------------------------------------------------
	$DaysRemainded = $ExtraIPOrder['DaysRemainded'];
	#-------------------------------------------------------------------------------
	if($DaysRemainded){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('/Formats/Date/Standard',Time() + $DaysRemainded*86400);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Текущая дата окончания',$Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Table[] = Array('Кол-во дней оплаты',SPrintF('%u дн.',$DaysPay));
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('/Formats/Date/Standard',Time() + ($DaysRemainded + $DaysPay)*86400);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Дата окончания после оплаты',$Comp);
	#-------------------------------------------------------------------------------
	if($ExtraIPScheme['CostInstall'] > 0){
		#-------------------------------------------------------------------------------
		# need give installation payment
		if(!$ExtraIPOrder['IsPayed']){
			#-------------------------------------------------------------------------------
			# if it not prolongation
			$Comp = Comp_Load('Formats/Currency',$ExtraIPScheme['CostInstall']);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Table[] = Array('Плата за установку',$Comp);
			#-------------------------------------------------------------------------------
			$CostPay += $ExtraIPScheme['CostInstall'];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	if(Count($Bonuses)){
		#-------------------------------------------------------------------------------
		$Tr = new Tag('TR');
		#-------------------------------------------------------------------------------
		foreach(Array('Дней','Скидка') as $Text)
			$Tr->AddChild(new Tag('TD',Array('class'=>'Head'),$Text));
		#-------------------------------------------------------------------------------
		Array_UnShift($Bonuses,$Tr);
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Tables/Extended',$Bonuses,'Бонусы');
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = new Tag('DIV',Array('align'=>'center'),$Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Currency',$CostPay);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Всего к оплате',$Comp);
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',Array('align'=>'right','class'=>'Standard'));
	#-------------------------------------------------------------------------------
	$Div->AddHTML(TemplateReplace('www.ServiceOrderPay',Array('ServiceCode'=>'ExtraIP')));
	#-------------------------------------------------------------------------------
	$Table[] = $Div;
	#-------------------------------------------------------------------------------
	$Table[] = new Tag('DIV',Array('align'=>'right','style'=>'font-size:10px;'),$CostPay > $ExtraIPOrder['ContractBalance']?'[заказ будет добавлен в корзину]':'[заказ будет оплачен с баланса договора]');
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',Array('align'=>'right'));
	#-------------------------------------------------------------------------------
	if($IsChange){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>'WindowPrev();','value'=>'Изменить период'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Div->AddChild($Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>'OrderPay("ExtraIP");','value'=>'Продолжить'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Div->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$Table[] = $Div;
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Table = Array();
	#-------------------------------------------------------------------------------
	$DaysRemainded = $ExtraIPOrder['DaysRemainded'];
	#-------------------------------------------------------------------------------
	if($DaysRemainded){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('/Formats/Date/Standard',Time() + $DaysRemainded*86400);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Текущая дата окончания',$Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$TimeRemainded = $DaysRemainded*86400;
	#-------------------------------------------------------------------------------
	$ExpirationDate = MkTime(0,0,0,Date('m'),Date('j'),Date('y')) + $TimeRemainded;
	#-------------------------------------------------------------------------------
	$sTime = MkTime(0,0,0,Date('m'),Date('j') + $MinDaysPay + $DaysRemainded,Date('Y'));
	$eTime = MkTime(0,0,0,Date('m'),Date('j') + $ExtraIPScheme['MaxDaysPay'] + $DaysRemainded,Date('Y'));
	#-------------------------------------------------------------------------------
	if($sTime >= $eTime){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('www/ExtraIPOrderPay',Array('ExtraIPOrderID'=>$ExtraIPOrder['ID'],'DaysPay'=>$MinDaysPay));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		return $Comp;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// расчёт вариантов периодов оплаты
	$Calendar = Comp_Load('Orders/Calendar',Array('ExpirationDate'=>$ExpirationDate,'sTime'=>$sTime,'eTime'=>$eTime));
	if(Is_Error($Calendar))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Script = $Calendar['Script'];
	$Years  = $Calendar['Years'];
	$Periods= $Calendar['Periods'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!Count($Years))
		return new gException('PERIOExtraIP_NOT_DEFINED','Периоды оплаты не определены');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsPeriods = (boolean)Count($Periods);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// считаем вариант оплаты с балланса - заранее, чтобы выбрать его при возможности
	$Table1 = Array();
	#-------------------------------------------------------------------------------
	if($ExtraIPScheme['CostDay'] > 0){
		$DaysFromBallance = Floor($ExtraIPOrder['ContractBalance'] / $ExtraIPScheme['CostDay']);
		#-------------------------------------------------------------------------------
		// если дней ноль - считаем что их один - так будут учитываться бонусы на 100% оплату
		$DaysFromBallance = Comp_Load('Bonuses/DaysCalculate',($DaysFromBallance)?$DaysFromBallance:1,$ExtraIPScheme,$ExtraIPOrder);
		if(Is_Error($DaysFromBallance))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$DaysFromBallance = 365;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if($MinDaysPay <= $DaysFromBallance){
		#-------------------------------------------------------------------------------
		if($IsPeriods){
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Form/Input',Array('onclick'=>'form.Period.disabled = true;form.Year.disabled = true;form.Month.disabled = true;form.Day.disabled = true;','name'=>'Calendar','type'=>'radio','checked'=>'true'));
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Table1[] = new Tag('TD',Array('class'=>'Separator','colspan'=>2),$Comp,new Tag('SPAN','Остаток денег на балансе (' . $ExtraIPOrder['ContractBalance'] . ' руб.)'));
		#-------------------------------------------------------------------------------
		$Table1[] = Array('Остатка на счету хватит на ',$DaysFromBallance . ' дней');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>'DaysPayFromBallance','value'=>$DaysFromBallance,'type'=>'hidden'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if($IsPeriods){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('onclick'=>'form.Period.disabled = false;form.Year.disabled = true;form.Month.disabled = true;form.Day.disabled = true;','name'=>'Calendar','type'=>'radio'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if(!SizeOf($Table1))
			$Comp->AddAttribs(Array('checked'=>'true'));
		#-------------------------------------------------------------------------------
		$Table[] = new Tag('TD',Array('class'=>'Separator','colspan'=>2),$Comp,new Tag('SPAN','Выбор периода оплаты'));
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Select',Array('name'=>'Period','onchange'=>'PeriodUpdate("ExtraIP");'),$Periods,12);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		// если оплата по остатку балланса - дисаблим
		if(SizeOf($Table1))
			$Comp->AddAttribs(Array('disabled'=>'true'));
		#-------------------------------------------------------------------------------
		$Table[] = Array('Период оплаты',$Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Head',new Tag('SCRIPT',Implode("\n",$Script)));
	#-------------------------------------------------------------------------------
	$DOM->AddAttribs('Body',Array('onload'=>'PeriodInit("ExtraIP");'));
	#-------------------------------------------------------------------------------
	if($IsPeriods){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('onclick'=>'form.Period.disabled = true;form.Year.disabled = false;form.Month.disabled = false;form.Day.disabled = false;','name'=>'Calendar','type'=>'radio'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Table[] = new Tag('TD',Array('class'=>'Separator','colspan'=>2),$Comp,new Tag('SPAN','Выбор даты окончания'));
	#-------------------------------------------------------------------------------
	$Options = Array();
	#-------------------------------------------------------------------------------
	foreach($Years as $Year)
		$Options[$Year] = $Year;
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Select',Array('name'=>'Year','onchange'=>'CalendarUpdateMonth("ExtraIP");'),$Options);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($IsPeriods)
		$Comp->AddAttribs(Array('disabled'=>'true'));
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',$Comp);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Select',Array('name'=>'Month','onchange'=>'CalendarUpdateDay("ExtraIP");','value'=>'init'),Array('init'=>'-'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($IsPeriods)
		$Comp->AddAttribs(Array('disabled'=>'true'));
	#-------------------------------------------------------------------------------
	$Div->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Select',Array('name'=>'Day','value'=>'init'),Array('init'=>'-'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($IsPeriods)
		$Comp->AddAttribs(Array('disabled'=>'true'));
	#-------------------------------------------------------------------------------
	$Div->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Дата окончания',$Div);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// достраиваем вариант оплаты с балланса
	if(SizeOf($Table1))
		foreach($Table1 as $Row)
			$Table[] = $Row;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'DaysPay','value'=>31,'type'=>'hidden'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>SPrintF("if(typeof(form.Calendar[2]) != 'undefined' && form.Calendar[2].checked == true){form.DaysPay.value = form.DaysPayFromBallance.value;}else{form.DaysPay.value = Math.ceil((new Date(form.Year.value,form.Month.value,form.Day.value) - new Date(%u,%u,%u) - %u*1000)/86400000);};ShowWindow('/ExtraIPOrderPay',FormGet(form));",Date('Y'),Date('n')-1,Date('j'),$TimeRemainded),'value'=>'Продолжить'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = $Comp;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'hidden','name'=>'IsChange','value'=>'true'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
