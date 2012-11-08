<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('UserID','Length');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$User = DB_Select('Users',Array('ID','Name','Email','AdminNotice','IsActive'),Array('UNIQ','ID'=>$UserID));
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Menus/List','Administrator/ListMenu/User.xml', $User);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Tr = new Tag('TR',new Tag('TD',Array('style'=>'width: 5px'),$Comp));
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Notice','Users',$User['ID'],$User['AdminNotice']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Tr->AddChild(new Tag('TD',$Comp));
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/String',$User['Email'],$Length);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Tr->AddChild(new Tag('TD',Array('style'=>SPrintF('color:#%s;',$User['IsActive']?'000000':'848484') . 'white-space: nowrap;'),$User['Email']));
    #---------------------------------------------------------------------------
    return new Tag('TABLE',Array('cellspacing'=>2,'cellpadding'=>0),$Tr);
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
