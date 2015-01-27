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
Debug(SPrintF('[comp/BooleanEdit]: TableID = %s; ColumnID = %s; RowID = %s',$TableID,$ColumnID,$RowID,$Value));
#-------------------------------------------------------------------------------

$Comp = Comp_Load('Formats/Logic',$Value);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);

return new Tag('A',Array('href'=>'/Administrator/API/BooleanEdit','onclick' => 'TableSuperReload();'),$Comp);






if(Mb_StrLen($Value) > $Length){
  #-----------------------------------------------------------------------------
  $Text = Mb_SubStr($Value,0,$Length);
  #-----------------------------------------------------------------------------
  $Result = (Is_Null($Url)?new Tag('SPAN',SPrintF('%s...',$Text)):new Tag('A',Array('href'=>$Url,'target'=>'blank'),SPrintF('%s...',$Text)));
  #-----------------------------------------------------------------------------
  $LinkID = UniqID('String');
  #-----------------------------------------------------------------------------
  $Links = &Links();
  #-----------------------------------------------------------------------------
  $Links[$LinkID] = &$Result;
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Form/Prompt',$LinkID,$Value);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  UnSet($Links[$LinkID]);
  #-----------------------------------------------------------------------------
  return $Result;
}
#-------------------------------------------------------------------------------
if(Is_Null($Url))
  return $Value;
#-------------------------------------------------------------------------------
return new Tag('A',Array('href'=>$Url,'target'=>'blank'),$Value);
#-------------------------------------------------------------------------------

?>