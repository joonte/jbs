<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ContactID = (integer) @$Args['ContactID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/Tree.php','libs/Server.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ContactID)
	return new gException('CONTACT_ID_NOT_SET','Не указан идентификатор контакта');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$Count = DB_Count('Contacts',Array('Where'=>SPrintF('`ID` = %u AND `UserID` = %u',$ContactID,$__USER['ID'])));
if(!$Count)
	return new gException('CONTACT_NOT_FOUND',SPrintF('Контакт (%u) не найден или Вам не принадлежит',$ContactID));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Contact = DB_Select('Contacts','*',Array('UNIQ','ID'=>$ContactID));
switch(ValueOf($Contact)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(100);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Contact['Confirmed'])
	return new gException('CONTACT_NOT_FOUND',SPrintF('Контактный адрес (%s) не подтверждён. Для настройки уведомлений, его необходимо подтвердить',$Contact['Address']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Config['Notifies']['Methods'][$Contact['MethodID']]['IsActive'])
	return new gException('METHOD_ID_DISABLED',SPrintF('Метод уведомления %s отключён и более не используется',$Contact['MethodID']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Настройка уведомлений');
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/UserNotifiesSet.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// заголовок таблицы
$Table = Array(SPrintF('%s / %s',$Contact['MethodID'],$Contact['Address']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// если это SMS, достаём цену, пишем объявление о стоимости
if($Contact['MethodID'] == 'SMS'){
	#-------------------------------------------------------------------------------
	$ServersSettings = Comp_Load('Servers/SMSSelectServer',$Contact['Address']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($ServersSettings)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		break;
	case 'integer':
		return new gException('SMS_SERVER_NOT_FOUND',SPrintF('Сервер для отправки SMS уведомлений не найден, невозможно настроить параметры уведомлений'));
	default:
		return ERROR | @Trigger_Error(100);
	}
	#-------------------------------------------------------------------------------
	$ServerSettings = $ServersSettings[0];
	#-------------------------------------------------------------------------------
	$Country = $ServersSettings[1];
	#-------------------------------------------------------------------------------
	$Price = $ServersSettings[2];
	#-------------------------------------------------------------------------------
	$Message = SPrintF('SMS платные (%s), включайте только "Уведомления о блокировках заказов"',$Price);
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/UserNotifiesSet]: SMS Price = %s',$Price));
	#-------------------------------------------------------------------------------
	// прочeкать SMSExceptionsPaidInvoices, если надо - получить сумму счетов, надпись по итогам вывести
	if(FloatVal($ServerSettings['Params']['ExceptionsPaidInvoices']) >= 0){
		#-------------------------------------------------------------------------------
		$IsSelect = DB_Select('InvoicesOwners','SUM(`Summ`) AS `Summ`',Array('UNIQ','Where'=>SPrintF('`UserID` = %u AND `IsPosted` = "yes" AND `StatusDate` > UNIX_TIMESTAMP() - %u * 24 * 60 *60',$__USER['ID'],$ServerSettings['Params']['ExceptionsPaidInvoicesPeriod'])));
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsSelect)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Formats/Currency',$IsSelect['Summ']);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/UserNotifiesSet]: оплачено счетов на сумму (%s)', $Comp));
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Formats/Currency',FloatVal($ServerSettings['Params']['ExceptionsPaidInvoices']));
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Message = ($IsSelect['Summ'] >= FloatVal($ServerSettings['Params']['ExceptionsPaidInvoices']))?SPrintF('Сумма ваших оплаченных счетов больше %s, SMS для вас бесплатны',$Comp):$Message;
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(100);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if(Str_Replace(',','.',$ServerSettings['Params'][$Country]) == 0)
			$Message = SPrintF('Для вашей страны (%s) сообщения через SMS бесплатны',$Country);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Table[] = new Tag('TR',new Tag('TD',Array('colspan'=>2,'class'=>'Standard','style' => 'background-color:#FDF6D3;'),$Message));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = new Tag('TR',new Tag('TD',Array('class'=>'Head'),'Тип сообщения'),new Tag('TD',Array('class'=>'Head'),$Contact['MethodID']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём юзерские настройки уведомлений, т.е. отключенные уведомления
$uNotifies = Array();
#-------------------------------------------------------------------------------
$Notifies = DB_Select('Notifies','*',Array('Where'=>SPrintF('`ContactID` = %u',$ContactID)));
#-------------------------------------------------------------------------------
switch(ValueOf($Notifies)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	// собираем в массив уведомления отключенные юзером
	#-------------------------------------------------------------------------------
	foreach($Notifies as $Value)
		$uNotifies[] = $Value['TypeID'];
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(100);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// перебираем уведомления, строим строчки с галочками
$Code = 'Default';
$Services = Array();
#-------------------------------------------------------------------------------
foreach(Array_Keys($Config['Notifies']['Types']) as $TypeID){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/UserNotifiesSet]: TypeID = %s',$TypeID));
	#-------------------------------------------------------------------------------
	$Type = $Config['Notifies']['Types'][$TypeID];
	#-------------------------------------------------------------------------------
	// проверяем группу для уведомлений
	if(!IsSet($Type['GroupID']))
		continue;
	#-------------------------------------------------------------------------------
	$Entrance = Tree_Entrance('Groups',(integer)$Type['GroupID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($Entrance)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		if(!In_Array($GLOBALS['__USER']['GroupID'],$Entrance))
			continue 2;
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// проверяем, есть ли такие услуги у юзера
	$Code = IsSet($Type['Code'])?$Type['Code']:$Code;
	#-------------------------------------------------------------------------------
	$Regulars = SPrintF('/^%s/',$Code);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Preg_Match($Regulars,$TypeID)){
		#-------------------------------------------------------------------------------
		// проверяем по ранее исканному
		if(!In_Array($Code,$Services)){
			#-------------------------------------------------------------------------------
			# код уведомления совпадает с уведомлением
			$Count = DB_Count(SPrintF('%sOrdersOwners',$Code),Array('Where'=>SPrintF('`UserID` = %u',$__USER['ID'])));
			if(Is_Error($Count))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			// сохраняем в массив значение, чтобы не искать снова по БД на следующей галке то что уже есть
			if($Count)
				$Services[] = $Code;
			#-------------------------------------------------------------------------------
			if(!$Count)
				continue;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// шапочка подраздела, если она задана в конфиге
	if(IsSet($Type['Title']))
		$Table[] = $Type['Title'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$UseName = SPrintF('Use%s',$Contact['MethodID']);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'name'	=> SPrintF('%s[]',$Contact['MethodID']),
				'type'	=> 'checkbox',
				'value'	=> $TypeID,
				'prompt'=> (IsSet($Type[$UseName]) && !$Type[$UseName])?'Данная настройка отключена администратором':SPrintF('Изменить настройки'),
				'id'	=> $TypeID
				)
			);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// этот тип уведомлений отключен глобально
	if(IsSet($Type[$UseName]) && !$Type[$UseName])
		$Comp->AddAttribs(Array('disabled'=>'true'));
	#-------------------------------------------------------------------------------
	if(!In_Array($TypeID,$uNotifies))
		$Comp->AddAttribs(Array('checked'=>'true'));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Table[] = Array(new Tag('LABEL',Array('for'=>$TypeID),$Type['Name']),$Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'    	=> 'button',
			'onclick'	=> 'UserNotifiesSet();',
			'value'		=> 'Сохранить'
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#---------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'UserNotifiesSetForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
#---------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'ContactID','type'=>'hidden','value'=>$ContactID));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#---------------------------------------------------------------------------
$Form->AddChild($Comp);
#---------------------------------------------------------------------------
#---------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'MethodID','type'=>'hidden','value'=>$Contact['MethodID']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#---------------------------------------------------------------------------
$Form->AddChild($Comp);
#---------------------------------------------------------------------------
#---------------------------------------------------------------------------
#$Comp = Comp_Load('Tab','User/Settings',new Tag('FORM',Array('name'=>'UserNotifiesSetForm','onsubmit'=>'return false;'),$Comp));
#if(Is_Error($Comp))
#	return ERROR | @Trigger_Error(500);
##-------------------------------------------------------------------------------
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
