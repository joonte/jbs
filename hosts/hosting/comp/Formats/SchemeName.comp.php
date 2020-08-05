<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Name','Code','OrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/

// если не задано имя тарифа - возвращаем прочерк
if(!$Name)
	return '-';
#-------------------------------------------------------------------------------
// если не задан код услуги или номер заказа - возвращаем имя тарифа
if(!$Code || !$OrderID)
	return $Name;
#-------------------------------------------------------------------------------
// длинну строки захардкодим
$Comp = Comp_Load('Formats/String',$Name,10);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return new Tag('A',Array('href'=>SPrintF('javascript:ShowWindow(\'/%sOrderSchemeChange\',{%sOrderID:%s});',$Code,$Code,$OrderID)),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
