<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('TableID','ColumnID','RowID','Value');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#Debug(SPrintF('[comp/BooleanEdit]: TableID = %s; ColumnID = %s; RowID = %s; Value = %s',print_r($TableID,true),$ColumnID,$RowID,($Value)?'TRUE':'FALSE'));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Logic',$Value);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Data = SPrintF('{TableID:\'%s\', ColumnID:\'%s\', RowID:\'%u\'}',$TableID,$ColumnID,$RowID);
#-------------------------------------------------------------------------------
$Func = SPrintF('jQuery.post("/Administrator/API/BooleanEdit",%s); setTimeout("TableSuperReload();",100); return false;',$Data);
#-------------------------------------------------------------------------------
$A = new Tag('A',Array('href'=>'#ChangeValue','onclick' => $Func),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Links = &Links();
#-------------------------------------------------------------------------------
$LinkID = UniqID('Prompt');
#-------------------------------------------------------------------------------
$Links[$LinkID] = &$A;
#-------------------------------------------------------------------------------	                      
$Comp = Comp_Load('Form/Prompt',$LinkID,($Value)?'Выключить':'Включить');
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $A;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
