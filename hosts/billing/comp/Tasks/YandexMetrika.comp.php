<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['YandexMetrika'];
#-------------------------------------------------------------------------------
#Debug(SPrintF('[comp/Tasks/Taxation]: Settings = %s',print_r($Settings,true)));
#-------------------------------------------------------------------------------
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecutePeriod'=>$Settings['ExecutePeriod']));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# если неактивна, то через день запуск
if(!$Settings['IsActive'])
	return 24*3600;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// настройки перезадём, в настройках задачи нет ничего
$Settings = $Config['Interface']['User']['YandexMetrika'];
#-------------------------------------------------------------------------------
// если метрика НЕ включена, то всё
if(!$Settings['IsActive'] || !$Settings['YandexCounterId'] || !$Settings['Token'])
        return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// библиотеки для работы
if(Is_Error(System_Load('libs/HTTP.php','libs/YandexMetrika.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// выхлоп задачи
$GLOBALS['TaskReturnInfo'] = Array('Contacts'=>Array(),'Invoices'=>Array());
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// инициализируем либу
$YandexMetrika = new YandexMetrika($Settings['Token'],$Settings['YandexCounterId']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// выбираем контакты
$Select = $YandexMetrika->SelectClients();
#-------------------------------------------------------------------------------
switch(ValueOf($Select)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'boolean':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	// чё-то выбралось. отправляем.
	if(Is_Error($Send = $YandexMetrika->SendClients($Select['Contacts'])))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	// удаляем записи из временной таблицы
	if(Is_Error($Delete = $YandexMetrika->DeleteRecords($Select['Deleted'])))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo']['Contacts'][] = SizeOf($Select['Contacts']);
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// выбираем оплаты
$Select = $YandexMetrika->SelectOrders();
#-------------------------------------------------------------------------------
switch(ValueOf($Select)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'boolean':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	// чё-то выбралось. отправляем.
	if(SizeOf($Select['Orders']['IN_PROGRESS']) > 0 || SizeOf($Select['Orders']['PAID']) > 0 || SizeOf($Select['Orders']['CANCELLED']) > 0)
		if(Is_Error($Send = $YandexMetrika->SendOrders($Select['Orders'])))
			return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	// удаляем записи из временной таблицы
	if(Is_Error($Delete = $YandexMetrika->DeleteRecords($Select['Deleted'])))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo']['Invoices'][] = SizeOf($Select['Orders']['IN_PROGRESS']) + SizeOf($Select['Orders']['PAID']) + SizeOf($Select['Orders']['CANCELLED']);
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(SizeOf($GLOBALS['TaskReturnInfo']['Invoices']) < 1)
	UnSet($GLOBALS['TaskReturnInfo']['Invoices']);
#-------------------------------------------------------------------------------
if(SizeOf($GLOBALS['TaskReturnInfo']['Contacts']) < 1)
	UnSet($GLOBALS['TaskReturnInfo']['Contacts']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
