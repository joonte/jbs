<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
#if(Is_Error(System_Load('modules/Authorisation.mod')))
#	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$ContactID	= (integer) @$Args['ContactID'];
$TypeID		=  (string) @$Args['TypeID'];
$Code		=  (string) @$Args['Code'];
$IsConfirm	= (boolean) @$Args['IsConfirm'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Base')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Отключение уведомлений');
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Code || !$TypeID || !$ContactID){
	#-------------------------------------------------------------------------------
	$DOM->AddAttribs('Body',Array('onload'=>"ShowAlert('Отсутствуют параметры отключения уведомлений','Warning');setTimeout(function(){location.href = '/Logon';},30000);"));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$NoBody);
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём даные, по контакту
$Contact = DB_Select('Contacts',Array('UserID','MethodID','Address','(SELECT `UniqID` FROM `Users` WHERE `Users`.`ID` = `Contacts`.`UserID`) AS `UniqID`'),Array('UNIQ','ID'=>$ContactID));
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
// проверяем подтверждение
$uCode = Hash('sha256',SPrintF('%s%s%s',Hash('sha256',$ContactID),Hash('sha256',$TypeID),Hash('sha256',$Contact['UniqID'])));
#-------------------------------------------------------------------------------
if($uCode != $Code){
	#-------------------------------------------------------------------------------
	$DOM->AddAttribs('Body',Array('onload'=>"ShowAlert('Неверные параметры','Warning');setTimeout(function(){location.href = '/Logon';},30000);"));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$NoBody);
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем наличие такого типа уведомления
$Config = Config();
#-------------------------------------------------------------------------------
$Types = $Config['Notifies']['Types'];
#-------------------------------------------------------------------------------
if(!IsSet($Types[$TypeID]) || !$Types[$TypeID]['IsActive']){
	#-------------------------------------------------------------------------------
	$DOM->AddAttribs('Body',Array('onload'=>"ShowAlert('Тип оповещения не существует, или отключён','Warning');setTimeout(function(){location.href = '/Logon';},30000);"));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$NoBody);
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем, не отписан ли от этого уведомления
$Count = DB_Count('Notifies',Array('Where'=>SPrintF('`ContactID` = %u AND `TypeID` = "%s"',$ContactID,DB_Escape($TypeID))));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count){
	#-------------------------------------------------------------------------------
	$DOM->AddAttribs('Body',Array('onload'=>SPrintF("ShowAlert('Вы уже отписаны от оповещений %s / %s','Warning');setTimeout(function(){location.href = '/Logon';},30000);",$Contact['Address'],$Types[$TypeID]['Name'])));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$NoBody);
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$IsConfirm){
	#-------------------------------------------------------------------------------
	// первый заход, показываем подтверждение отписки
	#-------------------------------------------------------------------------------
	$OnLoad = "JavaScript:ShowConfirm('Вы действительно хотите отписаться от уведомлений?','AjaxCall(\'/API/UnSubScribe\',FormGet(UnSubScribeForm),\'Отписка от уведомлений\',\'setTimeout(function(){location.href = \\\\\'/Logon\\\\\';},1000);\');');";
	#-------------------------------------------------------------------------------
	$DOM->AddAttribs('Body',Array('onload'=>$OnLoad));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// собираем значения формы на отпарвку
	$Form = new Tag('FORM',Array('name'=>'UnSubScribeForm','onsubmit'=>'return false;'));
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'Code','type'=>'hidden','value'=>$uCode));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'ContactID','type'=>'hidden','value'=>$ContactID));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'IsConfirm','type'=>'hidden','value'=>1));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'TypeID','type'=>'hidden','value'=>$TypeID));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$NoBody->AddChild($Form);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$NoBody);
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------

}else{
	#-------------------------------------------------------------------------------
	// отпарвка формы, всё проверно выше, вставляем данные в БД
	$INotify = Array('ContactID'=>$ContactID,'TypeID'=>DB_Escape($TypeID));
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('Notifies',$INotify);
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Types) as $Key){
		#-------------------------------------------------------------------------------
		if(IsSet($Types[$Key]['Title']))
			$Title = $Types[$Key]['Title'];
		#-------------------------------------------------------------------------------
		$Name = $Types[$Key]['Name'];
		#-------------------------------------------------------------------------------
		if($Key == $TypeID)
			break;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Event = Array('UserID'=>$Contact['UserID'],'PriorityID'=>'Billing','Text'=>SPrintF('Отключены оповещения для %s / %s / %s',$Contact['Address'],$Title,$Name));
	#-------------------------------------------------------------------------------
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return Array('Status'=>'Ok');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------


?>
