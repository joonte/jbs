<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$DSOrderID	= (integer) @$Args['DSOrderID'];
$Command	=  (string) @$Args['Command'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/IPMI.SuperMicro.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Command)
	return new gException('IPMI_COMMAND_NOT_SELECT','Не выбрана команда');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!In_Array($Command,Array('on','off','soft','cycle','reset','mc')))
	return new gException('IPMI_COMMAND_NOT_FOUND','Неизвестная команда IPMI');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// чтобы не тыкали кнопки со скоростью света, кэшируем факт нажатия на 2 минуты
$CacheID = Md5(SPrintF('IPMI_%s',$DSOrderID));
#-------------------------------------------------------------------------------
$Result = CacheManager::get($CacheID);
#-------------------------------------------------------------------------------
if($Result)
	if($Result + 2*60 > Time() && !$__USER['IsAdmin'])
		return new gException('IPMI_RATE_TOO_HIGH',SprintF('Управляющие команды нельзя подавать слишком часто, попробуйте через %s секунд',$Result + 2*60 - Time()));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DSOrder = DB_Select('DSOrdersOwners',Array('ID','UserID','StatusID','SchemeID'),Array('UNIQ','ID'=>$DSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrder)){
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
##-------------------------------------------------------------------------------
if($DSOrder['StatusID'] != 'Active' && !$__USER['IsAdmin'])
        return new gException('DS_ORDER_NOT_ACTIVE','Заказ выделенного сервера не активен');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DSScheme = DB_Select('DSSchemes','*',Array('UNIQ','ID'=>$DSOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($DSScheme)){
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
$IsPermission = Permission_Check('DSManage',(integer)$__USER['ID'],(integer)$DSOrder['UserID']);
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
// надо достать реальный статус сервера, если дают несовместимую команду - ругаться будем
$Status = IPMI_PowerGet($DSScheme);
if(Is_Exception($Status))
	return new gException('IPMI_PowerGet',$Status->String);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Command == 'mc'){
	#-------------------------------------------------------------------------------
	// перезагрузка контроллера
	$Result = IPMI_Command($DSScheme,'mc reset cold');
	if(Is_Exception($Result))
		return new gException('IPMI_mc_reset_cold',$Result->String);
	#-------------------------------------------------------------------------------
	CacheManager::add($CacheID,Time(),120);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return new gException('IPMI_mc_reset_cold_success','Контроллер IPMI успешно перезагружен');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// команды по питанию
$Result = IPMI_Command($DSScheme,SPrintF('chassis power %s',$Command));
if(Is_Exception($Result))
	return new gException('IPMI_mc_reset_cold',$Result->String);
#-------------------------------------------------------------------------------
CacheManager::add($CacheID,Time(),120);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return new gException('IPMI_command_executed',SPrintF('Выполнена команда: %s',$Command));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
