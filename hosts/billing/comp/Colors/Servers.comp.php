<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('IsActive','IsDefault');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Color = ($IsActive)?'FFFFFF':'DCDCDC';
#Debug(SPrintF('[comp/Colors/Servers]: Color = %s',$Color));
#-------------------------------------------------------------------------------
if($IsDefault && $IsActive)
	$Color = 'D5F66C';
#Debug(SPrintF('[comp/Colors/Servers]: Color = %s',$Color));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('bgcolor' => $Color);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
