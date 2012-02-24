<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Attribs','Title','Icon','JavaScript');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Links = &Links();
#-------------------------------------------------------------------------------
$DOM = &$Links['DOM'];
#-------------------------------------------------------------------------------
$CacheID = SPrintF('Buttons/Standard:%s',Md5($Title));
#-------------------------------------------------------------------------------
if(!Cache_IsExists($CacheID)){
  #-----------------------------------------------------------------------------
  if(!Is_Null($JavaScript)){
    #---------------------------------------------------------------------------
    $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>SPrintF('SRC:{Js/%s}',$JavaScript)));
    #---------------------------------------------------------------------------
    $DOM->AddChild('Head',$Script);
  }
  #-----------------------------------------------------------------------------
  Cache_Add($CacheID,TRUE);
}
#-------------------------------------------------------------------------------
$Img = new Tag('IMG',Array('alt'=>$Title,'height'=>22,'width'=>22,'src'=>SPrintF('SRC:{Images/Icons/%s}',$Icon)));
#-------------------------------------------------------------------------------
$Attribs['class'] = 'Standard';
#-------------------------------------------------------------------------------
$Button = new Tag('BUTTON',$Attribs,$Img);
#-------------------------------------------------------------------------------
$LinkID = UniqID('Button');
#-------------------------------------------------------------------------------
$Links = &Links();
#-------------------------------------------------------------------------------
$Links[$LinkID] = &$Button;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Prompt',$LinkID,$Title);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
UnSet($Links[$LinkID]);
#-------------------------------------------------------------------------------
return $Button;
#-------------------------------------------------------------------------------

?>
