<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Field','Time');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Null($Time))
	$Time = Time();
#-------------------------------------------------------------------------------
$Links = Links();
# Коллекция ссылок
$DOM = &$Links['DOM'];
#-------------------------------------------------------------------------------
if(!Comp_IsLoaded('jQuery/DatePicker')){
  #-----------------------------------------------------------------------------
  $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{others/jQuery/ui.core.js}')));
  $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{others/jQuery/ui.datepicker.js}')));
  #$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{others/jQuery/datepicker-ru.js}')));
  #-------------------------------------------------------------------------------
  #Debug(TemplateReplace('jQuery.DatePicker',Array(),FALSE));
  $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript'),TemplateReplace('jQuery.DatePicker',Array(),FALSE)));
  #-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$UniqID = UniqID();
#-------------------------------------------------------------------------------
$DOM->AddAttribs('Body',Array('onload'=>SPrintF("$('#%s').datepicker({dateFormat:'[yy/mm/dd]',firstDay:0,showAnim:'slideDown',beforeShow:DatePickerBeforeShow,onClose:DatePickerOnClose,onSelect:function(sDate){ DatePickerSelect('%s',sDate,'%s'); },showOn:'button',duration:'fast'});",$Field,$Field,$UniqID)));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Date/Standard',$Time);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Tr = new Tag('TR',new Tag('TD',Array('id'=>$UniqID,'class'=>'Standard','style'=>'white-space:nowrap;','width'=>130),$Comp));
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'id'    => $Field,
    'name'  => $Field,
    'type'  => 'hidden',
    'value' => Date('[Y/m/d]',$Time)
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Tr->AddChild(new Tag('TD',$Comp));
#-------------------------------------------------------------------------------
return new Tag('TABLE',$Tr);
#-------------------------------------------------------------------------------

?>
