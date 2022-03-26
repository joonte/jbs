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
$ServiceOrderID	= (integer) @$Args['ServiceOrderID'];
$OrderID	= (integer) @$Args['OrderID'];
$ServiceID	= (integer) @$Args['ServiceID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# достаём сервис
$Service = DB_Select('ServicesOwners',Array('ID','Code','NameShort','Name'),Array('UNIQ','ID'=>$ServiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Service)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVICE_NOT_FOUND','Указанный сервис не найден');
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём данные заказа
$Where = ($ServiceOrderID?SPrintF('`ID` = %u',$ServiceOrderID):SPrintF('`OrderID` = %u',$OrderID));
#-------------------------------------------------------------------------------
$Order = DB_Select(SPrintF('%sOrdersOwners',$Service['Code']),Array('*'),Array('UNIQ','Where'=>$Where));
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
switch(ValueOf($IsConsiderManage)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	# No more...
	break;
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$OrdersConsider = DB_Select('OrdersConsider','*',Array('Where'=>SPrintF('`OrderID` = %u',$Order['OrderID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($OrdersConsider)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('NO_CONSIDER','Учёт отсутствует, вероятно, заказ ещё неоплачен');
case 'array':
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
$DOM->AddText('Title',SPrintF('Данные учёта "%s", "%s"',(!In_Array($ServiceID,Array(40000,51000)))?$Order['Login']:SprintF('#%u/%s',$Order['OrderID'],$Order['IP']),$Service['Name']));
#-------------------------------------------------------------------------------
$Table = Array(SPrintF('Способ учета, "%s", %s',(!In_Array($ServiceID,Array(40000,51000)))?$Order['Login']:SprintF('#%u/%s',$Order['OrderID'],$Order['IP']),$Service['NameShort']));
#-------------------------------------------------------------------------------
$Row = Array();
#-------------------------------------------------------------------------------
foreach(Array('Дн. зарез.','Дн. ост.','Дн. не учт.','Цена','Скидка') as $Text)
	$Row[] = new Tag('TD',Array('class'=>'Head'),$Text);
#-------------------------------------------------------------------------------
$Rows = Array($Row);
#-------------------------------------------------------------------------------
$RemainderSumm = $UserRemainderSumm = 0.00;
#-------------------------------------------------------------------------------
for($i=0;$i<Count($OrdersConsider);$i++){
	#-------------------------------------------------------------------------------
	$ConsiderItem = $OrdersConsider[$i];
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Percent',$ConsiderItem['Discont']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Row = Array();
	#-------------------------------------------------------------------------------
	if($IsConsiderManage){
		#-------------------------------------------------------------------------------
		foreach(Array('DaysReserved','DaysRemainded','DaysConsidered','Cost','Discont') as $ParamID){
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load(
					'Form/Input',
					Array(
						'type'  => 'text',
						'name'  => SPrintF('OrdersConsider[%u][]',$i),
						'style' => 'width: 80px',
						'value' => $ConsiderItem[$ParamID]
						)
					);
			#-------------------------------------------------------------------------------
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Row[] = new Tag('TD',$Comp);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Row[] = (integer)$ConsiderItem['DaysReserved'];
		$Row[] = (integer)$ConsiderItem['DaysRemainded'];
		$Row[] = (integer)$ConsiderItem['DaysConsidered'];
		$Row[] = (float)$ConsiderItem['Cost'];
		$Row[] = $Comp;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Rows[] = $Row;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// сумма возврата с учётом скидок
	$RemainderSumm	+= (float)$ConsiderItem['Cost']*(integer)$ConsiderItem['DaysRemainded']*(1 - (float)$ConsiderItem['Discont']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// при ручноё правке, возможно что DaysRemainded > DaysReserved.
	if($ConsiderItem['DaysRemainded'] > $ConsiderItem['DaysReserved'])
		continue;
	#-------------------------------------------------------------------------------
	// оплата за использованные дни, без учёта скидок
	$NoDiscontPayment = ($ConsiderItem['DaysReserved'] - $ConsiderItem['DaysRemainded'])*$ConsiderItem['Cost'];
	#-------------------------------------------------------------------------------
	// реальная сумма оплаты за эту строку учёта
	$PaymentSumm = $ConsiderItem['DaysReserved']*$ConsiderItem['Cost']*(1 - $ConsiderItem['Discont']);
	#-------------------------------------------------------------------------------
	// если оплата за использованные дни больше реальнооплаченной суммы - возврата за этот период нет
	if($NoDiscontPayment >= $PaymentSumm)
		continue;
	#-------------------------------------------------------------------------------
	// сумма возврата без учёта скидок
	$UserRemainderSumm += $PaymentSumm - $NoDiscontPayment;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Extended',$Rows);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = new Tag('DIV',Array('align'=>'center'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем условные счета, если они есть - то разные сообщения админу и запрет возврата юзеру
$Where = SPrintF("`StatusID` = 'Conditionally' AND `UserID` = %u",$Order['UserID']);
$IsConditionally = DB_Count('InvoicesOwners',Array('Where'=>$Where));
#-------------------------------------------------------------------------------
if(Is_Error($IsConditionally))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// дефолтовый текст возврата денег
$RefundText = 'Вы действительно хотите осуществить возврат средств?';
#-------------------------------------------------------------------------------
// текст если есть условные счета
if($IsConditionally)
	$RefundText = SPrintF('Обратите внимание, что производится возврат на балланс для пользователя у которого есть условно оплаченные счета. %s',$RefundText);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($IsConsiderManage){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'type'    => 'button',
				'onclick' => "AjaxCall('/Administrator/API/OrderConsider',FormGet(form),'Сохранение способа учета','GetURL(document.location);');",
				'value'   => 'Сохранить'
				)
			);
	#-------------------------------------------------------------------------------
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',Array('align'=>'right'),$Comp);
	#-------------------------------------------------------------------------------
	if($RemainderSumm){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Formats/Currency',$RemainderSumm);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load(
				'Form/Input',
				Array(
					'type'		=> 'button',
					'onclick'	=> SPrintF("javascript:ShowConfirm('%s','AjaxCall(\'/API/OrderRestore\',FormGet(OrderConsiderInfoForm),\'Возврат денег\',\'GetURL(document.location);\');');",$RefundText),
					'value'		=> SPrintF('Вернуть %s',$Comp),
					'prompt'	=> 'Возврат с учётом скидок'
					)
				);
		#-------------------------------------------------------------------------------
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Div->AddChild($Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Table[] = $Div;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// кнопка возрата средств, юзер-левел
if($UserRemainderSumm){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Currency',$UserRemainderSumm);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// что происходит по клику
	$OnClick = "javascript:ShowConfirm('Сделать возрат?','AjaxCall(\'/API/OrderRestore\',FormGet(OrderConsiderInfoForm),\'Возврат денег\',\'GetURL(document.location);\');');";
	#-------------------------------------------------------------------------------
	if($IsConditionally){
		#-------------------------------------------------------------------------------
		if($IsConsiderManage){
			#-------------------------------------------------------------------------------
			$OnClick = SPrintF("javascript:ShowConfirm('%s','AjaxCall(\'/Administrator/API/OrderRestore\',FormGet(OrderConsiderInfoForm),\'Возврат денег\',\'GetURL(document.location);\');');",$RefundText);
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$OnClick = "ShowAlert('У вас есть условно оплаченные счета, возврат невозможен','Warning')";
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'type'		=> 'button',
				'onclick'	=> $OnClick,
				'value'		=> SPrintF('Вернуть %s',$Comp),
				'prompt'	=> ($IsConsiderManage)?'Возврат БЕЗ учёта скидок':'Сделать возрат неизрасходованных средств на балланс договора'
				)
			);
	#-------------------------------------------------------------------------------
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// если суммы одинаковые, не показываем админу вторую кнопку
	if(!$IsConsiderManage || ($IsConsiderManage && $UserRemainderSumm != $RemainderSumm)){
		#-------------------------------------------------------------------------------
		$Div = new Tag('DIV',Array('align'=>'right'),$Comp);
		#-------------------------------------------------------------------------------
		$Table[] = $Div;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('method'=>'POST','name'=>'OrderConsiderInfoForm'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'  => 'hidden',
			'name'  => 'OrderID',
			'value' => $Order['OrderID']
			)
		);
#-------------------------------------------------------------------------------
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
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
