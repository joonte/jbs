<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('StatusID','IsHidden');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
switch($StatusID){
case 'OnForming':
	$Color = 'F9E47D';
	break;
case 'Public':
	$Color = 'F1FCCE';
	break;
case 'Complite':
	$Color = 'D5F66C';
	break;
default:
	$Color = '999999';
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// если это скрытый договор, то меняем цвет на "ламантин"
if($IsHidden)
	$Color = '979AAA';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('bgcolor'=>SPrintF('#%s',$Color));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
