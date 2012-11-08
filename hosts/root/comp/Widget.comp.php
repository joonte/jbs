<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('WidgetID','Title','Inner');
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Links = &Links();
# Коллекция ссылок
#-------------------------------------------------------------------------------
$DOM = &$Links['DOM'];
#-------------------------------------------------------------------------------
if(!Comp_IsLoaded('Widget')){
  #-----------------------------------------------------------------------------
  $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Widget.js}'));
  #-----------------------------------------------------------------------------
  $DOM->AddChild('Head',$Script);
}
#-------------------------------------------------------------------------------
$Table = new Tag('TABLE',Array('id'=>$WidgetID,'class'=>'Widget','cellspacing'=>5,'cellpadding'=>0));
#-------------------------------------------------------------------------------
$lPosition = 0;
$rPosition = 0;
$IsOpened = TRUE;
#-------------------------------------------------------------------------------
if(IsSet($_COOKIE[$WidgetID])){
  #-----------------------------------------------------------------------------
  $Settings = Explode(':',$_COOKIE[$WidgetID]);
  #-----------------------------------------------------------------------------
  $lPosition = (integer)Current($Settings);
  $rPosition = (integer)Next($Settings);
  #-----------------------------------------------------------------------------
  $IsOpened = (boolean)Next($Settings);
}
#-------------------------------------------------------------------------------
$Table->AddAttribs($lPosition && $rPosition?Array('style'=>SPrintF('position:absolute;left:%u;top:%u;',$lPosition,$rPosition)):Array('style'=>'display:inline-table;'));
#-------------------------------------------------------------------------------
$Move = new Tag('IMG',Array('class'=>'Button','alt'=>'Переместить','width'=>16,'height'=>16,'onmousedown'=>SPrintF("WidgetInit(event,'%s');document.body.onmouseup = function(){ document.body.onmousemove = null;document.body.onmouseup = null;WidgetRender('%s'); };document.body.onmousemove = function(event){ WidgetMove(event,'%s'); };",$WidgetID,$WidgetID,$WidgetID),'src'=>'SRC:{Images/Icons/WidgetMove.gif}'));
#-------------------------------------------------------------------------------
$Switch = new Tag('IMG',Array('class'=>'Button','alt'=>'Показать (скрыть)','width'=>16,'height'=>16,'onclick'=>SPrintF("WidgetSwitch('%s');",$WidgetID),'src'=>'SRC:{Images/Icons/WidgetSwitch.gif}'));
#-------------------------------------------------------------------------------
$Dock = new Tag('IMG',Array('class'=>'Button','alt'=>'Закрепить','width'=>16,'height'=>16,'onclick'=>SPrintF("WidgetDock('%s');",$WidgetID),'src'=>'SRC:{Images/Icons/WidgetDock.gif}'));
#-------------------------------------------------------------------------------
$Panel = new Tag('TR',Array('height'=>16),new Tag('TD',Array('width'=>16),$Move),new Tag('TD',$Title),new Tag('TD',Array('width'=>16),$Switch),new Tag('TD',Array('width'=>16,'align'=>'right'),$Dock));
#-------------------------------------------------------------------------------
$Table->AddChild($Panel);
#-------------------------------------------------------------------------------
$Table->AddChild(new Tag('TR',Array('id'=>SPrintF('%sInner',$WidgetID),'style'=>SPrintF('display:%s;',$IsOpened?'':'none')),new Tag('TD',Array('colspan'=>3),$Inner)));
#-------------------------------------------------------------------------------
return $Table;
#-------------------------------------------------------------------------------

?>
