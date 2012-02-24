<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('CreateDate','UserID','Text','PriorityID','IsReaded');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$User = DB_Select('Users','Email',Array('UNIQ','ID'=>$UserID));
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    $Email = $UserID;
  break;
  case 'array':
    $Email = $User['Email'];
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$CreateDate = Comp_Load('Formats/Date/Extended',$CreateDate);
if(Is_Error($CreateDate))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
switch($PriorityID){
  case 'Notice':
    $Color = 'E3EAFA';
  break;
  case 'Waiting':
    $Color = 'FDF6D3';
  break;
  case 'Error':
    $Color = 'FFCCCC';
  break;
  default:
    $Color = 'F1FCCE';
}
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY',Array('class'=>'Standard','style'=>SPrintF('background-color:#%s;',$Color)),new Tag('U',$CreateDate),new Tag('CNAME',SPrintF(' [%s]',$Email)),new Tag('BR'));
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('DIV',$Text));
#-------------------------------------------------------------------------------
if(!$IsReaded){
  #-----------------------------------------------------------------------------
  $Img = new Tag('IMG',Array('width'=>16,'height'=>16,'src'=>'SRC:{Images/Icons/Event.gif}'));
  #-----------------------------------------------------------------------------
  $NoBody->AddChild($Img,TRUE);
  #-----------------------------------------------------------------------------
  $NoBody->AddAttribs(Array('style'=>'border:1px solid #FF6666;'));
}
#-------------------------------------------------------------------------------
return $NoBody;
#-------------------------------------------------------------------------------

?>
