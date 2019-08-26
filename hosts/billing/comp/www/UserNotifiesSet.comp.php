<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/Tree.php','libs/Server.php')))
  return ERROR | @Trigger_Error(500);
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
$Config = Config();
#-------------------------------------------------------------------------------
$Notifies = $Config['Notifies'];
#-------------------------------------------------------------------------------
$Methods = $Notifies['Methods'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Methods['SMS']['IsActive']){
	#-------------------------------------------------------------------------------
	$ServersSettings = Comp_Load('Servers/SMSSelectServer',$__USER['Params']['NotificationMethods']['SMS']['Address']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($ServersSettings)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		break;
	case 'integer':
		#-------------------------------------------------------------------------------
		# чопик для крона, когад сервер не найден
		return $ServersSettings;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(100);
	}
	#-------------------------------------------------------------------------------
	$ServerSettings = $ServersSettings[0];
	#-------------------------------------------------------------------------------
	$MobileCountry = $ServersSettings[1];
	#-------------------------------------------------------------------------------
	$SMSPrice = $ServersSettings[2];
	#-------------------------------------------------------------------------------
	if($__USER['Params']['NotificationMethods']['SMS']['Confirmed'] == 0){
		#-------------------------------------------------------------------------------
		$Row2 = Array(new Tag('TD', Array('colspan' => (SizeOf($Methods) + 1), 'class' => 'Standard', 'style' => 'background-color:#FDF6D3;'), 'Для настройки SMS уведомлений, подтвердите свой номер телефона'));
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Message = SPrintF('SMS платные (%s), включайте только "Уведомления о блокировках заказов"',$SMSPrice);
		#-------------------------------------------------------------------------------
		# прочкать SMSExceptionsPaidInvoices, если надо - получить сумму счетов, надпись по итогам вывести
		if(FloatVal($ServerSettings['Params']['ExceptionsPaidInvoices']) >= 0){
			#-------------------------------------------------------------------------------
			$IsSelect = DB_Select('InvoicesOwners','SUM(`Summ`) AS `Summ`',Array('UNIQ','Where'=>SPrintF('`UserID` = %u AND `IsPosted` = "yes" AND `StatusDate` > UNIX_TIMESTAMP() - %u * 24 * 60 *60',$__USER['ID'],$ServerSettings['Params']['ExceptionsPaidInvoicesPeriod'])));
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
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if(Str_Replace(',','.',$ServerSettings['Params'][$MobileCountry]) == 0)
			$Message = SPrintF('Для вашей страны (%s) сообщения через SMS бесплатны',$MobileCountry);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Row2 = Array(new Tag('TD', Array('colspan' => (SizeOf($Methods) + 1), 'class' => 'Standard', 'style' => 'background-color:#FDF6D3;'), $Message));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	if(FloatVal($ServerSettings['Params']['ExceptionsPaidInvoices']) == 0 && $__USER['Params']['NotificationMethods']['SMS']['Confirmed'] > 0)
		UnSet($Row2);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Row = Array(new Tag('TD',Array('class'=>'Head'),'Тип сообщения'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$uNotifies = Array();
#-------------------------------------------------------------------------------
foreach(Array_Keys($Methods) as $MethodID){
	#-------------------------------------------------------------------------------
	$Method = $Methods[$MethodID];
	#-------------------------------------------------------------------------------
	if(!$Method['IsActive'])
		continue;
	#-------------------------------------------------------------------------------
	$uNotifies[$MethodID] = Array();
	#-------------------------------------------------------------------------------
	$Row[] = new Tag('TD',Array('class'=>'Head'),$MethodID);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = IsSet($Row2)?Array($Row2, $Row):Array($Row);
#-------------------------------------------------------------------------------
$Rows = DB_Select('Notifies','*',Array('Where'=>SPrintF('`UserID` = %u',$__USER['ID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($Rows)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Rows as $Row)
		$uNotifies[$Row['MethodID']][] = $Row['TypeID'];
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Types = $Notifies['Types'];
$Code = 'Default';
#-------------------------------------------------------------------------------
foreach(Array_Keys($Types) as $TypeID){
	#-------------------------------------------------------------------------------
	#Debug(SPrintF('[comp/www/UserNotifiesSet]: TypeID = %s',$TypeID));
	$Type = $Types[$TypeID];
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
	# проверяем, есть ли такие услуги у юзера
	$Code = IsSet($Type['Code'])?$Type['Code']:$Code;
	$Regulars = SPrintF('/^%s/',$Code);
	#-------------------------------------------------------------------------------
	if(Preg_Match($Regulars,$TypeID)){
		#-------------------------------------------------------------------------------
		# код уведомления совпадает с уведомлением
		$Count = DB_Count(SPrintF('%sOrdersOwners',$Code),Array('Where'=>SPrintF('`UserID` = %u',$__USER['ID'])));
		if(Is_Error($Count))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if(!$Count)
			continue;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(IsSet($Type['Title']))
		$Table[] = Array(new Tag('TD',Array('colspan'=>(SizeOf($Methods) + 1),'class'=>'Separator'),$Type['Title']));
	#-------------------------------------------------------------------------------
	$Row = Array(new Tag('TD',Array('class'=>'Comment'),$Type['Name']));
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Methods) as $MethodID){
		#-------------------------------------------------------------------------------
		$Method = $Methods[$MethodID];
		#-------------------------------------------------------------------------------
		if(!$Method['IsActive'])
			continue;
		#-------------------------------------------------------------------------------
		$UseName = SPrintF('Use%s',$MethodID);
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load(
				'Form/Input',
				Array(
					'name'	=> SPrintF('%s[]',$MethodID),
					'type'	=> 'checkbox',
					'value'	=> $TypeID,
					'prompt'=> (IsSet($Type[$UseName]) && !$Type[$UseName])?'Данная настройка отключена администратором':SPrintF('Настройка уведомления %s',$MethodID)
					)
				);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		// Если контакт не подтвержден то не выводить активными галочки для смс.
		if($MethodID != 'Email' && !$__USER['Params']['NotificationMethods'][$MethodID]['Confirmed']){
			#-------------------------------------------------------------------------------
			$Comp->AddAttribs(Array('disabled'=>'true'));
			#-------------------------------------------------------------------------------
		}else{
			#Debug(SPrintF('[comp/www/UserNotifiesSet]: ', $MobileCountry));
			#-------------------------------------------------------------------------------
			if(IsSet($Type[$UseName]) && !$Type[$UseName])
				$Comp->AddAttribs(Array('disabled'=>'true'));
			#-------------------------------------------------------------------------------
			if(!In_Array($TypeID,$uNotifies[$MethodID]))
				$Comp->AddAttribs(Array('checked'=>'true'));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Row[] = new Tag('TD',Array('align'=>'center'),$Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Table[] = $Row;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'type'    => 'button',
    'onclick' => 'UserNotifiesSet();',
    'value'   => 'Сохранить'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('TD',Array('colspan'=>(SizeOf($Methods) + 1),'align'=>'right'),$Comp));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Extended',$Table);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tab','User/Settings',new Tag('FORM',Array('name'=>'UserNotifiesSetForm','onsubmit'=>'return false;'),$Comp));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------

?>
