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
$OrderID	= (integer) @$Args['OrderID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
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
$DOM->AddAttribs('MenuLeft',Array('args'=>'User/Services'));
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Услуги → Домены → Мои заказы');
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// передан номер заказа, надо открыть окно на оплату
if($OrderID)
	$DOM->AddAttribs('Body',Array('onload'=>SPrintF("ShowWindow('/DomainOrderPay',{OrderID:'%u'});",$OrderID)));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = DB_Count('Services',Array('Where'=>"`ID` = 20000 AND `IsActive` = 'yes'"));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count){
	#-------------------------------------------------------------------------------
	$Comp1 = Comp_Load('Buttons/Standard',Array('onclick'=>"ShowWindow('/DomainOrder');"),'Новый заказ','Add.gif');
	if(Is_Error($Comp1))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp2 = Comp_Load('Buttons/Standard',Array('onclick'=>"ShowWindow('/Clause',{ClauseID:'/Help/Services/Paying'});"),'Оплатить (продлить) заказ','Pay.gif');
	if(Is_Error($Comp2))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp3 = Comp_Load('Buttons/Standard',Array('onclick'=>"ShowWindow('/DomainTransfer');"),'Перенос домена','DomainTransfer.png');
	if(Is_Error($Comp3))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Buttons/Panel',Array('Comp'=>$Comp1,'Name'=>'Новый заказ'),Array('Comp'=>$Comp2,'Name'=>'Оплатить (продлить) заказ'),Array('Comp'=>$Comp3,'Name'=>'Перенос домена'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$NoBody->AddChild($Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Super','DomainOrders[User]');
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$NoBody->AddChild($Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tab','User/Domain',$NoBody);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
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

?>
