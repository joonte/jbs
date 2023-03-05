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
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// профили
$Profiles = Array('Templates'=>Array());
#-------------------------------------------------------------------------------
foreach(Array_Keys($Config['Profiles']['Templates']) as $Key){
	#-------------------------------------------------------------------------------
	$Template = System_XML(SPrintF('profiles/%s.xml',$Key));
	if(Is_Error($Template))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Profiles['Templates'][$Key] = $Config['Profiles']['Templates'][$Key];
	#-------------------------------------------------------------------------------
	$Profiles['Templates'][$Key]['Template'] = $Template;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Out['Profiles'] = $Profiles;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// договора
$Out['Contracts'] = $Config['Contracts'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// счета на оплату, платёжные системы
$PaymentSystems = Array();
#-------------------------------------------------------------------------------
foreach(Array_Keys($Config['Invoices']['PaymentSystems']) as $Key){
	#-------------------------------------------------------------------------------
	$PaymentSystem = $Config['Invoices']['PaymentSystems'][$Key];
	#-------------------------------------------------------------------------------
	// а в старых платежах может быть...
	//if(!$PaymentSystem['IsActive'])
	//	continue;
	#-------------------------------------------------------------------------------
	$Out['PaymentSystems'][$Key] = Array('Name'=>$PaymentSystem['Name'],'SystemDescription'=>$PaymentSystem['SystemDescription'],'ContractsTypes'=>$PaymentSystem['ContractsTypes'],'IsContinuePaying'=>$PaymentSystem['IsContinuePaying'],'Course'=>$PaymentSystem['Course'],'Measure'=>$PaymentSystem['Course'],'Valute'=>$PaymentSystem['Valute'],'MinimumPayment'=>$PaymentSystem['MinimumPayment'],'MaximumPayment'=>$PaymentSystem['MaximumPayment'],'IsActive'=>$PaymentSystem['IsActive']);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// уведомленния
$Out['Notifies'] = $Config['Notifies'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// тикеты
$Out['Edesks'] = $Config['Edesks'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// сервисы
$Out['Services'] = $Config['Services'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// статусы
$Out['Statuses'] = $Config['Statuses'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// докУменты
$Out['MotionDocuments'] = $Config['MotionDocuments'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

