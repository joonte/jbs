<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params','Key');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#Debug(print_r($Params,true));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!Is_Array($Params)){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Explode/JSON',$Params);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Params = $Comp;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
//Debug(print_r($Params,true));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Path = Explode('/',$Key);
#-------------------------------------------------------------------------------
//Debug(print_r($Path,true));
if(SizeOf($Path) == 1){
	#-------------------------------------------------------------------------------
	if(IsSet($Params[$Key]))
		return $Params[$Key];
	#-------------------------------------------------------------------------------
}elseif(SizeOf($Path) == 2){
	#-------------------------------------------------------------------------------
	if(IsSet($Params[$Path[0]][$Path[1]]))
		return $Params[$Path[0]][$Path[1]];
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Key;
//return ERROR | @Trigger_Error(SPrintF('[KEY_NOT_FOUND]: Ключ "%s" не найден в массиве',$Key));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
