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
$ServiceOrderID		= (integer) @$Args['ServiceOrderID'];
$ServiceOrderType	=  (string) @$Args['ServiceOrderType'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ServiceOrderType == 'Default')
	return new gException('NO_SCHEMES','Нет тарифных планов для смены. Обратитесь в "Поддержку" для смены параметров услуги');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ServiceOrderType == 'DS')
	return new gException('NO_DS_SCHEMES','У выделенного сервера нельзя сменить тариф. Но, вы можете обратится в "Поддержку", и рассказать, что именно в железе вам надо поменять. Сотрудники расскажут вам, что возможно, а что - нет');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Order = DB_Select(SPrintF('%sOrdersOwners',$ServiceOrderType),Array('ID'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$ServiceOrderID)));
#-----------------------------------------------------------------------
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
#-----------------------------------------------------------------------
#-----------------------------------------------------------------------
if($ServiceOrderType == 'Domain'){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/DomainOrderNsChange',Array('DomainOrderID'=>$Order['ID']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------

}else{
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load(SPrintF('www/%sOrderSchemeChange',$ServiceOrderType),Array(SPrintF('%sOrderID',$ServiceOrderType)=>$Order['ID']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Comp;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
